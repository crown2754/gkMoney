# Add Daily Expense Feature Specification  

**Product:** Accounting (Fintech) Mobile/Web App  
**Feature:** Add Daily Expense – allows users to record a personal or business expense for the current day (or a past date) with minimal friction.  
**Owner:** Product Manager  
**Target Release:** Q4 2025  

---  

## 1. Overview  

Users often need to capture expenses on the go. This feature provides a fast, guided flow to create an expense entry, attach receipts, categorize, and optionally split or tag the transaction. The entry is persisted to the backend and immediately reflected in reports, budgets, and dashboards.  

---  

## 2. Goals  

| Goal | Success Metric |
|------|----------------|
| Reduce time to log an expense to ≤ 15 seconds on average | Avg. time measured via analytics |
| Capture > 90 % of daily expenses for active users | Ratio of expenses logged vs. bank‑imported transactions |
| Maintain data integrity (no duplicate or orphaned entries) | Zero data‑loss incidents in QA |
| Enable offline entry with sync when back online | Sync success rate ≥ 98 % |
| Provide clear validation & error messaging | < 2 % error‑retry rate |

---  

## 3. Non‑Goals  

- Bulk import of expenses (CSV/OFX) – covered by separate feature.  
- Recurring expense scheduling – out of scope for v1.  
- Multi‑currency conversion at entry time – will use stored base currency; conversion shown later in reports.  
- Advanced receipt OCR – placeholder for future enhancement.  

---  

## 4. Data Model  

### 4.1 Tables  

| Table | Description |
|-------|-------------|
| `expenses` | Core expense record. |
| `expense_categories` | Hierarchical category lookup (e.g., Food → Groceries). |
| `expense_tags` | User‑defined tags (many‑to‑many). |
| `expense_receipts` | Binary receipt storage (reference to object store). |
| `expense_splits` | For splitting an expense across categories/tags or users. |
| `users` | Existing user table (FK). |
| `accounts` | Existing account (wallet/bank) table (FK). |

### 4.2 Schema Details  

#### `expenses`  

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PK, NOT NULL, DEFAULT `gen_random_uuid()` | Unique identifier |
| `user_id` | UUID | FK → `users.id`, NOT NULL | Owner |
| `account_id` | UUID | FK → `accounts.id`, NULLABLE | Source account (cash, card, etc.) |
| `amount_cents` | BIGINT | NOT NULL, CHECK (>0) | Stored in smallest currency unit |
| `currency` | CHAR(3) | NOT NULL, DEFAULT user’s base currency (ISO 4217) | e.g., `USD` |
| `expense_date` | DATE | NOT NULL | Date of expense (defaults to today) |
| `description` | TEXT | NULLABLE | Free‑form memo |
| `category_id` | UUID | FK → `expense_categories.id`, NULLABLE | Primary category |
| `is_recurring` | BOOLEAN | NOT NULL, DEFAULT false | Flag for future use |
| `created_at` | TIMESTAMPTZ | NOT NULL, DEFAULT `now()` | Audit |
| `updated_at` | TIMESTAMPTZ | NOT NULL, DEFAULT `now()` | Audit |
| `deleted_at` | TIMESTAMPTZ | NULLABLE | Soft delete |
| `receipt_id` | UUID | FK → `expense_receipts.id`, NULLABLE | Linked receipt |
| `external_id` | VARCHAR(64) | NULLABLE | ID from bank import for deduplication |
| `metadata` | JSONB | NULLABLE | Extensible key‑value (e.g., travel mode) |

#### `expense_categories`  

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | UUID | PK |
| `parent_id` | UUID | FK → same table (self‑ref), NULLABLE |
| `name` | VARCHAR(100) | NOT NULL |
| `icon` | VARCHAR(50) | NULLABLE (e.g., FontAwesome class) |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true |
| `sort_order` | INTEGER | NOT NULL, DEFAULT 0 |

#### `expense_tags`  

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | UUID | PK |
| `user_id` | UUID | FK → `users.id`, NOT NULL |
| `label` | VARCHAR(50) | NOT NULL |
| `color` | CHAR(7) | NOT NULL (hex) |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true |

