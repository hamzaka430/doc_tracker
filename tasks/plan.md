# Implementation Plan: Chat Speed Optimization, Reliable PIN Lock, & Daily Documents Selection Fix

## Overview
This plan addresses 3 core areas requested by the user:
1. **Daily Documents Selection Bug (`/products/daily`)**: When user selects all documents, they get selected, but unmarking does not unmark individual items or clear bulk selection due to unsynchronized desktop/mobile checkboxes.
2. **Chat Lock Glitch**: The chat lock should ONLY open with a PIN code, and must never open automatically on page refresh. Fix the session lifecycle and dual-view transition so that every page refresh or entry requires the 4-digit security code.
3. **Chat System Performance & Speed**: Make all chat features (reactions, last seen activity, typing indicators, replies, unsend, receipts) lightning-fast and responsive using optimistic UI updates and backend query optimizations (throttled presence writes, conditional read receipts, targeted indexes).

---

## Architecture & Design Decisions

### 1. Daily Documents Selection Fix (`resources/views/products/daily.blade.php`)
- Both Desktop table (`selectAllDesktop`) and Mobile card list (`selectAllMobile`) render checkboxes with `.product-checkbox`.
- When an individual checkbox changes, synchronize all checkboxes with the matching value (`.product-checkbox[value="${this.value}"]`).
- Update `selectAllDesktop.checked` and `selectAllMobile.checked` based on whether all visible items are selected.
- When `selectAll` is clicked (checked or unchecked), apply `isChecked` to all checkboxes and update the bulk submit count.

### 2. Lock System Security Fix (`ChatController.php` & `resources/views/chat/index.blade.php`)
- In `ChatController::index()`, ensure `GET /chat` resets the active unlock state (`session()->forget('chat_unlocked')`) so that accessing or refreshing the page ALWAYS requires the PIN.
- In `resources/views/chat/index.blade.php`, render the PIN Security Verification Card as the active view and the Chat Container ready in the DOM.
- When `verifyPin` succeeds via AJAX, verify PIN on server, set `session(['chat_unlocked' => true])`, and smoothly transition the UI from the PIN keypad to the Chat interface without full page reload.
- If the user reloads the page (`F5`) or navigates away and comes back, the page is securely locked again, strictly requiring the 4-digit PIN code.

### 3. Chat Speed & Performance Optimization
- **Optimistic UI for Reactions**: When a user double-taps or taps a reaction emoji, immediately update the DOM in 0ms (toggle reaction count/pill, play heart burst) before waiting for the network. Background sync reconciliation handles server state.
- **Backend Presence Throttling (`fetchMessages`)**: Throttles writes to `chat_settings` — only writes to database if `is_typing` state changed or if >8 seconds have elapsed since last write.
- **Conditional Read Receipt Updates**: Only executes `UPDATE chat_messages SET is_read = true` if there are unread messages from the other user.
- **Theme Caching**: Avoid re-querying `chat_theme` from MySQL on every 1.6s polling cycle.
- **Database Indexes Migration**: Add index on `updated_at`, `is_pinned`, and `(sender_name, is_read)` to `chat_messages` on `mysql_chat` connection for sub-millisecond query execution.

---

## Task Breakdown

### Phase 1: Daily Documents Selection Bug Fix
- [ ] Task 1: Fix checkbox synchronization and unmarking in `resources/views/products/daily.blade.php`.

### Phase 2: Lock System Glitch Fix
- [ ] Task 2: Update `ChatController.php` to enforce lock-on-fresh-visit/refresh and support in-page unlock.
- [ ] Task 3: Update `resources/views/chat/index.blade.php` to transition into chat upon PIN verification without page reload and stay locked on refresh.

### Phase 3: Chat Speed & Performance Optimizations
- [ ] Task 4: Add MySQL performance migration for `chat_messages` indexes on `mysql_chat`.
- [ ] Task 5: Optimize backend queries in `ChatController.php` (`fetchMessages` presence throttling, conditional updates, cached theme).
- [ ] Task 6: Implement Optimistic UI for reactions, typing indicators, and message unsend in `resources/views/chat/index.blade.php`.

### Phase 4: Verification & Testing
- [ ] Task 7: Run PHP syntax validation, verify Daily Documents unmarking, test PIN lock on refresh, and verify instant reaction speeds.
