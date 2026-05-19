# Add Daily Expense Feature Specification  
**Product:** Accounting App (FinTech)  
**Feature:** Add a daily expense entry  
**Owner:** Product Manager – *[Your Name]*  
**Date:** 2025‑11‑03  

---  

## 1. Overview  
Users need a quick way to record daily expenses (e.g., meals, transport, office supplies) so that their cash‑flow reports stay up‑to‑date without navigating through multiple screens.  

This spec defines the data model, UI screens, and end‑to‑end user flow for the **Add Daily Expense** flow, covering creation, validation, persistence, and confirmation.  

---  

## 2. Goals & Success Metrics  

| Goal | Success Indicator |
|------|-------------------|
| **Speed** – enable entry in ≤ 15 seconds from tap‑to‑save | Avg. time to complete flow measured via analytics |
| **Accuracy** – minimize data entry errors | < 2 % of saved entries flagged for correction within 24 h |
| **Adoption** – increase daily expense entries per active user | +20 % week‑over‑week after release |
| **Reliability** – zero data loss on sync failures | No orphaned drafts after app restart or network loss |

---  

## 3. Non‑Goals  

- Bulk import of expenses (CSV/OCR) – to be handled in a separate “Import” feature.  
- Recurring expense scheduling – out of scope for v1.  
- Multi‑currency conversion – amounts are stored in the user’s base currency; conversion UI is handled elsewhere.  

---  

## 4. Data Model  

### 4.1 Table: `expenses`  

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID (PK) | NOT NULL, DEFAULT `gen_random_uuid()` | Unique identifier |
| `user_id` | UUID (FK → users.id) | NOT NULL, INDEX | Owner of the expense |
| `date` | DATE | NOT NULL, DEFAULT CURRENT_DATE | Calendar date of the expense (user‑selected) |
| `amount_cents` | BIGINT | NOT NULL, CHECK (`amount_cents` > 0) | Monetary value in smallest currency unit (e.g., cents) |
| `currency` | CHAR(3) | NOT NULL, DEFAULT user’s base currency (ISO‑4217) | Currency code |
| `category_id` | UUID (FK → expense_categories.id) | NOT NULL, INDEX | Selected category |
| `description` | TEXT | NULLABLE, max 250 chars | Free‑form note |
| `receipt_image_url` | TEXT | NULLABLE | URL to uploaded receipt (if any) |
| `created_at` | TIMESTAMPTZ | NOT NULL, DEFAULT now() | Timestamp when record was first saved |
| `updated_at` | TIMESTAMPTZ | NOT NULL, DEFAULT now() | Timestamp of last edit |
| `is_deleted` | BOOLEAN | NOT NULL, DEFAULT false | Soft‑delete flag |

### 4.2 Table: `expense_categories` (reference)  

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID (PK) | NOT NULL, DEFAULT `gen_random_uuid()` | |
| `user_id` | UUID (FK → users.id) | NOT NULL | Categories are user‑specific (allow custom categories) |
| `name` | VARCHAR(50) | NOT NULL | e.g., “Meals”, “Transport” |
| `icon` | VARCHAR(30) | NULLABLE | Identifier for UI icon (e.g., “fa-utensils”) |
| `color` | CHAR(7) | NULLABLE | Hex colour for UI highlight |
| `is_system` | BOOLEAN | NOT NULL, DEFAULT false | Pre‑defined categories shipped with the app |
| `created_at` | TIMESTAMPTZ | NOT NULL, DEFAULT now() | |
| `updated_at` | TIMESTAMPTZ | NOT NULL, DEFAULT now() | |

### 4.3 Indexes  

- `expenses(user_id, date DESC)` – for quick daily lists.  
- `expenses(category_id)` – for category‑based reporting.  

---  

## 5. UI Specification  

### 5.1 Screens  

| Screen | Purpose | Key Elements |
|--------|---------|--------------|
| **Expense Entry Form** (modal or full‑screen) | Capture a single expense | - Date picker (defaults to today) <br> - Amount field (numeric keyboard, currency symbol) <br> - Category selector (searchable list with icons) <br> - Description field (optional, multiline, max 250) <br> - Attach receipt button (camera/gallery) <br> - Cancel & Save buttons |
| **Success Toast** | Confirmation after save | Brief message: “Expense saved”, undo option for 5 s |
| **Error Inline** | Validation feedback | Red highlight under offending field with concise message |
| **Expense List (Daily)** – *existing screen* | Show today’s expenses; new entry appears at top | Swipe‑to‑edit/delete, total sum at bottom |

### 5.2 Component Details  

