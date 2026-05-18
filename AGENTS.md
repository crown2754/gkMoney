# AGENTS.md

## Cursor Cloud specific instructions

This is a Laravel 12 personal finance app (gkMoney) with a Traditional Chinese UI. It uses SQLite for database, sessions, cache, and queue (no external services needed).

### Tech Stack
- **Backend:** Laravel 12, PHP 8.2+, Livewire 4
- **Frontend:** Vite 7, Tailwind CSS, Alpine.js
- **Database:** SQLite (file at `database/database.sqlite`)

### Running the application

- **Dev server:** `php artisan serve --host=0.0.0.0 --port=8000`
- **Vite HMR:** `npm run dev` (runs in parallel with artisan serve)
- **Full dev stack (all-in-one):** `composer dev` (runs server, queue, logs, and vite via concurrently)

### Key commands

| Task | Command |
|------|---------|
| Lint (PHP) | `./vendor/bin/pint --test` |
| Lint fix | `./vendor/bin/pint` |
| Tests | `php artisan test` |
| Build assets | `npm run build` |
| Migrations | `php artisan migrate` |
| Fresh DB | `php artisan migrate:fresh` |

### Notes

- The app uses SQLite; no external database service (MySQL/PostgreSQL/Redis) is needed.
- `.env` is created from `.env.example` during setup. The `APP_KEY` must be generated via `php artisan key:generate`.
- The `database/database.sqlite` file must exist before running migrations. Create it with `touch database/database.sqlite` if missing.
- Mail is configured to use the `log` driver in development (check `storage/logs/laravel.log` for sent emails).
- Queue uses the `database` driver; for inline processing without a worker, set `QUEUE_CONNECTION=sync` in `.env`.
