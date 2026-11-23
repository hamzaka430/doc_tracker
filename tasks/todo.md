# Task List: Sidebar Unread Messages Count Selective Deployment

## Phase 1: Isolation & Preparation
- [x] Task 1: Stash local modifications (`git stash push -u -m "WIP local uncommitted changes"`)
- [x] Task 2: Create release branch `deploy/sidebar-unread-count` tracking `remotes/heroku/main`

## Checkpoint 1
- [x] Clean working tree on `deploy/sidebar-unread-count` matching live Heroku production (`8556014`)

## Phase 2: Feature Implementation on Release Branch
- [x] Task 3: Implement `unreadCount()` in `app/Http/Controllers/ChatController.php`
- [x] Task 4: Register route `Route::get('/chat/unread-count', ...)` in `routes/web.php`
- [x] Task 5: Add `#chatSidebarUnreadBadge` and `pollSidebarUnreadCount` in `resources/views/layouts/dashboard.blade.php`

## Checkpoint 2
- [x] Lint check passes: `php -l app/Http/Controllers/ChatController.php` & `php -l routes/web.php`
- [x] Artisan route verification: `php artisan route:list --name=chat.unreadCount`
- [x] Verified git diff contains ONLY the sidebar unread message count logic (0 layout changes)

## Phase 3: Production Deployment & Restoration
- [x] Task 6: Commit changes to `deploy/sidebar-unread-count` (commit `b6591ca`)
- [x] Task 7: Deploy to Heroku: `git push heroku deploy/sidebar-unread-count:main` (Released v60)
- [x] Task 8: Verified live response: `{"unread_count":1}` from Heroku production endpoint
- [x] Task 9: Return to `main` branch and restore local uncommitted work (`git stash pop`)