| Component | Type | Props / State | Validation |
|-----------|------|---------------|------------|
| DatePicker | Native date picker (or custom calendar) | `selectedDate: Date` | Must be ≤ today + 1 (allow future‑dated entries for upcoming expenses) |
| AmountField | TextInput (numeric) | `amount: string` | > 0, max 9,999,999.99 (configurable) |
| CategorySelector | Searchable dropdown | `selectedCategoryId: UUID` | Required; shows icons & names |
| DescriptionField | TextInput (multiline) | `description: string` | Max 250 chars; trim whitespace |
| ReceiptUploader | Button + preview | `imageUrl?: string` | Accepts JPEG/PNG ≤ 5 MB; shows thumbnail |
| SaveButton | Button | `isEnabled: boolean` | Enabled only when all required fields pass validation |
| CancelButton | Button | – | Returns to previous screen without saving |

### 5.3 Interaction & Feedback  

- **Live validation**: As user types, field‑level errors appear instantly.  
- **Amount formatting**: Auto‑insert commas/currency symbol while preserving cents internally.  
- **Category search**: Debounced 300 ms; shows recent/frequent categories first.  
- **Receipt upload**: After picking image, compress to ≤ 800 px width, upload to storage service, store URL. Show upload progress spinner.  
- **Undo**: Toast appears for 5 s with “Undo” action; tapping deletes the just‑saved expense (soft‑delete flag set).  

---  

## 6. User Flow  

```mermaid
flowchart TD
    A[Open Add Expense (FAB or + button)] --> B[Expense Entry Form]
    B --> C1{Date Picker}
    C1 -->|Select Date| B
    B --> C2{Amount Field}
    C2 -->|Enter Amount| B
    B --> C3{Category Selector}
    C3 -->|Choose Category| B
    B --> C4{Description (optional)}
    C4 -->|Enter Text| B
    B --> C5{Attach Receipt (optional)}
    C5 -->|Pick/Capture Image| B
    B --> C6{Validate All Fields}
    C6 -->|Invalid| D[Show Inline Errors]
    D --> B
    C6 -->|Valid| E[Disable Form, Show Spinner]
    E --> F[Call API POST /expenses]
    F -->|201 Created| G[Save Success Toast + Undo]
    G --> H[Close Form, Refresh Daily List]
    F -->|4xx/5xx| I[Show Error Toast]
    I --> B
```

### Step‑by‑Step Narrative  

1. **Entry Point** – User taps the **FAB** (`+`) on the home/dashboard or navigates via **Menu → Add Expense**.  
2. **Form Load** – Date defaults to today; amount field empty; category selector shows last used category; description blank; receipt preview empty.  
3. **Data Entry** – User optionally changes date, enters amount (numeric keyboard), selects a category (type‑ahead search), adds a description, and/or attaches a receipt.  
4. **Validation** – On each blur/change, client‑side validation runs:  
   - Amount > 0 and within limits.  
   - Category selected.  
   - Date not beyond allowed range (today + 1).  
   - Description length ≤ 250.  
   - Receipt size/type valid.  
   Errors appear inline; Save button stays disabled until all pass.  
5. **Submit** – When all fields are valid, user taps **Save**:  
   - Form disables, spinner shows.  
   - Client sends `POST /api/v1/expenses` with JSON payload (see §7).  
6. **Server Response** –  
   - **201 Created**: Returns the newly created expense object (including server‑generated `id`, `created_at`).  
   - Client shows **Success Toast** (“Expense saved”) with an **Undo** action for 5 s.  
   - Form closes; user returns to the **Daily Expense List**, which updates via optimistic update (new entry inserted at top) or a quick refresh.  
   - **Undo** triggers a `DELETE /expenses/{id}` (soft‑delete) and removes the toast.  
   - **Error (4xx/5xx)**: Shows a toast with a generic message (“Failed to save expense”) and keeps the form open for correction.  

### Edge Cases  

| Situation | Handling |
|-----------|----------|
| **Network loss before response** | Save request is queued (background sync) with a local optimistic entry marked `is_synced = false`. User sees a “Saving…” banner; when connectivity returns, the request retries. |
| **Duplicate rapid taps** | Save button disables after first tap; prevents duplicate submissions. |
| **User changes date to past/future beyond allowed range** | Inline error: “Date must be today or tomorrow”. |
| **Receipt upload fails** | Show error under receipt preview; user can retry or proceed without receipt. |
| **Category deleted after selection** | If category becomes unavailable (e.g., admin removed), show error: “Selected category no longer available; please choose another”. |
| **Amount exceeds max allowed** | Error: “Amount exceeds limit of $9,999,999.99”. |

---  

## 7. API Contract  

### 7.1 Endpoint  

`POST /api/v1/expenses`  

### 7.2 Request  

```json
{
  "date": "2025-11-03",
  "amount_cents": 1250,
  "currency": "USD",
  "category_id": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
  "description": "Lunch at cafe",
  "receipt_image_url": null   // optional
}
```

*All fields are required except `description` and `receipt_image_url`.*

