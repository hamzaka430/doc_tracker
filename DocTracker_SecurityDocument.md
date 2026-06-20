# 🔐 Security and Access Document — DocTracker
**Client:** Healthtek Pvt Ltd  
**Author:** Security Architecture Review  
**Version:** 1.0 (Audited from live codebase — Laravel 11, June 2026)  
**Audience:** Founders, Product Owners, and Developers  

> This document is written so that a non-technical founder can understand every security decision, what is already protected, what is not, and what must be fixed before launch. Technical references are included in footnotes for the developer implementing fixes.

---

## Table of Contents

1. [How Authentication Works](#1-how-authentication-works)
2. [User Roles & Permissions](#2-user-roles--permissions)
3. [Row-Level Security — Who Can See What](#3-row-level-security)
4. [Error Handling Guide](#4-error-handling-guide)
5. [Pre-Launch Edge Cases & Security Fixes](#5-pre-launch-edge-cases--security-fixes)
6. [What Is Already Secure](#6-what-is-already-secure)
7. [Security Health Dashboard](#7-security-health-dashboard)

---

## 1. How Authentication Works

### What Authentication Means (Plain English)

Authentication is the process of proving you are who you say you are. In DocTracker, this means: before you can see any document or batch data, the system first verifies your email and password are correct. If they match, it gives you a session — like a digital ID badge — that gets checked on every page you visit.

### The Current Method: Session-Based Authentication

DocTracker uses a proven, industry-standard method called **session-based authentication** via Laravel Breeze.

**Here is exactly what happens when someone logs in:**

```
Step 1: User types email + password → clicks "Sign In"
Step 2: App checks the rate limiter — has this email/IP tried too many times?
           → If YES (5+ failed attempts): locked out for 60 seconds
           → If NO: continue
Step 3: App looks up the email in the database
Step 4: App verifies the password matches the stored (hashed) password
Step 5: If correct → generates a new session ID (an encrypted random token)
Step 6: Stores session data in the database (sessions table)
Step 7: Sends a session cookie to the user's browser
Step 8: Every future page visit includes this cookie → user stays logged in
Step 9: Session expires automatically after 120 minutes of inactivity
```

**What "hashed password" means:** Passwords are never stored as plain text. When you set your password "abc123", the system runs it through a mathematical algorithm (bcrypt) that turns it into a scrambled string like `$2y$12$xKp7...`. This scrambled version is what's saved. Even if someone stole your database, they could not reverse the scramble back to "abc123". This is the correct, industry-standard approach and DocTracker does this correctly.

### Is This the Right Auth Method for DocTracker?

**Yes — session-based auth is the correct choice** for this use case. Here's why:

| Method | Why It Fits DocTracker |
|--------|----------------------|
| **Session Auth (current)** | ✅ Internal tool, same-origin, users stay logged in across a work shift, no mobile app |
| API Tokens (JWT) | ❌ Overkill — needed when you have a separate mobile app or external integrations |
| Google/SSO Login | ⚠️ Optional future enhancement for Healthtek IT environment |
| Magic Link (email) | ❌ Too slow for factory floor usage where speed matters |

### Session Configuration Summary

| Setting | Current Value | Security Assessment |
|---------|--------------|-------------------|
| Session stored in | Database (`sessions` table) | ✅ Secure — session data lives server-side |
| Session lifetime | 120 minutes | ✅ Reasonable for a work shift |
| Cookie is HTTP-only | Yes | ✅ JavaScript cannot read the cookie |
| Cookie SameSite | Lax | ✅ Prevents cross-site request forgery |
| Cookie encrypted | No | ⚠️ Recommend enabling in production |
| Secure cookie (HTTPS only) | Reads from env | ⚠️ Must be set to `true` in production |

### Password Reset Flow

DocTracker has a complete password reset system:
1. User clicks "Forgot password?" on login page
2. User enters their email address
3. System sends a reset link to that email (link is valid for **60 minutes**)
4. User clicks the link and sets a new password
5. New password is immediately hashed and saved; old link is invalidated

**Important:** The password reset feature requires a working email service (SMTP/Mailgun) to be configured in the `.env` file before it can send reset emails. Without this, users who forget their password will be locked out permanently.

### Login Rate Limiting — Already Implemented ✅

After **5 failed login attempts** from the same email + IP address combination, the system locks that combination out for **60 seconds**. This protects against automated password guessing attacks. This is correctly implemented in `LoginRequest.php`.

---

## 2. User Roles & Permissions

### Current State: Single-Role System

In version 1, DocTracker has **one role** — "Authenticated User." Every person who logs in has identical permissions. There is no admin, no supervisor, no read-only role.

**What this means in practice:**
- Any logged-in user can create, edit, submit, and delete their own documents
- No user can see or touch another user's documents (this is enforced at the data level — see §3)
- There is no user who has elevated access to see everyone's data

### Why This Is Fine for v1

At Healthtek's current scale (small team, single facility), having one role works because:
- Each operator manages their own document queue
- A supervisor who wants to see everyone's data would need their own account and a code change — this is the v2 priority (see below)

---

### Role: Authenticated Operator (Current — Everyone)

This is the only role in the system today.

#### ✅ What They CAN Do

| Action | Where | Notes |
|--------|-------|-------|
| Log in and log out | Login page | Standard |
| Register a new account | Registration page | ⚠️ See §5 — this should be restricted |
| View their own documents | All Documents, Pending, Submitted, Daily views | Own data only |
| Create new batch documents | Add Document page | Single or multiple at once |
| Edit a document's clearance checkboxes and remarks | Document Detail page | Only while document is pending or within 6 hours of submission |
| Edit a document's basic info (name, batch, stage, type) | Edit page | Same time restriction applies |
| Submit a single document | Document Detail page | Only when all 3 clearances are checked |
| Bulk submit multiple pending documents | Pending Documents page | All selected documents submitted at once |
| Delete a pending document | Document Detail page | Moves to Recycle Bin (soft delete) |
| Restore a deleted document | Recycle Bin page | Returns document to Pending status |
| Export daily PDF | Daily List page | Single or double column layout |
| Export submitted documents to CSV | Submitted page | Their own submissions only |
| Log a SAP error | SAP Errors page | With optional screenshot |
| Edit their own SAP error entries | SAP Errors page | Only errors they created |
| Delete their own SAP error entries | SAP Errors page | Image deleted from S3 simultaneously |
| Update profile name and email | Profile page | Email must remain unique |
| Change their password | Profile page | Requires current password confirmation |
| Upload or remove their profile avatar | Profile page | Stored on AWS S3 |
| Delete their account | Profile page | Requires password confirmation; permanently deletes all their data |

#### ❌ What They CANNOT Do

| Action | Why Not |
|--------|---------|
| See another user's documents | Global data scope filters all queries by their own user ID |
| Edit another user's document | Controller checks ownership; throws 403 Forbidden |
| Delete another user's SAP error | `authorizeError()` method checks ownership; throws 403 |
| Change another user's password | Not possible — password change requires the current password |
| Modify a document more than 6 hours after submission | `isEditable()` method returns false; form is hidden; server rejects the request |
| Delete a submitted document after the 6-hour window | `isEditable()` returns false; delete button hidden; server rejects |
| Access any page without logging in | `auth` middleware redirects to login page automatically |

---

### Role: Supervisor (Does Not Exist Yet — v2 Priority)

This role does not exist in the code today. It is described here so you understand what to build when the team grows.

#### What a Supervisor Should Be Able to Do

| Action | Status |
|--------|--------|
| See all operators' documents in one view | ❌ Not built |
| Filter documents by operator name | ❌ Not built |
| View aggregate analytics (team-level, not just personal) | ❌ Not built |
| Export all submissions across all operators | ❌ Not built |
| Approve or reject a document submission | ❌ Not built |
| Cannot create or delete documents | By design |

**How to Build This:** Add a `role` column to the `users` table (`operator` or `supervisor`). Add middleware that checks the role before accessing supervisor-only routes. Remove the global user scope from queries in supervisor controllers.

---

### Role: Admin (Does Not Exist Yet — Future)

| Action | Status |
|--------|--------|
| Create and deactivate user accounts | ❌ Not built |
| Reset any user's password | ❌ Not built |
| View system health and usage stats | ❌ Not built |
| Configure product catalog | ❌ Not built |
| Permanently purge the Recycle Bin | ❌ Not built |

---

## 3. Row-Level Security

### What Is Row-Level Security? (Plain English)

Imagine your database is a giant filing cabinet with thousands of document folders. Row-level security means that each person can only open the folders that belong to them — even if they know the exact drawer and folder number of someone else's documents.

In practice: if User A's document has ID number 42, and User B types `/products/42` directly into their browser, row-level security either shows them their own document #42, or shows a "Not Found" error. They should **never** see User A's data.

### How DocTracker Implements This

DocTracker uses two mechanisms to enforce data isolation:

---

#### Mechanism 1: Global Eloquent Scope on Products ✅ (Strong)

The `Product` model has a "global scope" — a permanent filter that is automatically added to every single database query for products, everywhere in the code.

**In plain English:** Every time the app asks the database "give me products," the system invisibly adds "...but only products belonging to the currently logged-in user." This is automatic and cannot be forgotten.

**What this means:** If User B goes to `/products/42` and that document belongs to User A, the global scope means User A's document #42 simply does not exist as far as User B's query is concerned. Laravel returns a 404 "Not Found" page — not a 403 "Forbidden" — which is actually more secure because it doesn't even confirm whether document #42 exists.

**Strength:** Very strong. The filter is in the model itself, so every controller automatically benefits from it without the developer needing to remember to add it.

---

#### Mechanism 2: Manual `where('user_id')` on SAP Errors ⚠️ (Weaker — Needs Fixing)

The `SapError` model does **not** have a global scope. Instead, each controller method manually writes:

```
SapError::where('user_id', auth()->id())...
```

The `show`, `edit`, `update`, and `destroy` methods use a helper called `authorizeError()` which checks: "does this SAP error belong to the current user? If not, show a 403 Forbidden error."

**The risk:** This manual approach works correctly today because the code was written carefully. But as the app grows and new developers add features, someone might write a new SAP error query and forget the `where('user_id')` check. The Products table is immune to this mistake; SAP Errors are not.

**The fix:** Add a global scope to the `SapError` model, the same way it exists on the `Product` model. This is a 5-minute code change. See §5, Item #1.

---

#### Mechanism 3: User Preferences ✅ (Safe)

User preferences always use `auth()->id()` as the key in an `updateOrCreate` call. A user can only write preferences for themselves, and preferences are only read for the current user. No cross-user risk.

---

### Row-Level Security Summary Table

| Table | Protection Method | Strength |
|-------|------------------|----------|
| `products` | Global Eloquent scope (automatic) | 🟢 Strong |
| `sap_errors` | Manual `where()` + `authorizeError()` | 🟡 Medium — works but fragile |
| `user_preferences` | Always uses `auth()->id()` as key | 🟢 Strong |
| `users` | Auth guards + profile controller | 🟢 Strong |
| `sessions` | Framework-managed | 🟢 Framework handles |

---

### What Happens If the Protection Fails? (Defense in Depth)

Even if the application-level filter was somehow bypassed (which is very unlikely in Laravel), the database itself has a `user_id` column on every sensitive table linked as a foreign key. A properly written SQL injection would still need to know and correctly specify the `user_id` to retrieve data. Laravel's Eloquent ORM uses parameterized queries by default, which makes SQL injection essentially impossible.

---

## 4. Error Handling Guide

### What "Error Handling" Means (Plain English)

When something goes wrong — a user submits a bad form, a file upload fails, the database is unavailable — your app needs to decide: what does it show the user, and what does it log for the developer? This section maps every major failure point in DocTracker to what currently happens and what should happen.

---

### 4.1 Authentication Errors

#### Wrong Email or Password
- **What happens:** The system shows "These credentials do not match our records." on the login form
- **What's logged:** Nothing (by design — logging failed attempts with email addresses is a privacy risk)
- **Is this correct?** ✅ Yes — vague error message on purpose. Never say "email not found" or "wrong password" separately, as that tells attackers which emails are registered
- **Rate limiting:** After 5 failed attempts, the user sees: "Too many login attempts. Please try again in X seconds/minutes." ✅

#### Session Expired
- **What happens:** User is redirected to the login page with their originally intended URL remembered. After logging in, they're sent back to where they were trying to go
- **Is this correct?** ✅ Yes — this is the standard, expected behavior

#### Accessing Protected Pages Without Logging In
- **What happens:** Automatic redirect to `/login`
- **Is this correct?** ✅ Yes — all routes are inside an `auth` middleware group

#### Password Reset — Email Not Found
- **What happens:** The system shows a success message even if the email doesn't exist in the database
- **Why:** This prevents attackers from discovering which emails are registered by trying the forgot-password form
- **Is this correct?** ✅ Yes — this is the correct security behavior

#### Password Reset Token Expired
- **What happens:** User sees "This password reset token is invalid" error
- **Token lifetime:** 60 minutes
- **Is this correct?** ✅ Yes

---

### 4.2 Authorization Errors (Accessing Something You Don't Own)

#### Trying to View/Edit/Delete Another User's SAP Error
- **What happens:** `authorizeError()` is called → if `user_id` doesn't match → `abort(403)` → user sees the default Laravel 403 Forbidden page
- **What's logged:** Laravel logs a 403 response automatically
- **Is this correct?** ✅ Yes, but the 403 page is generic/unstyled. Consider adding a custom 403 page for a better user experience

#### Trying to View Another User's Document (via URL manipulation)
- **What happens:** The global scope makes the document appear as if it doesn't exist → Laravel returns a 404 Not Found page
- **Is this correct?** ✅ Yes — returning 404 instead of 403 is more secure because it doesn't reveal that the document exists at all

#### Trying to Edit a Document After the 6-Hour Window
- **What happens (server-side):** Controller calls `isEditable()` → returns false → redirects back with "This document is no longer editable" error message
- **What happens (UI):** The edit form is hidden; the "Edit" and "Delete" buttons are not shown
- **Gap:** The UI hides the buttons, but the server check is what actually enforces it. Both layers working together is the correct approach ✅
- **Residual risk:** A technically savvy user could directly POST to the update URL. The server-side check catches this correctly ✅

---

### 4.3 Form Validation Errors

#### Missing or Invalid Form Fields (Document Creation)
- **Rules enforced:**
  - Product name: required, text, max 255 characters
  - Batch number: required, text, max 255 characters
  - Stage: required, text, max 255 characters
  - Type: required, must be exactly one of: Injection, Suspension, Tablet, Capsule
- **What happens:** Form submission fails; user is sent back to the form with their input preserved and red error messages shown next to each invalid field
- **Is this correct?** ✅ Yes

#### Submitting a Document That Isn't Fully Cleared
- **What happens:** Server checks `isReadyForSubmission()` → if any clearance checkbox is false → redirects back with "Please complete all clearances before submitting"
- **UI enforcement:** Submit button only appears when all three checkboxes are checked ✅
- **Server enforcement:** Server re-checks even if button was shown ✅ (important — never trust the UI alone)

#### Bulk Submit With No Documents Selected
- **What happens:** Returns error "No documents selected for submission"
- **Is this correct?** ✅ Yes

#### Profile Update — Email Already Taken by Another User
- **What happens:** Validation error "The email has already been taken"
- **Is this correct?** ✅ Yes — the `unique` rule ignores the current user's own email

#### Remarks Field Too Long
- **Maximum length:** 1,000 characters (enforced server-side)
- **What happens:** Validation error shown
- **Is this correct?** ✅ Yes

---

### 4.4 File Upload Errors (Avatars and SAP Screenshots)

#### File Upload to S3 Fails
- **What happens:** The upload call is inside a `try/catch` block → if S3 throws an error, the user is sent back to the form with: "Image upload failed: [error message]"
- **What's logged:** The S3 exception is caught and shown to the user (the error message from AWS)
- **Risk:** The AWS error message could contain internal details (bucket name, region). In production, consider replacing the raw AWS error with a generic "Upload failed. Please try again" message
- **Is this correct?** 🟡 Partially — the catch exists (good), but the raw error message is exposed to the user (needs improvement)

#### File Too Large
- **Avatar limit:** 2MB
- **Screenshot limit:** 5MB
- **What happens:** Laravel's validator rejects the file before it even reaches S3; user sees "The file may not be greater than X kilobytes"
- **Is this correct?** ✅ Yes

#### Wrong File Type
- **Allowed types:** PNG, JPG, JPEG, WebP
- **What happens:** Validation rejects non-image files; user sees "The file must be an image"
- **Is this correct?** ✅ Yes

#### Replacing an Image — Old Image on S3
- **What happens:** Before uploading the new file, the old file path is fetched from the database and deleted from S3 first
- **Is this correct?** ✅ Yes — prevents "orphaned" files accumulating on S3 and costing money

---

### 4.5 Database Errors

#### Database Connection Failure
- **What happens:** Laravel throws an exception → if `APP_DEBUG=false`, user sees a generic "Server Error" (500) page → the actual error is written to `storage/logs/laravel.log`
- **Critical setting:** `APP_DEBUG` must be `false` in production. If it is `true` in production, the error page will show your database credentials, server file paths, and internal code — a major security risk
- **Is this correct?** 🟡 Depends on configuration. See §5 Item #6.

#### Record Not Found (e.g., `/products/99999`)
- **What happens:** Laravel automatically returns a 404 Not Found page when a model lookup fails
- **Is this correct?** ✅ Yes

#### Soft Delete / Restore Failure
- **What happens:** If a restore is attempted on a record that doesn't exist in the trash (wrong ID), `findOrFail()` throws a 404
- **Is this correct?** ✅ Yes

---

### 4.6 PDF and CSV Export Errors

#### PDF Generation Failure (DomPDF)
- **What happens:** If DomPDF crashes (e.g., malformed data), an unhandled exception occurs → 500 error page
- **Gap:** There is no try/catch around the PDF generation. If a product's data contains special characters that break the PDF renderer, the export silently fails with a generic error
- **Recommended fix:** Wrap `Pdf::loadView(...)->download()` in a try/catch and show a user-friendly error message ⚠️

#### CSV Export — Very Large Dataset
- **What happens:** CSV is streamed as a download using `response()->stream()`, which means it doesn't load all records into memory at once. This is the correct approach for large datasets ✅
- **Risk:** If the CSV contains tens of thousands of rows, the streaming could time out on Heroku (30-second timeout limit)
- **Current scale:** Not an immediate concern for Healthtek's volume

---

### 4.7 Preferences Error

#### Invalid Preference Key or Value Format
- **What happens:** The `key` field is validated (required, string). The `value` field must be an array or null
- **Gap:** There is no whitelist of allowed preference keys. A user could theoretically POST any key name and it would be saved. While this only affects their own preferences (no cross-user risk), it could pollute the database with garbage data
- **Recommended fix:** Add a validation rule: `'key' => 'required|in:hidden_items,autocomplete_enabled,...'` ⚠️

---

## 5. Pre-Launch Edge Cases & Security Fixes

These are ordered by priority — fix the top items before going live.

---

### 🔴 CRITICAL — Fix Before Any Users Are Onboarded

#### #1 — Open Registration Must Be Disabled or Restricted
**The problem:** Right now, anyone on the internet can visit `/register` and create an account on your DocTracker system. This means strangers can log in, create fake batch documents, and access the application.

**Why this matters:** DocTracker is an internal tool for Healthtek Pvt Ltd employees only. There should be no public sign-up.

**How to fix it (choose one):**
- **Option A — Disable registration entirely:** Remove the `/register` route and the "Sign up" link from the login page. An admin manually creates accounts via `php artisan tinker` or a protected admin panel
- **Option B — Domain restriction:** Allow registration only for emails ending in `@healthtek.com` (or whatever Healthtek's domain is)
- **Option C — Invite-only:** Generate a unique invite link; only people with the link can register

**Developer note:** Comment out or remove the `Route::get('/register', ...)` and `Route::post('/register', ...)` lines in `routes/auth.php`

---

#### #2 — S3 Bucket Must Not Be Publicly Listable
**The problem:** If your AWS S3 bucket is set to "public" in the AWS console, anyone who knows (or guesses) your bucket name can list all files in it and download every avatar and error screenshot without logging in.

**How to fix it:**
1. Log into AWS Console → S3 → your bucket
2. Under "Block public access" settings → enable ALL four block settings
3. Add a bucket policy that only allows your Laravel application's IAM user to read/write
4. Make sure `AWS_USE_PATH_STYLE_ENDPOINT=false` and the bucket itself has no public ACL

**The images should only be accessible via a signed URL or through your application** — never directly from S3 with a public link.

---

#### #3 — `APP_DEBUG=false` Must Be Confirmed in Production
**The problem:** If `APP_DEBUG=true` in your production `.env` file, every error page (database failure, file not found, etc.) will display your database password, AWS keys, internal file paths, and full code stack traces — visible to any user who triggers an error.

**How to check:** Look at your Heroku config vars. `APP_DEBUG` must be `false`. If it's `true`, change it immediately.

**Developer note:** Run `heroku config:set APP_DEBUG=false` and then `heroku config:set APP_ENV=production`

---

### 🟠 HIGH PRIORITY — Fix Within First Week

#### #4 — Add Global Scope to SapError Model
**The problem:** Described in §3. The SAP Errors model does not have automatic user isolation. While the current code handles it correctly with manual checks, this is fragile as the codebase grows.

**How to fix it:** Copy the `booted()` method with the global scope from `Product.php` into `SapError.php`. This is a 10-line code change.

**Developer note:** Add to `SapError.php`:
```php
protected static function booted()
{
    static::addGlobalScope('user', function (Builder $builder) {
        if (auth()->check()) {
            $builder->where('user_id', auth()->id());
        }
    });
}
```

---

#### #5 — SESSION_SECURE_COOKIE Must Be True in Production
**The problem:** If `SESSION_SECURE_COOKIE` is not explicitly set to `true` in production, the session cookie could potentially be sent over an unencrypted HTTP connection, allowing it to be stolen.

**How to fix it:** Add to your Heroku config: `SESSION_SECURE_COOKIE=true`

Heroku automatically provides HTTPS, so this setting just ensures the cookie is restricted to HTTPS connections only.

---

#### #6 — Confirm Email Service Is Configured for Password Reset
**The problem:** Password reset emails cannot be sent without a working email service. If an operator forgets their password and no email service is configured, they are permanently locked out with no self-service recovery.

**How to fix it:** Configure Mailgun, Postmark, or another SMTP provider and set the `MAIL_*` environment variables in Heroku.

---

#### #7 — Add Custom Error Pages (403, 404, 500)
**The problem:** When an authorization error (403), missing page (404), or server error (500) occurs, Laravel shows its default, unstyled, branded error pages. This looks unprofessional and can confuse non-technical users.

**How to fix it:** Create custom Blade templates at:
- `resources/views/errors/403.blade.php` — "You don't have permission to access this"
- `resources/views/errors/404.blade.php` — "Page not found"
- `resources/views/errors/500.blade.php` — "Something went wrong. We're looking into it."

These pages should match the DocTracker design and include a link back to the dashboard.

---

### 🟡 MEDIUM PRIORITY — Fix Before Scaling

#### #8 — Raw S3 Error Messages Should Not Be Shown to Users
**The problem:** When an S3 file upload fails, the raw AWS error message is displayed to the user. This can expose internal infrastructure details (bucket name, region, access patterns).

**How to fix it:** In `ProfileController` and `SapErrorController`, change:
```php
// Current:
return back()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()]);

// Better:
return back()->withErrors(['image' => 'Image upload failed. Please try again or contact support.']);
// Log the actual error for the developer:
Log::error('S3 upload failed: ' . $e->getMessage());
```

---

#### #9 — `user_id` Column on Products Is Nullable — Should Not Be
**The problem:** When the `user_id` column was added to the products table, it was added as `nullable()`. This means a product record could theoretically exist with no owner (`user_id = NULL`). The global scope filters by the current user's ID, so if `user_id` is NULL, that product becomes invisible to everyone and is essentially orphaned.

**How to fix it:** Run a database migration to:
1. First, assign any NULL user_ids to a default admin user
2. Then change the column to `NOT NULL`

---

#### #10 — Preference Keys Are Not Whitelisted
**The problem:** Described in §4.7. Any string can be saved as a preference key, which could lead to database pollution.

**How to fix it:** In `UserPreferenceController`, add:
```php
'key' => 'required|in:hidden_items,autocomplete_enabled,default_stage'
```

---

#### #11 — PDF Export Has No Error Handling
**The problem:** If PDF generation crashes, the user sees a raw 500 server error with no useful message.

**How to fix it:** Wrap the PDF generation in a try/catch and redirect with an error message if it fails.

---

#### #12 — SAP Errors List Has No Pagination
**The problem:** `SapError::where(...)->get()` returns all records at once. As the error log grows, this will slow down the page and eventually cause memory issues.

**How to fix it:** Change to `->paginate(20)` and add pagination controls to the view.

---

### 🔵 LOW PRIORITY — Good to Have Before Scale

#### #13 — Session Encryption Should Be Enabled
**Setting:** `SESSION_ENCRYPT=true` in production `.env`

**Why:** Session data stored in the database (which includes user ID, CSRF tokens, flash messages) would be encrypted at rest. This adds an extra layer of protection if the database is ever compromised.

**Trade-off:** Very small performance overhead. Worth enabling.

---

#### #14 — Implement Account Lockout Notification
**The problem:** When an account is locked due to too many failed login attempts, the legitimate user is not notified.

**How to fix it:** Laravel fires a `Lockout` event (already present in the code). Add a listener that sends an email to the account owner: "Someone attempted to log into your DocTracker account multiple times. Your account has been temporarily locked."

---

#### #15 — The "Delete Account" Feature Deletes All Production Data
**The problem:** An operator can delete their own account from the Profile page. Because products are linked with `CASCADE ON DELETE`, deleting a user permanently deletes all their batch documents, SAP errors, and preferences — with no undo.

**Why this is dangerous:** An operator could accidentally delete months of batch history.

**Recommended fix:**
- Add a confirmation step that explicitly states "This will permanently delete all your documents and cannot be undone"
- Consider soft-deleting users instead of hard-deleting
- Or make account deletion a request that only an admin can approve

---

#### #16 — The Product Catalog Is Hardcoded in the Controller
**Security-adjacent issue:** The list of predefined product names and stages is written directly in `ProductController.php`. This is a maintenance and accuracy risk — if Healthtek adds new products, a developer must modify the code and redeploy.

**Recommended fix:** Move the catalog to a database table (`product_catalog`) with a simple admin interface to add/remove products.

---

#### #17 — No HTTPS Enforcement at Application Level
**The problem:** While Heroku provides HTTPS automatically, the application itself does not enforce HTTPS redirects. A user who types `http://` instead of `https://` would get an unencrypted connection.

**How to fix it:** Add HTTPS redirect middleware in `bootstrap/app.php`, or configure it via Heroku's `FORCE_HTTPS` settings.

---

## 6. What Is Already Secure

Before focusing only on what needs fixing, it's important to document what DocTracker gets right. This is a strong baseline.

| Security Feature | Implementation | Status |
|-----------------|---------------|--------|
| Passwords are hashed (bcrypt) | `Hash::make()` on registration and password change | ✅ Correct |
| CSRF protection on all forms | `@csrf` directive in every form, verified by middleware | ✅ Correct |
| SQL injection prevention | Eloquent ORM uses parameterized queries throughout | ✅ Correct |
| XSS prevention | Blade `{{ }}` syntax HTML-escapes all output by default | ✅ Correct |
| Login rate limiting | 5 attempts max, then 60-second lockout | ✅ Correct |
| Session regeneration on login | `$request->session()->regenerate()` after successful login | ✅ Correct — prevents session fixation attacks |
| Session invalidation on logout | `invalidate()` + `regenerateToken()` called on logout | ✅ Correct |
| Authorization on SAP errors | `authorizeError()` helper checks ownership | ✅ Correct |
| Products scoped by user | Global Eloquent scope on Product model | ✅ Correct |
| S3 old file deletion on replace | Old image deleted before uploading new one | ✅ Correct |
| Server-side validation | All form inputs validated in controllers/FormRequests | ✅ Correct |
| Editable window enforcement | `isEditable()` checked both in UI and server-side | ✅ Correct (both layers) |
| Submission gate enforcement | `isReadyForSubmission()` checked server-side before submit | ✅ Correct |
| S3 upload failure caught | Try/catch around S3 uploads; returns error to user | ✅ Correct pattern |
| Soft deletes preserve data | Products go to Recycle Bin, not permanently deleted | ✅ Correct |
| Password reset token expiry | 60-minute token lifetime, throttled regeneration | ✅ Correct |
| Trust proxies configured | `trustProxies(at: '*')` — correct for Heroku deployment | ✅ Correct |
| HTTP-only session cookie | JavaScript cannot read the session cookie | ✅ Correct |
| SameSite cookie policy | Set to "Lax" — prevents most CSRF attacks on top of the token | ✅ Correct |

---

## 7. Security Health Dashboard

A quick one-page summary of your security posture today.

### Overall Rating: 🟡 Moderate — Good Foundation, 5 Critical Gaps Before Launch

```
Authentication       ████████░░  80%  — Strong, needs HTTPS cookie enforcement
Data Isolation       ███████░░░  70%  — Products: excellent; SAP Errors: needs global scope
Access Control       ██████░░░░  60%  — No roles yet; open registration is a critical gap
Error Handling       ███████░░░  70%  — Most paths handled; PDF and S3 error messages need work
Input Validation     █████████░  90%  — Strong server-side validation throughout
Infrastructure       ██████░░░░  60%  — Needs debug mode and HTTPS cookie confirmed in prod
```

### Pre-Launch Checklist

| # | Item | Priority | Fixed? |
|---|------|----------|--------|
| 1 | Disable or restrict public registration | 🔴 Critical | ☐ |
| 2 | Verify S3 bucket is not publicly listable | 🔴 Critical | ☐ |
| 3 | Confirm `APP_DEBUG=false` in production | 🔴 Critical | ☐ |
| 4 | Add Global Scope to SapError model | 🟠 High | ☐ |
| 5 | Set `SESSION_SECURE_COOKIE=true` in production | 🟠 High | ☐ |
| 6 | Configure email service for password reset | 🟠 High | ☐ |
| 7 | Add custom 403, 404, 500 error pages | 🟠 High | ☐ |
| 8 | Replace raw S3 error messages with generic messages | 🟡 Medium | ☐ |
| 9 | Make `user_id` on products NOT NULL | 🟡 Medium | ☐ |
| 10 | Whitelist allowed preference keys | 🟡 Medium | ☐ |
| 11 | Add try/catch around PDF export | 🟡 Medium | ☐ |
| 12 | Add pagination to SAP Errors list | 🟡 Medium | ☐ |
| 13 | Enable `SESSION_ENCRYPT=true` | 🔵 Low | ☐ |
| 14 | Add lockout notification email | 🔵 Low | ☐ |
| 15 | Add explicit warning before account deletion | 🔵 Low | ☐ |
| 16 | Move product catalog to database | 🔵 Low | ☐ |
| 17 | Enforce HTTPS at application level | 🔵 Low | ☐ |

---

*Document prepared from live codebase security audit of DocTracker v1 (Laravel 11, PHP 8.2, June 2026 build). All findings are referenced to actual code files and reflect the deployed state of the application.*
