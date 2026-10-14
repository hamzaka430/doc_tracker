# Implementation Plan: Decouple Chat to Aiven MySQL & Zero-JawsDB Chat Polling

## Overview
Move all Secret Chat database storage (`chat_messages`, `chat_settings`) from the JawsDB MySQL database (which has a strict 3,600 query/hour free plan limit) to a dedicated Aiven Cloud MySQL database with SSL. Reconfigure session storage to `file` and eliminate all JawsDB queries on the 1.5s chat polling endpoint, so that all document operations remain on JawsDB while chat operates with zero impact on JawsDB query quotas.

## Architecture Decisions
1. **Multi-Database Connection in Laravel:**
   - Keep default `mysql` connection pointing to JawsDB (products, vials, users, sap errors, user preferences, jobs).
   - Dedicated `mysql_chat` connection pointing to Aiven Cloud MySQL (`mysql-doctracking-doctracking.b.aivencloud.com:21102`, database `defaultdb`, SSL mode REQUIRED with `ca.pem`).
2. **Models Bound to `mysql_chat`:**
   - `ChatMessage` and `ChatSetting` models explicitly declare `protected $connection = 'mysql_chat';`.
3. **Zero JawsDB Polling Pipeline:**
   - `SESSION_DRIVER=file`: Moves session reads/writes off JawsDB to disk.
   - `CACHE_STORE=file` (or `CACHE_DRIVER=file`).
   - The `/chat/messages` polling route is excluded from `auth` middleware (already secured via `session('chat_unlocked')` in controller), eliminating `User::find()` queries against JawsDB during polling.
4. **Defense-in-Depth Polling Safeguards:**
   - Verify tab visibility pauses and backoff in `index.blade.php` to prevent unnecessary polls when the user is away.
5. **Heroku Configuration & Deployment:**
   - Authenticate Heroku CLI using user's API key.
   - Set Heroku Config Vars for Aiven connection (`CHAT_DB_*`) and session/cache drivers (`SESSION_DRIVER=file`, `CACHE_STORE=file`).
   - Run migrations on `mysql_chat` to create `chat_messages` and `chat_settings` tables in Aiven MySQL.
   - Deploy codebase with `ca.pem` included.

## Task List

### Phase 1: Configuration & Migration
- [ ] Task 1: Verify & update `config/database.php` for `mysql_chat` with SSL CA options and fallback.
- [ ] Task 2: Configure local `.env` with Aiven credentials and run database migrations on `mysql_chat`.
- [ ] Task 3: Verify models (`ChatMessage`, `ChatSetting`) and verify table schemas in Aiven.

### Checkpoint: Foundation
- [ ] Direct connection to Aiven MySQL tested and verified.
- [ ] `chat_messages` and `chat_settings` tables exist on Aiven MySQL.

### Phase 2: Polling & Zero-JawsDB Verification
- [ ] Task 4: Verify `ChatController` and `routes/web.php` for complete zero-query isolation from JawsDB during message polling.
- [ ] Task 5: Verify tab visibility handling in `chat/index.blade.php`.

### Phase 3: Heroku Deployment & Verification
- [ ] Task 6: Authenticate Heroku CLI and set Heroku config vars (`CHAT_DB_*`, `SESSION_DRIVER=file`, `CACHE_STORE=file`).
- [ ] Task 7: Ensure `ca.pem` is committed to git and push changes to Heroku (`git push heroku main`).
- [ ] Task 8: Run migrations on Heroku / verify production logs and test chat endpoint.

## Risks & Mitigations
| Risk | Impact | Mitigation |
|---|---|---|
| Aiven SSL requirement fails on Heroku | High | `ca.pem` is committed to repository root; `config/database.php` points `PDO::MYSQL_ATTR_SSL_CA` to `base_path('ca.pem')`. |
| Session driver `file` loses sessions across Heroku dyno restarts | Low | Standard for single web dyno; chat unlock session simply re-prompts 4-digit PIN on restart. |
| Existing chat messages on JawsDB are left behind | Low | User specified chat can start fresh or table created on Aiven; clean migration setup. |