### 7.3 Response (201 Created)  

```json
{
  "id": "a1b2c3d4-5678-90ab-cdef-1234567890ab",
  "user_id": "9f86d081-884c-4f5a-b96c-9655b0285e2c",
  "date": "2025-11-03",
  "amount_cents": 1250,
  "currency": "USD",
  "category_id": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
  "description": "Lunch at cafe",
  "receipt_image_url": null,
  "created_at": "2025-11-03T08:15:22Z",
  "updated_at": "2025-11-03T08:15:22Z",
  "is_deleted": false
}
```

### 7.4 Error Responses  

| Status | Body Example | Meaning |
|--------|--------------|---------|
| 400 | `{ "error": "VALIDATION_FAILED", "details": [{ "field": "amount_cents", "message": "must be greater than 0" }] }` | Validation error |
| 401 | `{ "error": "UNAUTHORIZED" }` | Missing/invalid auth token |
| 403 | `{ "error": "FORBIDDEN", "message": "Category not owned by user" }` | Attempt to use another user's category |
| 422 | `{ "error": "RECEIPT_TOO_LARGE", "message": "Image exceeds 5 MB limit" }` | Receipt upload failure |
| 500 | `{ "error": "INTERNAL_SERVER_ERROR" }` | Unexpected server error |

---  

## 8. Acceptance Criteria  

| # | Criteria | Test Method |
|---|----------|--------------|
| AC1 | User can create an expense with mandatory fields (date, amount, category) and see it appear in the daily list within 2 seconds of save. | Manual QA + automation (Espresso/XCUITest). |
| AC2 | Validation errors appear inline and prevent saving until corrected. | Unit tests on form validation; UI test with invalid input. |
| AC3 | Saved expense persists after app restart and is reflected in reports. | Save, kill app, relaunch, verify entry present. |
| AC4 | Receipt attachment works: image uploads, preview shows, and URL stored. | Mock storage service; verify URL in DB. |
| AC5 | Undo toast removes the expense (soft‑delete) and restores UI state. | Trigger undo, verify `is_deleted = true` and entry removed from list. |
| AC6 | Network‑loss scenario: expense is saved locally and synced when connectivity returns. | Disable Wi‑Fi, submit, enable Wi‑Fi, verify remote record. |
| AC7 | Performance: time from tapping FAB to seeing success toast ≤ 15 seconds on median device (Android 10 / iOS 14). | Performance test with profiling tools. |
| AC8 | No duplicate entries are created on rapid double‑tap. | Stress test tapping Save quickly; assert single DB row. |
| AC9 | Accessibility: all inputs have proper labels, contrast ≥ 4.5:1, and flow is navigable via TalkBack/VoiceOver. | Accessibility audit (axe, manual). |
| AC10 | Analytics: event `expense_added` fires with correct properties (amount, category_id, has_receipt). | Instrumentation test. |

---  

## 9. Open Questions & Decisions  

| Question | Options | Recommendation |
|----------|---------|----------------|
| Should we allow **future‑dated** expenses beyond tomorrow? | - No (only today/tomorrow) <br> - Yes (up to 30 days) | Start with today/tomorrow to keep UI simple; extend later via feature flag. |
| Where to store receipts? | - Cloud storage (S3/GCS) with public‑read URL <br> - Encrypted blob in DB | Use cloud storage for scalability; store signed URL with short expiry for privacy. |
| Should category selection be hierarchical (parent/child)? | - Flat list <br> - Two‑level hierarchy | Flat list for v1; hierarchy can be added in v2 as a separate “Category Management” feature. |
| Do we need **recurring expense** toggle in this screen? | - Yes (adds complexity) <br> - No (defer) | Defer to a dedicated “Recurring Expenses” screen to keep the add flow fast. |
| Should we support **split** expenses (multiple categories)? | - No (single category per entry) <br> - Yes (allow splitting) | No for MVP; splitting can be handled via “Add another entry” or a future “Split Expense” flow. |

---  

## 10. Implementation Notes  

- **Frontend**: Use React Native (or native Swift/Kotlin) with Formik/Yup or React Hook Form for validation.  
- **State Management**: Redux Toolkit or React Query for optimistic updates and offline queue.  
- **Backend**: Node.js/Express or Go/Gin; validation via Joi or custom middleware; DB layer using Prisma/TypeORM with PostgreSQL.  
- **Security**: JWT auth; ensure `user_id` is enforced server‑side; receipt URLs should be time‑limited signed URLs.  
- **Testing**: Unit tests for validation logic, integration tests for API contract, e2e tests for the full flow (Detox).  

---  

### End of Specification  

*Prepared by:* **[Your Name]**, Product Manager – FinTech Accounting App  
*Date:* 2025‑11‑03  

---  



*Feel free to copy this markdown into your Confluence/Wiki or PRD tool for further refinement.*