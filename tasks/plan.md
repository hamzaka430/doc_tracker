# Implementation & Deployment Plan: Sidebar Unread Message Count Feature

## Overview
Deploy ONLY the real-time sidebar unread messages count badge to Heroku production without deploying any unfinished chat layout or mobile UI modifications, and without deploying local commit `8556014`.

## Architecture & Deployment Decisions
1. **Zero-Contamination Release Branch**: Create a clean deployment branch `deploy/sidebar-unread-count` directly from `remotes/heroku/main` (currently at `2efc7b1`).
2. **Preserve Local In-Progress Work**: Stash all existing uncommitted modifications (`custom.css`, `chat/index.blade.php`, etc.) so the user's ongoing work is 100% preserved.
3. **Targeted Feature Application**: Apply only:
   - `app/Http/Controllers/ChatController.php`: `unreadCount()` controller action
   - `routes/web.php`: `/chat/unread-count` route
   - `resources/views/layouts/dashboard.blade.php`: Sidebar badge element `#chatSidebarUnreadBadge` and client polling function `pollSidebarUnreadCount`
4. **Verification**: Run `php -l` lint checks and verify route registration.
5. **Production Deployment**: Push `deploy/sidebar-unread-count:main` to `heroku`.
6. **Workspace Restoration**: Switch back to `main` branch and restore stashed local changes.

## Task List

### Phase 1: Isolation & Preparation
- [ ] Task 1: Stash local modifications to ensure no work is lost
- [ ] Task 2: Create release branch `deploy/sidebar-unread-count` from `remotes/heroku/main`

### Checkpoint 1
- [ ] Working tree on release branch matches live production clean state

### Phase 2: Feature Implementation on Release Branch
- [ ] Task 3: Add `unreadCount()` method to `app/Http/Controllers/ChatController.php`
- [ ] Task 4: Add route `/chat/unread-count` in `routes/web.php`
- [ ] Task 5: Add sidebar unread badge and polling script in `resources/views/layouts/dashboard.blade.php`

### Checkpoint 2
- [ ] PHP syntax check passes (`php -l`)
- [ ] Route list includes `chat.unreadCount`
- [ ] Git diff on release branch shows ONLY sidebar unread count changes

### Phase 3: Deployment & Cleanup
- [ ] Task 6: Commit changes with atomic semantic commit message
- [ ] Task 7: Deploy to Heroku (`git push heroku deploy/sidebar-unread-count:main`)
- [ ] Task 8: Switch back to `main` branch and restore `git stash pop`

## Risks and Mitigations
| Risk | Impact | Mitigation |
|------|--------|------------|
| Database query quota spike on JawsDB | High | Route `/chat/unread-count` is placed outside auth middleware, and queries `mysql_chat` (Aiven MySQL) which has an index on `(sender_name, is_read)`. Zero queries to JawsDB. |
| In-progress layout changes accidentally pushed | High | Using a dedicated release branch off `heroku/main` guarantees zero layout changes reach production. |
| Local uncommitted work lost during branch switch | High | Stash created and verified prior to branch switch; restored cleanly afterward. |
