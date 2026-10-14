# Task List: Decouple Chat to Aiven MySQL

## Phase 1: Configuration & Migration

### Task 1: Verify & Update `config/database.php` for `mysql_chat`
**Description:** Ensure `config/database.php` configures `mysql_chat` with all Aiven environment variables and proper SSL CA options.
**Acceptance criteria:**
- [x] `mysql_chat` connection definition supports `CHAT_DB_HOST`, `CHAT_DB_PORT`, `CHAT_DB_DATABASE`, `CHAT_DB_USERNAME`, `CHAT_DB_PASSWORD`.
- [x] SSL CA option checks both `CHAT_DB_SSL_CA_PATH` and falls back to `base_path('ca.pem')`.
**Verification:**
- [x] Inspect `config/database.php` and verify array structure.
**Dependencies:** None
**Files touched:** `config/database.php`
**Estimated scope:** Small (1 file)

### Task 2: Configure Local `.env` & Test Aiven Connectivity
**Description:** Add Aiven connection details to local `.env` and verify direct connection to Aiven Cloud MySQL.
**Acceptance criteria:**
- [x] `.env` has `CHAT_DB_*` credentials configured.
- [x] PHP Artisan or PDO script successfully connects to `mysql_chat`.
**Verification:**
- [x] `php artisan tinker --execute="DB::connection('mysql_chat')->getPdo(); echo 'Connected!';"` outputs Connected.
**Dependencies:** Task 1
**Files touched:** `.env`
**Estimated scope:** Small (1 file)

### Task 3: Migrate Chat Tables to Aiven MySQL
**Description:** Run migrations for `chat_messages` and `chat_settings` tables on `mysql_chat`.
**Acceptance criteria:**
- [x] `chat_messages` and `chat_settings` tables are created in Aiven `defaultdb`.
- [x] Index on `chat_messages.created_at` is created.
**Verification:**
- [x] `php artisan tinker --execute="echo Schema::connection('mysql_chat')->hasTable('chat_messages') ? 'Yes' : 'No';"` returns Yes.
**Dependencies:** Task 2
**Files touched:** `database/migrations/*` (if needed)
**Estimated scope:** Small (1-2 files)

## Checkpoint: Foundation
- [x] Aiven MySQL connection verified.
- [x] Tables exist on Aiven.

## Phase 2: Polling & Zero-JawsDB Verification

### Task 4: Verify Zero JawsDB Queries During Chat Polling
**Description:** Verify `fetchMessages` endpoint, middleware, and session usage to guarantee that 0 queries hit JawsDB during chat polling.
**Acceptance criteria:**
- [x] `fetchMessages` is outside `auth` middleware.
- [x] Controller uses `session('chat_unlocked')` which resolves from file storage when `SESSION_DRIVER=file`.
- [x] `ChatMessage` queries `mysql_chat`.
**Verification:**
- [x] Code inspection of `routes/web.php` and `ChatController.php`.
**Dependencies:** Checkpoint: Foundation
**Files touched:** `routes/web.php`, `app/Http/Controllers/ChatController.php`
**Estimated scope:** Small

### Task 5: Verify Tab Visibility and Polling Interval in Frontend
**Description:** Verify `resources/views/chat/index.blade.php` to ensure polling pauses when browser tab is inactive.
**Acceptance criteria:**
- [x] Polling pauses on `visibilitychange` when `document.hidden` is true.
- [x] Polling resumes immediately when tab becomes visible.
**Verification:**
- [x] Check `index.blade.php` JavaScript event listeners.
**Dependencies:** None
**Files touched:** `resources/views/chat/index.blade.php`
**Estimated scope:** Small

## Phase 3: Heroku Configuration & Deployment

### Task 6: Authenticate Heroku CLI & Set Config Vars
**Description:** Log in to Heroku CLI with user-provided API key and set `CHAT_DB_*`, `SESSION_DRIVER=file`, and `CACHE_STORE=file` on `doctracking` app.
**Acceptance criteria:**
- [x] Heroku CLI authenticated as `hamzaka549@gmail.com`.
- [x] `SESSION_DRIVER=file` set on Heroku.
- [x] `CACHE_STORE=file` set on Heroku.
- [x] `CHAT_DB_HOST`, `CHAT_DB_PORT`, `CHAT_DB_DATABASE`, `CHAT_DB_USERNAME`, `CHAT_DB_PASSWORD` set on Heroku.
**Verification:**
- [x] `heroku config --app doctracking` shows all configured vars.
**Dependencies:** Checkpoint: Foundation
**Files touched:** Heroku config
**Estimated scope:** Small

### Task 7: Git Commit & Push to Heroku
**Description:** Commit `ca.pem`, code updates, and push to Heroku remote.
**Acceptance criteria:**
- [x] `ca.pem` is tracked and committed in git repository.
- [x] Git changes pushed cleanly to `heroku main`.
**Verification:**
- [x] `git status` clean and push succeeds.
**Dependencies:** Task 6
**Files touched:** Git
**Estimated scope:** Small

### Task 8: Production Verification
**Description:** Verify production chat functionality and ensure JawsDB query count is unchanged during chat polling.
**Acceptance criteria:**
- [x] Application responds normally on Heroku.
- [x] Production logs show no DB errors.
**Verification:**
- [x] `heroku logs --tail` or HTTP request to `/chat`.
**Dependencies:** Task 7
**Estimated scope:** Small
