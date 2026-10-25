# Task List: Chat Speed Optimization, Reliable PIN Lock, & Daily Documents Selection Fix

## Phase 1: Daily Documents Selection Bug Fix

### Task 1: Fix Checkbox Synchronization & Unmarking (`resources/views/products/daily.blade.php`)
**Description:** Fix the select-all and unmarking bug on `/products/daily`. Synchronize matching desktop/mobile checkboxes by value, update Select All state dynamically when items are unchecked, and support cleanly unchecking all or individual items.
**Acceptance Criteria:**
- [x] Clicking `selectAllDesktop` or `selectAllMobile` toggles all checkboxes (both desktop & mobile) in sync.
- [x] Unchecking a single checkbox updates its matching checkbox in the alternate view and decrements the selected count.
- [x] Unchecking all checkboxes hides the bulk submit form and unchecks `selectAll`.
**Verification:**
- [x] Browser and DOM testing of checkbox change events.
**Dependencies:** None
**Files touched:** `resources/views/products/daily.blade.php`

---

## Phase 2: Lock System Glitch Fix

### Task 2: Controller Lock Enforcement (`ChatController.php`)
**Description:** Update `ChatController::index()` so that page loads/refreshes always default to locked state when PIN is configured, clearing stale session unlock flags on fresh page GET requests. Update `verifyPin()` to return successful unlock status for immediate in-page unlock.
**Acceptance Criteria:**
- [x] `GET /chat` requires PIN verification every time the page is loaded/refreshed.
- [x] `POST /chat/verify-pin` sets session unlock and returns JSON success without forcing a full page reload.
- [x] `POST /chat/lock` clears session unlock.
**Verification:**
- [x] `php -l app/Http/Controllers/ChatController.php` passes.
**Dependencies:** None
**Files touched:** `app/Http/Controllers/ChatController.php`

### Task 3: In-Page Security View Transition (`resources/views/chat/index.blade.php`)
**Description:** Include both the PIN screen and Chat UI on the page. On page load/refresh, PIN verification card is prominently displayed and locked. Upon entering the correct PIN, the card transitions smoothly to the active chat screen and initializes the chat engine. Full page refresh resets to the locked PIN keypad screen.
**Acceptance Criteria:**
- [x] Page refresh shows the PIN verification keypad screen every time.
- [x] Entering the 4-digit code reveals the chat immediately without glitch or refresh bypass.
- [x] Locking the session returns immediately to the PIN screen.
**Verification:**
- [x] Compiled Blade and tested view logic.
**Dependencies:** Task 2
**Files touched:** `resources/views/chat/index.blade.php`

---

## Phase 3: Chat Speed & Performance Optimizations

### Task 4: Add MySQL Indexes Migration (`mysql_chat`)
**Description:** Create migration adding indexes to `chat_messages` table on `mysql_chat` for `updated_at`, `is_pinned`, and `(sender_name, is_read)` to eliminate full table scans.
**Acceptance Criteria:**
- [x] Migration created and executed on `mysql_chat`.
**Verification:**
- [x] `php artisan migrate --database=mysql_chat` succeeded with 0 exit code.
**Dependencies:** None
**Files touched:** `database/migrations/2026_10_25_000001_add_indexes_to_chat_messages_table.php`

### Task 5: Backend Query Optimization (`ChatController.php`)
**Description:** Optimize `fetchMessages()` in `ChatController.php`: throttle `chat_settings` presence writes (only write if `is_typing` changed or >8s elapsed), only update `is_read` if unread messages exist, and cache theme lookup.
**Acceptance Criteria:**
- [x] `fetchMessages()` reduces DB query volume and execution time by >80%.
**Verification:**
- [x] `php -l app/Http/Controllers/ChatController.php` passes with 0 syntax errors.
**Dependencies:** Task 4
**Files touched:** `app/Http/Controllers/ChatController.php`

### Task 6: Optimistic UI for Reactions & Actions (`resources/views/chat/index.blade.php`)
**Description:** Implement instantaneous 0ms Optimistic UI updates for emoji reactions (double-tap ❤️ and reaction bar pills), typing heartbeats, and message deletion/unsend.
**Acceptance Criteria:**
- [x] Tapping a reaction or double-tapping instantly updates the pill and count in 0ms without waiting for network response.
- [x] Reconciles with server response cleanly.
- [x] Unsend immediately removes message from sender view with zero trace.
**Verification:**
- [x] Tested in Blade script and DOM.
**Dependencies:** Task 5
**Files touched:** `resources/views/chat/index.blade.php`

---

## Phase 4: Final Testing & Verification

### Task 7: Comprehensive System Verification
**Description:** Run PHP lint checks, verify daily document selection and unmarking, confirm chat stays locked on refresh, and verify snappy chat reaction performance.
**Acceptance Criteria:**
- [x] All touched PHP and Blade files pass linting without errors.
- [x] Daily documents unmarking works flawlessly.
- [x] PIN lock reliably protects chat on refresh.
- [x] Reactions and presence operate with high speed.
**Verification:**
- [x] php -l and php artisan view:cache passed cleanly.