#### `expense_receipts`  

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | UUID | PK |
| `storage_key` | VARCHAR(255) | NOT NULL (reference to S3/GCS) |
| `mime_type` | VARCHAR(100) | NOT NULL |
| `size_bytes` | BIGINT | NOT NULL |
| `uploaded_at` | TIMESTAMPTZ | NOT NULL, DEFAULT `now()` |

#### `expense_splits` (optional, for future)  

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | UUID | PK |
| `expense_id` | UUID | FK → `expenses.id`, NOT NULL |
| `category_id` | UUID | FK → `expense_categories.id`, NULLABLE |
| `tag_id` | UUID | FK → `expense_tags.id`, NULLABLE |
| `amount_cents` | BIGINT | NOT NULL |
| `note` | TEXT | NULLABLE |

### 4.3 Indexes  

- `expenses(user_id, expense_date DESC)` – fast daily list.  
- `expenses(account_id)` – for account‑wise filtering.  
- `expenses(category_id)` – category reports.  
- `expense_receipts(expense_id)` – lookup receipt.  
- `expense_tags(user_id, label)` – unique tag per user.  

### 4.4 Constraints & Validation  

- `amount_cents` must be > 0.  
- `expense_date` cannot be > today + 7 days (future‑lookup limited to a week for planning).  
- If `receipt_id` is set, the referenced receipt must belong to the same user.  
- Soft‑delete: queries must filter `deleted_at IS NULL`.  

---  

## 5. API Specification  

All endpoints require OAuth2 Bearer token (`user_id` derived from token).  

### 5.1 Create Expense  

**POST** `/api/v1/expenses`  

**Request Body**  

```json
{
  "account_id": "uuid",               // optional
  "amount_cents": 1250,               // required
  "currency": "USD",                  // optional, defaults to user base
  "expense_date": "2025-09-16",       // ISO‑8601 date, optional (today)
  "description": "Lunch at cafe",    // optional
  "category_id": "uuid",              // optional
  "tags": ["uuid1", "uuid2"],         // optional array of tag IDs
  "receipt_id": "uuid",               // optional (pre‑uploaded)
  "metadata": { "travel_mode": "car"} // optional
}
```

**Responses**  

- `201 Created` – returns created expense object (same shape as GET).  
- `400 Bad Request` – validation errors (field‑specific).  
- `401 Unauthorized` – missing/invalid token.  
- `403 Forbidden` – user does not own referenced account/tag/category.  
- `409 Conflict` – duplicate `external_id` detected.  

### 5.2 Get Expense Detail  

**GET** `/api/v1/expenses/{expense_id}`  

Returns full expense with nested category, tags, and receipt URL (signed, short‑lived).  

### 5.3 List Today’s Expenses  

**GET** `/api/v1/expenses?date=2025-09-16&limit=20&offset=0`  

- `date` optional (defaults to today).  
- Supports `search` query on `description`.  

### 5.4 Upload Receipt (multipart/form‑data)  

**POST** `/api/v1/receipts`  

- Returns `receipt_id` and signed URL for direct upload (or stores directly).  

### 5.5 Error Schema  

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Amount must be greater than zero",
    "details": [
      { "field": "amount_cents", "issue": "must_be_positive" }
    ]
  }
}
```

---  

## 6. User Interface (UI)  

### 6.1 Platforms  

- Mobile (iOS/Android) – primary.  
- Web (responsive) – secondary, mirrors mobile flow.  

### 6.2 Screen Flow (Wireframe Description)  

| Screen | Key Elements | Interaction |
|--------|--------------|-------------|
| **Home / Dashboard** | Floating Action Button (FAB) “+” labeled **Add Expense** | Tap → **Expense Entry** |
| **Expense Entry** | 1. Amount field (numeric keypad, currency symbol prefixed) <br>2. Date picker (inline, defaults to today, shows calendar) <br>3. Category selector (searchable dropdown with icons) <br>4. Description text field (optional, max 200 chars) <br>5. Tags chip input (type‑ahead, allows multiple) <br>6. Account selector (if multiple accounts) <br>7. Attach receipt button (camera/gallery) <br>8. Cancel / Save buttons (top‑left / top‑right) | - Amount: real‑time validation (show error if empty or zero) <br>- Date: tapping opens modal; selecting > today+7 days shows warning but allows (with confirmation) <br>- Category: shows recent/frequent first <br>- Tags: chips display selected tags; tapping chip removes <br>- Receipt: opens media picker; after selection shows thumbnail with “Remove” overlay; uploads in background, shows progress toast <br>- Save: disabled until amount valid; on tap shows loading spinner, then either success toast + return to Home or error inline |
| **Success Toast** | “Expense added” + undo snackbar (5 s) | Undo → calls DELETE expense (soft delete) |
| **Error Inline** | Red border & message under offending field | User can correct and re‑submit |
| **Receipt Preview (optional)** | Small thumbnail in entry screen, tap to preview full‑size | – |
| **Settings → Categories / Tags** (out of scope) | Manage master lists | – |

### 6.3 UI Components & Style  

- **Typography:** Body 14 pt, Heading 16 pt, Amount 24 pt (bold).  
- **Colors:** Primary brand blue for FAB, success green, error red, neutral gray for fields.  
- **Accessibility:** Minimum touch target 48 dp, label‑for‑input association, voice‑over hints, dynamic type support.  
- **Offline:** All fields stored locally (SQLite/Realm); Save queues entry for background sync. Receipt uploads are queued and retried with exponential backoff.  

### 6.4 Mockup (ASCII) – Mobile Portrait  

```
+-----------------------------------+
|  <  Add Expense   [Save]          |
+-----------------------------------+
|  Amount:   [$]  ___________       |
|              [ 1,250 ]            |
+-----------------------------------+
|  Date:   [Today]  ▼               |
|              (Sep 16, 2025)       |
+-----------------------------------+
|  Category:  [Food ▼]              |
|              (groceries)          |
+-----------------------------------+
|  Description: ___________________ |
|              Lunch at cafe       |
+-----------------------------------+
|  Tags:   [ #work #travel ]       |
|              + Add tag            |
+-----------------------------------+
|  Account:  [Checking ▼]           |
+-----------------------------------+
|  [Attach Receipt]  [camera icon]  |
|              [ thumbnail ]        |
+-----------------------------------+
```

---  

## 7. User Flow (Step‑by‑Step)  

1. **Entry Point** – User taps FAB “Add Expense” on Home screen.  
2. **Amount Input** – User enters numeric value; keyboard shows currency symbol; validation ensures > 0.  
3. **Date Selection** – Optional; defaults to today. If user picks a future date beyond +7 days, a confirmation modal appears (“Are you sure this expense is for a future date?”).  
4. **Category Selection** – User taps category field → searchable list with icons; recent/frequent categories surface first.  
5. **Description (Optional)** – Free‑form text field; character counter shown.  
6. **Tags (Optional)** – User types; matching tags appear; selecting adds a chip. New tags can be created via “Create new tag” (opens modal, stores in `expense_tags`).  
7. **Account Selection (if multiple)** – Dropdown of user’s accounts (cash, credit cards, wallets).  
8. **Attach Receipt** – Tap button → media picker → user selects image or takes photo → thumbnail appears; upload begins in background; progress shown via small bar on thumbnail.  
9. **Save** – When all validations pass, Save button becomes enabled. Tap →  
   - Show full‑screen spinner.  
   - Persist expense locally (SQLite/Realm).  
   - Queue receipt upload (if any).  
   - Send POST `/expenses` to backend.  
   - On 201:  
     - Remove from local queue.  
     - Show success toast with undo.  
     - Return to Home; expense appears in list (optimistically updated).  
   - On error:  
     - Show inline error (field‑specific) or toast for network issues.  
     - Keep entry in local queue for retry.  
10. **Undo** – If user taps undo within 5 s, send DELETE `/expenses/{id}` (soft delete) and remove from UI.  
11. **Sync** – Background service periodically retries failed uploads/expense posts; when online, updates UI with server timestamps.  

---  

## 8. Validation & Error Handling  

| Validation | Where | Message |
|------------|-------|---------|
| Amount > 0 | Client (real‑time) + Server | “Amount must be greater than zero” |
| Currency ISO‑4217 | Client dropdown + Server | “Invalid currency” |
| Date not > today+7 (without confirmation) | Client (warning) + Server (reject if > today+30) | “Date is too far in the future” |
| Category exists & active | Server FK | “Invalid category” |
| Tag belongs to user | Server FK | “Tag not found or not accessible” |
| Account belongs to user (if provided) | Server FK | “Account not accessible” |
| Receipt file size ≤ 10 MB | Client + Server | “Receipt too large (max 10 MB)” |
| Receipt MIME type in allowed list (image/*, application/pdf) | Server | “Unsupported file type” |
| Duplicate external_id (if provided) | Server unique constraint | “Expense already imported” |

All errors returned as per API error schema; client displays field‑specific messages where possible, otherwise a generic toast.

---  

## 9. Security & Privacy  

- **Authentication:** OAuth2 JWT with short‑lived access token + refresh token.  
- **Authorization:** Every request checks `user_id` against resource ownership (expense, account, tag, category, receipt).  
- **Data at Rest:** Expense data encrypted with AES‑256 per‑user key stored in keystore; receipts stored encrypted in object store (S3 SSE‑KMS).  
- **Data in Transit:** TLS 1.2+.  
- **Privacy:** No PII stored beyond what user inputs; receipts are only accessible to the owner unless explicitly shared (future feature).  
- **Audit:** `created_at`, `updated_at`, `deleted_at` fields retained for 2 years for compliance.  

---  

## 10. Analytics & Telemetry  

| Event | Properties |
|-------|------------|
| `expense_added` | `amount_cents`, `currency`, `category_id`, `tags_count`, `has_receipt`, `expense_date_offset` (days from today) |
| `expense_added_error` | `error_code`, `field` |
| `receipt_uploaded` | `size_bytes`, `mime_type`, `upload_time_ms` |
| `expense_undo` | `expense_id` |
| `sync_success` / `sync_failure` | `retry_count`, `latency_ms` |

Events sent to analytics endpoint (batch every 5 min or on app background). Used to measure funnel completion, time‑to‑save, and error rates.

---  

## 11. Open Questions / Decisions Needed  

1. **Maximum look‑ahead date for expenses** – currently +7 days with warning; confirm with finance team.  
2. **Tag creation UI** – allow inline creation vs. redirect to settings.  
3. **Receipt storage policy** – retention period (e.g., 2 years) and deletion flow.  
4. **Currency handling for multi‑currency users** – store amount in user’s base currency; need conversion rate service for reporting.  
5. **Offline conflict resolution** – “last write wins” vs. merge strategy; decide based on product risk.  

---  

## 12. Acceptance Criteria  

- [ ] User can add an expense with amount, date, category, optional description, tags, account, and receipt in ≤ 15 seconds.  
- [ ] Entry appears instantly in the daily list (optimistic UI) and persists after successful sync.  
- [ ] Validation errors are shown inline; saving is disabled until all required fields are valid.  
- [ ] Receipt uploads succeed ≥ 95 % of the time on 3G/4G networks; failures are retryable with exponential backoff.  
- [ ] Undo snackbar restores state and removes expense from server and local DB.  
- [ ] No crash or data loss when device loses connectivity mid‑flow.  
- [ ] All API responses conform to the defined schemas; error messages are actionable.  
- [ ] Analytics events fire as specified and are visible in the dashboard.  

---  

## 13. Timeline (High‑Level)  

| Sprint | Goal |
|--------|------|
| 1 (2 weeks) | API endpoints, DB migrations, unit tests |
| 2 (2 weeks) | Mobile UI screens, local persistence, validation |
| 3 (2 weeks) | Receipt upload flow, offline queue, sync logic |
| 4 (1 week) | Undo, analytics integration, QA & bug bash |
| 5 (1 week) | Beta rollout, feedback incorporation, release prep |

---  

**Prepared by:**  
[Your Name], Product Manager – FinTech Accounting App  
**Date:** 2025‑09‑16  

---  

*End of Specification*