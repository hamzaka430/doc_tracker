# 🎫 Feature Ticket List — DocTracker
**Client:** Healthtek Pvt Ltd  
**Author:** Engineering Lead  
**Version:** 1.0 — June 2026  
**Based on:** PRD v1.0, Security Audit, Technical Architecture Document, and codebase review  
**Stack:** Laravel 11 · PHP 8.2 · MySQL · Bootstrap 5 · Tailwind CSS · AWS S3 · Heroku

> Each ticket below is written to be used directly as a prompt for an AI coding tool. Each includes: description, acceptance criteria, a ready-to-paste AI prompt, dependencies, and a priority label.

---

## How to Use This Document

- **Copy the AI Prompt** section of any ticket directly into your coding tool (Cursor, GitHub Copilot Chat, etc.)
- **Priority Labels:**
  - 🔴 **MUST-HAVE** — blocking launch; do not onboard users until resolved
  - 🟠 **SHOULD-HAVE** — important for quality; target within first sprint after launch
  - 🟡 **NICE-TO-HAVE** — valuable but not urgent; plan for v1.5
  - 🔵 **V2** — deliberate deferral; design now, build next cycle
- **Phases:**
  - **Phase 1** — Pre-Launch Fixes (8 tickets)
  - **Phase 2** — v1 Enhancements (9 tickets)
  - **Phase 3** — v2 New Features (11 tickets)

---

## Phase 1 — Pre-Launch Fixes

These issues were identified in the security audit and codebase review. They must be resolved before any user is onboarded in a production environment.

---

### TICKET-001 · Disable or Restrict Public Registration
**Priority:** 🔴 MUST-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (1–2 hours)

#### Description
The registration page (`/register`) is currently publicly accessible. Anyone on the internet can create an account on the DocTracker system. DocTracker is an internal tool for Healthtek Pvt Ltd employees only — there must be no open self-registration in production.

#### Acceptance Criteria
- [ ] The `/register` route and `/register` form are not publicly accessible
- [ ] A user attempting to visit `/register` is redirected to `/login` with an informational message: "Account registration is by invitation only. Please contact your administrator."
- [ ] The "Sign up" link on the login page (`login.blade.php`) is removed
- [ ] Existing authenticated users are not affected — login, logout, and profile functions work unchanged
- [ ] At least one admin can still create new user accounts via Laravel Tinker or an admin panel route protected by a specific email check

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker. The app uses Laravel Breeze for authentication.

TASK: Disable public user registration.

WHAT TO DO:
1. In `routes/auth.php`, comment out or remove the two registration routes:
   - GET /register → RegisteredUserController@create
   - POST /register → RegisteredUserController@store

2. In `resources/views/auth/login.blade.php`, remove the "Don't have an account? Sign up" link and its surrounding div (lines 88–92).

3. Add a catch-all redirect: if anyone navigates directly to /register, redirect them to /login with a session flash message:
   Route::get('/register', fn() => redirect()->route('login')->with('status', 'Account registration is by invitation only. Please contact your administrator.'))->name('register');

4. Verify the login page displays the session 'status' message (it already does via the @if(session('status')) block — confirm this is present).

5. Do NOT break any existing auth routes: login, logout, password reset, profile update must all continue to work.

TEST: Visit /register as a guest → should redirect to /login with the message.
TEST: Login with an existing account → should work normally.
```

**Dependencies:** None  
**Files Affected:** `routes/auth.php`, `resources/views/auth/login.blade.php`

---

### TICKET-002 · Add Global Scope to SapError Model
**Priority:** 🔴 MUST-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (30 minutes)

#### Description
The `Product` model has a global Eloquent scope that automatically filters all queries to the currently authenticated user's records. The `SapError` model does not — it relies on manual `where('user_id', auth()->id())` checks in each controller method. This is fragile: any future developer adding a new query could accidentally expose one user's SAP error records to another user.

#### Acceptance Criteria
- [ ] `SapError` model has a `booted()` method with a global scope identical in structure to the one on `Product`
- [ ] All existing SAP error queries (index, show, edit, update, destroy) continue to work correctly
- [ ] A logged-in user cannot access another user's SAP error record by guessing its URL (`/sap-errors/5`)
- [ ] The `authorizeError()` helper in `SapErrorController` is kept as a second layer of defense (do not remove it)
- [ ] No existing tests or routes are broken

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add a global Eloquent scope to the SapError model so that all database queries automatically filter by the currently authenticated user's ID.

WHAT TO DO:
1. Open `app/Models/SapError.php`

2. Add the `Illuminate\Database\Eloquent\Builder` import at the top.

3. Add a `booted()` static method to the model class:

protected static function booted(): void
{
    static::addGlobalScope('user', function (Builder $builder) {
        if (auth()->check()) {
            $builder->where('user_id', auth()->id());
        }
    });
}

4. Do NOT remove the existing `authorizeError()` method in `SapErrorController` — it stays as a backup check.

5. Verify that all existing controller methods (index, show, create, store, edit, update, destroy) still work. The global scope means you no longer need the manual `->where('user_id', auth()->id())` in each query, but leaving them in is also harmless — do not remove them unless you want to clean up.

TEST: Log in as User A. Note the ID of one of User A's SAP errors. Log in as User B. Navigate to /sap-errors/{that ID}. You should get a 404 (not a 403 or the actual record).
```

**Dependencies:** None  
**Files Affected:** `app/Models/SapError.php`

---

### TICKET-003 · Confirm and Enforce Production Environment Variables
**Priority:** 🔴 MUST-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (30 minutes — config, not code)

#### Description
Three environment variable settings are critical for security in production but are not enforced at the application level. If they are misconfigured on Heroku, the app will expose sensitive data or allow insecure connections.

#### Acceptance Criteria
- [ ] `APP_DEBUG=false` is confirmed in Heroku config vars — error pages never show stack traces, file paths, or database credentials to end users
- [ ] `APP_ENV=production` is set in Heroku config vars
- [ ] `SESSION_SECURE_COOKIE=true` is set — session cookies only travel over HTTPS
- [ ] `SESSION_ENCRYPT=true` is set — session data in the database is encrypted at rest
- [ ] Application boots without errors after these changes
- [ ] A custom error page exists for 500 errors that does not expose Laravel framework details

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker deployed on Heroku.

TASK: Create custom error pages for 403, 404, and 500 HTTP errors so that users see a branded, friendly error page instead of Laravel's default unstyled page.

WHAT TO DO:
1. Create the directory `resources/views/errors/` if it doesn't exist.

2. Create `resources/views/errors/403.blade.php`:
   - Extend layouts.dashboard if the user is authenticated, otherwise use a standalone HTML page
   - Show a clear message: "You don't have permission to access this page."
   - Include a link back to the dashboard: route('products.index')
   - Use the existing Kaiadmin styling (Bootstrap classes, card container)

3. Create `resources/views/errors/404.blade.php`:
   - Same structure as 403
   - Message: "The page you're looking for doesn't exist."
   - Include a "Go back to Dashboard" button

4. Create `resources/views/errors/500.blade.php`:
   - Standalone HTML page (do NOT extend dashboard — the app may not be functional)
   - Message: "Something went wrong on our end. Please try again or contact your administrator."
   - Simple, clean styling using inline CSS — no external CSS dependency
   - Do NOT include any error details, stack traces, or technical information

5. Add a note in the README or a comment in .env.example:
   - APP_DEBUG must be false in production
   - SESSION_SECURE_COOKIE=true
   - SESSION_ENCRYPT=true

Laravel automatically picks up views in resources/views/errors/ matching HTTP status codes.

TEST: Temporarily throw a 404 in a controller, verify the custom page renders. Then restore.
```

**Dependencies:** None  
**Files Affected:** `resources/views/errors/403.blade.php` (new), `resources/views/errors/404.blade.php` (new), `resources/views/errors/500.blade.php` (new)

---

### TICKET-004 · Make `user_id` Column Non-Nullable on Products Table
**Priority:** 🔴 MUST-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (45 minutes)

#### Description
The `user_id` foreign key on the `products` table was added with `->nullable()`. This means a product record could exist with no owner (`user_id = NULL`). Such records become invisible to everyone because the global scope filters `WHERE user_id = {logged-in user}`. These orphaned records cannot be recovered through the UI.

#### Acceptance Criteria
- [ ] The `user_id` column on the `products` table is `NOT NULL` in the database
- [ ] A migration is created that first assigns any existing `NULL` user_ids to a default value (the first user's ID), then alters the column to `NOT NULL`
- [ ] The migration is reversible (has a `down()` method)
- [ ] Existing data is not lost
- [ ] `ProductController@store` always sets `user_id` when creating a new product (verify this is already the case)

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Write a database migration that makes the `user_id` column on the `products` table NOT NULL.

WHAT TO DO:
1. Run: php artisan make:migration make_user_id_not_nullable_on_products_table

2. In the migration's `up()` method:
   a. First, fix any existing NULL user_ids:
      DB::statement("UPDATE products SET user_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) WHERE user_id IS NULL");
   b. Then, modify the column:
      Schema::table('products', function (Blueprint $table) {
          $table->unsignedBigInteger('user_id')->nullable(false)->change();
      });

3. In the `down()` method, reverse this:
      Schema::table('products', function (Blueprint $table) {
          $table->unsignedBigInteger('user_id')->nullable()->change();
      });

4. Require the `doctrine/dbal` package if not present (needed for ->change() in older setups):
   composer require doctrine/dbal

5. Run: php artisan migrate

6. Verify in the database that `user_id` column is NOT NULL.

IMPORTANT: Do not modify the Product model or any controllers. This is a database-only change.
```

**Dependencies:** None  
**Files Affected:** New migration file

---

### TICKET-005 · Replace Raw S3 Error Messages with Generic User Messages
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (1 hour)

#### Description
When an AWS S3 file upload fails, the raw exception message from the AWS SDK is currently displayed directly to the user. This message may contain internal infrastructure details (bucket name, region, AWS account information). In production, users should see a generic, actionable error message — while the full technical details are logged for developers.

#### Acceptance Criteria
- [ ] All S3 upload operations in `ProfileController` and `SapErrorController` use `Log::error()` to log the full exception message
- [ ] The user-facing error returned via `back()->withErrors()` says: "Image upload failed. Please try again. If the issue persists, contact your administrator."
- [ ] The same pattern is applied to S3 delete operations
- [ ] The `Log` facade is imported at the top of both controllers

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: In ProfileController and SapErrorController, replace raw S3 exception messages shown to users with generic messages, while logging the actual error for developers.

WHAT TO DO:

1. In both `app/Http/Controllers/ProfileController.php` and `app/Http/Controllers/SapErrorController.php`:
   - Add `use Illuminate\Support\Facades\Log;` at the top of the file if not already present.

2. Find every `catch` block that handles S3 exceptions (look for `try { ... Storage::disk('s3')... } catch (\Exception $e)`).

3. Replace the catch block pattern from:
   return back()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()]);
   
   With:
   Log::error('S3 upload failed for user ' . auth()->id() . ': ' . $e->getMessage(), [
       'user_id' => auth()->id(),
       'exception' => $e,
   ]);
   return back()->withErrors(['image' => 'Image upload failed. Please try again. If the issue persists, contact your administrator.'])->withInput();

4. Apply the same pattern to S3 delete operations in the destroy methods.

5. Do NOT change any other logic — only replace the user-visible error messages and add logging.

TEST: The application still correctly shows an error to the user when an upload fails, but the message is generic and reveals no infrastructure details.
```

**Dependencies:** None  
**Files Affected:** `app/Http/Controllers/ProfileController.php`, `app/Http/Controllers/SapErrorController.php`

---

### TICKET-006 · Configure Mail Service for Password Reset
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (30 minutes — config, not code)

#### Description
The password reset functionality (already built via Laravel Breeze) sends a reset link via email. Without a configured mail service, this link cannot be sent. Any user who forgets their password cannot self-recover and must contact a developer. This is a severe usability gap for a factory floor deployment.

#### Acceptance Criteria
- [ ] A mail service is configured in the production Heroku environment (Mailgun, Postmark, or SMTP)
- [ ] The `MAIL_*` environment variables are set in Heroku config vars
- [ ] Clicking "Forgot password?" → entering an email → a reset link arrives in the inbox within 60 seconds
- [ ] The reset link expires after 60 minutes (already configured)
- [ ] The reset email is sent from a Healthtek-owned email address (`no-reply@healthtek.com` or similar), not a generic domain

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker deployed on Heroku.

TASK: Configure the mail system so password reset emails are sent reliably.

WHAT TO DO:

1. Add the Mailgun driver to your Heroku config (recommended for reliability):
   heroku config:set MAIL_MAILER=mailgun
   heroku config:set MAILGUN_DOMAIN=your-mailgun-domain.com
   heroku config:set MAILGUN_SECRET=your-mailgun-api-key
   heroku config:set MAIL_FROM_ADDRESS=no-reply@healthtek.com
   heroku config:set MAIL_FROM_NAME="DocTracker - Healthtek"

   OR if using SMTP:
   heroku config:set MAIL_MAILER=smtp
   heroku config:set MAIL_HOST=smtp.mailgun.org
   heroku config:set MAIL_PORT=587
   heroku config:set MAIL_USERNAME=your-username
   heroku config:set MAIL_PASSWORD=your-password
   heroku config:set MAIL_ENCRYPTION=tls
   heroku config:set MAIL_FROM_ADDRESS=no-reply@healthtek.com
   heroku config:set MAIL_FROM_NAME="DocTracker - Healthtek"

2. In `config/mail.php`, verify the `from` fallback reads from env:
   'from' => ['address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'), 'name' => env('MAIL_FROM_NAME', 'DocTracker')]

3. Install Mailgun driver if needed: composer require symfony/mailgun-mailer symfony/http-client

4. Test in local .env first with MAIL_MAILER=log to confirm the password reset flow generates the right email content before connecting the real mail service.

TEST: Visit /forgot-password. Enter a real email address. Confirm the email arrives with a working reset link.
```

**Dependencies:** None  
**Files Affected:** `.env`, `config/mail.php`, Heroku config vars

---

### TICKET-007 · Whitelist Allowed User Preference Keys
**Priority:** 🟡 NICE-TO-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (30 minutes)

#### Description
`UserPreferenceController@update` accepts any string as a preference `key`. There is no validation of which keys are allowed. A user could submit `key=anything` and it would be stored in the `user_preferences` table. While this only affects the user's own data, it is a database hygiene issue.

#### Acceptance Criteria
- [ ] The `key` field in `UserPreferenceController@update` is validated against an allowlist of known keys
- [ ] Known valid keys: `hidden_suggestions`, `added_name`, `added_batch_no`, `added_stage`
- [ ] An invalid key returns a 422 validation error
- [ ] All existing autocomplete functionality continues to work unchanged

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add validation to UserPreferenceController@update to whitelist allowed preference keys.

WHAT TO DO:
1. Open `app/Http/Controllers/UserPreferenceController.php`

2. Change the validation rule for 'key' from:
   'key' => 'required|string',
   
   To:
   'key' => 'required|string|in:hidden_suggestions,added_name,added_batch_no,added_stage',

3. The 'value' validation stays the same: 'value' => 'nullable|array'

4. No other changes needed — the rest of the controller logic is correct.

TEST: Make a fetch() request to /preferences with key='hidden_suggestions' and value=['test'] — should return {success: true}.
TEST: Make a fetch() request with key='malicious_key' — should return a 422 validation error.
TEST: The autocomplete feature on /products/create still saves and restores hidden suggestions correctly.
```

**Dependencies:** None  
**Files Affected:** `app/Http/Controllers/UserPreferenceController.php`

---

### TICKET-008 · Add Try/Catch Around PDF Export Generation
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 1 — Pre-Launch  
**Estimated Complexity:** Low (1 hour)

#### Description
The PDF export function in `ProductController@exportDailyPdf` uses DomPDF (`barryvdh/laravel-dompdf`) to generate a downloadable PDF. If PDF generation fails (e.g., malformed product name with special characters, DomPDF memory limit), the user receives a raw 500 error with no useful message. This should be caught and shown as a user-friendly error.

#### Acceptance Criteria
- [ ] A `try/catch` block wraps the entire PDF generation and download response in `exportDailyPdf()`
- [ ] On failure, the user is redirected back to `/products/daily` with an error flash message: "PDF generation failed. Please try again or contact your administrator."
- [ ] The full exception is logged with `Log::error()`
- [ ] On success, the PDF downloads normally with the correct filename format

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add error handling around the PDF export in ProductController@exportDailyPdf.

WHAT TO DO:
1. Open `app/Http/Controllers/ProductController.php`

2. Find the `exportDailyPdf()` method.

3. Wrap the entire body of the method in a try/catch:

public function exportDailyPdf(Request $request)
{
    try {
        // ... existing code that builds the PDF ...
        // ... existing Pdf::loadView()->download() call ...
    } catch (\Exception $e) {
        Log::error('PDF export failed for user ' . auth()->id() . ': ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'exception' => $e,
        ]);
        return redirect()->route('products.daily')
            ->with('error', 'PDF generation failed. Please try again or contact your administrator.');
    }
}

4. Add `use Illuminate\Support\Facades\Log;` at the top if not already present.

5. Do NOT change any existing PDF generation logic inside the try block.

TEST: Temporarily throw an exception inside the try block. Visit /products/daily and click Download PDF. Confirm you are redirected to /products/daily with the error message. Then remove the temporary exception.
```

**Dependencies:** None  
**Files Affected:** `app/Http/Controllers/ProductController.php`

---

## Phase 2 — v1 Enhancements

These features improve the quality, stability, and usability of the existing shipped system. They do not require major architectural changes.

---

### TICKET-009 · Add Pagination to SAP Errors Index Page
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (1 hour)

#### Description
`SapErrorController@index` currently uses `->get()` which loads all SAP error records at once. As the knowledge base grows, this will cause slow page loads and eventually memory issues. The fix is to add pagination.

#### Acceptance Criteria
- [ ] SAP errors are paginated with 15 items per page
- [ ] Pagination controls are rendered at the bottom of the SAP errors index table
- [ ] Search/filter query parameters are preserved across page navigation
- [ ] The total count of errors is shown (e.g., "Showing 1–15 of 42 errors")

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add pagination to the SAP Errors index page.

WHAT TO DO:

1. Open `app/Http/Controllers/SapErrorController.php`, find the `index()` method.

2. Change the query from:
   $sapErrors = SapError::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
   
   To:
   $sapErrors = SapError::orderBy('created_at', 'desc')->paginate(15);
   (The global scope added in TICKET-002 handles the user_id filtering automatically.)

3. Open `resources/views/sap_errors/index.blade.php`.

4. After the closing </table> tag, add pagination links:
   <div class="mt-4 d-flex justify-content-center">
       {{ $sapErrors->links('pagination::bootstrap-5') }}
   </div>

5. If the view has a search/filter form, append request query params:
   {{ $sapErrors->appends(request()->query())->links('pagination::bootstrap-5') }}

6. Verify the view passes $sapErrors to the table loop using @foreach($sapErrors as $sapError).

TEST: If you have more than 15 SAP errors, pagination controls appear. Navigating pages works correctly.
```

**Dependencies:** TICKET-002 (global scope on SapError)  
**Files Affected:** `app/Http/Controllers/SapErrorController.php`, `resources/views/sap_errors/index.blade.php`

---

### TICKET-010 · Add Search to SAP Errors Index Page
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (2 hours)

#### Description
The SAP Errors knowledge base has no search functionality. As the list grows, users must manually scan through all entries to find a relevant error. A search by title and T-Code is the minimum needed for the knowledge base to remain useful.

#### Acceptance Criteria
- [ ] A search input field is present at the top of the SAP Errors index page
- [ ] Search filters by `title` (partial, case-insensitive) and `sap_tcode` (partial, case-insensitive) simultaneously
- [ ] Submitting the search form reloads the page with filtered results
- [ ] A "Clear" button resets the search
- [ ] The search input retains its value after submission
- [ ] Pagination works correctly when search is active

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add a search bar to the SAP Errors index page that filters by title and T-Code.

WHAT TO DO:

1. In `SapErrorController@index()`, add search query handling:

public function index(Request $request)
{
    $query = SapError::orderBy('created_at', 'desc');
    
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('sap_tcode', 'LIKE', "%{$search}%");
        });
    }
    
    $sapErrors = $query->paginate(15);
    return view('sap_errors.index', compact('sapErrors'));
}

2. In `resources/views/sap_errors/index.blade.php`, add a search form above the table:

<form action="{{ route('sap-errors.index') }}" method="GET" class="mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control" placeholder="Search by title or T-Code...">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Search</button>
            <a href="{{ route('sap-errors.index') }}" class="btn btn-secondary flex-grow-1">Clear</a>
        </div>
    </div>
</form>

3. Update pagination links to preserve search:
   {{ $sapErrors->appends(request()->query())->links('pagination::bootstrap-5') }}

TEST: Search for "MIGO" — only SAP errors with "MIGO" in the title or T-Code appear.
TEST: Clear search — all errors appear again.
TEST: Search + navigate pages — search is preserved across pages.
```

**Dependencies:** TICKET-009 (pagination must exist first)  
**Files Affected:** `app/Http/Controllers/SapErrorController.php`, `resources/views/sap_errors/index.blade.php`

---

### TICKET-011 · Extract Hardcoded Product Catalog to Database
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Medium (4–6 hours)

#### Description
The product name catalog (30+ product names with batch code mappings) is hardcoded inside `create.blade.php` as a JavaScript object (`productBatchMap`). Adding a new Healthtek product requires a developer to edit the code and redeploy the application. This should be moved to a database table so the catalog can be managed without code changes.

#### Acceptance Criteria
- [ ] A new `product_catalog` table exists with columns: `id`, `name`, `batch_prefix`, `type`, `timestamps`
- [ ] A seeder populates the table with the existing 30+ products from the hardcoded map
- [ ] `ProductController@create` fetches the catalog from the database and passes it to the view
- [ ] The autocomplete functionality on the create form uses database data (no hardcoded JS objects)
- [ ] The batch code auto-fill (selecting a name fills in the batch prefix) works from database data
- [ ] The hardcoded `productBatchMap` JS object in `create.blade.php` is removed

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Move the hardcoded product catalog from JavaScript in create.blade.php to a database table.

STEP 1 — Create Migration:
php artisan make:migration create_product_catalog_table

Migration schema:
Schema::create('product_catalog', function (Blueprint $table) {
    $table->id();
    $table->string('name');           // e.g., "Caricef 100mg Suspension"
    $table->string('batch_prefix')->nullable(); // e.g., "281P"
    $table->string('type')->nullable(); // Injection/Suspension/Tablet/Capsule
    $table->timestamps();
});

STEP 2 — Create Model:
php artisan make:model ProductCatalog

No special logic needed. Make it fillable for name, batch_prefix, type.

STEP 3 — Create Seeder:
php artisan make:seeder ProductCatalogSeeder

In the seeder, use DB::table('product_catalog')->insert([...]) to insert all products from the existing hardcoded list in create.blade.php. Copy all entries from the `productBatchMap` JavaScript object in that file.

STEP 4 — Update ProductController@create:
Add to the create() method:
$catalog = \App\Models\ProductCatalog::orderBy('name')->get(['name', 'batch_prefix']);
$productBatchMap = $catalog->pluck('batch_prefix', 'name')->toArray();
Pass both $catalog and $productBatchMap to the view.

STEP 5 — Update create.blade.php:
Replace the hardcoded JS:
const productBatchMap = { "Product A": "001P", ... };
With:
const productBatchMap = @json($productBatchMap);

And update the name autocomplete data source to use $catalog->pluck('name')->toArray() instead of the hardcoded names.

STEP 6 — Remove the hardcoded productBatchMap object from the blade file.

Run: php artisan db:seed --class=ProductCatalogSeeder

TEST: Add a new product to the product_catalog table directly in the database. Refresh the Add Document page. The new product should appear in the autocomplete dropdown.
```

**Dependencies:** None  
**Files Affected:** New migration, new model `ProductCatalog.php`, new seeder `ProductCatalogSeeder.php`, `app/Http/Controllers/ProductController.php`, `resources/views/products/create.blade.php`

---

### TICKET-012 · Add Account Lockout Email Notification
**Priority:** 🟡 NICE-TO-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (2 hours)

#### Description
When an account is locked due to 5 failed login attempts, the legitimate account owner is not notified. They only discover the lockout when they try to log in themselves. An email notification improves security awareness.

#### Acceptance Criteria
- [ ] When a `Lockout` event is fired, an email is sent to the affected account's email address
- [ ] The email body states: "Multiple failed login attempts were detected on your DocTracker account. Your account has been temporarily locked for 60 seconds. If this was not you, please contact your administrator."
- [ ] The email is sent asynchronously (queued) so it doesn't delay the login response
- [ ] If the email address does not exist in the system, no error is thrown

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Send an email notification to the user when their account is locked due to too many failed login attempts.

WHAT TO DO:

1. Create a Mailable:
php artisan make:mail AccountLockedMail --markdown=emails.account-locked

In AccountLockedMail.php, accept a $email string in the constructor. In the build method, use markdownMail view and set subject to "Security Alert: Your DocTracker Account Was Locked".

2. Create the markdown view at `resources/views/emails/account-locked.blade.php`:
@component('mail::message')
# Account Temporarily Locked

Multiple failed login attempts were detected on your **DocTracker** account.

Your account has been temporarily locked for **60 seconds** as a security measure.

If this was not you, please contact your system administrator immediately.

Thanks,
The DocTracker Team
@endcomponent

3. Create an event listener:
php artisan make:listener SendLockoutEmail --event=Illuminate\\Auth\\Events\\Lockout

In SendLockoutEmail, extract the email from the request:
$email = $event->request->input('email');
$user = \App\Models\User::where('email', $email)->first();
if ($user) {
    \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\AccountLockedMail($email));
}

4. Register the listener in `app/Providers/EventServiceProvider.php` (or `AppServiceProvider::boot()` in Laravel 11):
\Illuminate\Auth\Events\Lockout::class => [
    \App\Listeners\SendLockoutEmail::class,
],

5. Ensure a queue driver is configured (at minimum: QUEUE_CONNECTION=database) and the queue is running.

TEST: Fail login 5 times with the same email. Confirm the lockout email arrives in the inbox.
```

**Dependencies:** TICKET-006 (mail service must be configured)  
**Files Affected:** New Mailable, new listener, `app/Providers/EventServiceProvider.php`

---

### TICKET-013 · Add Explicit Warning Before Account Deletion
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (1 hour)

#### Description
The "Delete Account" button on the Profile page permanently deletes the user's account AND all their batch documents (due to `CASCADE ON DELETE`). The current confirmation step does not explicitly warn the user that all their documents, SAP errors, and preferences will be permanently deleted. This is a data loss risk.

#### Acceptance Criteria
- [ ] The delete account confirmation dialog includes an explicit warning: "This will permanently delete your account AND all X batch documents, Y SAP errors, and all your preferences. This action cannot be undone."
- [ ] The counts (X documents, Y SAP errors) are calculated dynamically before the confirmation
- [ ] The warning text is displayed in red/danger styling
- [ ] The "Delete Account" button in the dialog is clearly labeled as a destructive action

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Improve the account deletion warning to show the user exactly how much data they are about to permanently destroy.

WHAT TO DO:

1. Open `resources/views/profile/partials/delete-user-form.blade.php`.

2. Before the form, add PHP to count the user's data:
@php
    $docCount = \App\Models\Product::withTrashed()->where('user_id', auth()->id())->count();
    $sapCount = \App\Models\SapError::withoutGlobalScopes()->where('user_id', auth()->id())->count();
@endphp

3. Update the warning message displayed in the modal/confirmation to:
<div class="alert alert-danger">
    <strong>⚠️ This action is permanent and cannot be undone.</strong>
    <ul class="mb-0 mt-2">
        <li><strong>{{ $docCount }} batch documents</strong> (including deleted ones in the Recycle Bin)</li>
        <li><strong>{{ $sapCount }} SAP error records</strong> and their screenshots</li>
        <li>Your profile, avatar, and all preferences</li>
    </ul>
</div>

4. Update the confirm button to be styled as danger and clearly labeled:
   Change the label to: "Yes, permanently delete everything"
   Ensure it has the danger class (bg-red-600 or btn btn-danger)

5. Do NOT change the actual deletion logic — only modify the warning display.

TEST: Navigate to Profile → Delete Account. Confirm the modal shows the correct document and SAP error counts for the logged-in user.
```

**Dependencies:** None  
**Files Affected:** `resources/views/profile/partials/delete-user-form.blade.php`

---

### TICKET-014 · Inline Style Cleanup — Extract to Custom CSS
**Priority:** 🟡 NICE-TO-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (2 hours)

#### Description
Multiple views use inline `style=""` attributes for presentational rules. This makes styles impossible to override in a stylesheet and violates the principle of separation of concerns. Common inline styles should be extracted to `custom.css`.

#### Acceptance Criteria
- [ ] All inline `style="font-size: 0.85rem;"` instances removed and replaced with a utility class
- [ ] All `style="height: Npx;"` on progress bars replaced with named CSS classes (`.progress-table`, `.progress-large`, `.progress-slim`)
- [ ] The topbar stat badges use a named class instead of inline padding/border styles
- [ ] `custom.css` is updated with all new classes
- [ ] Visual appearance is unchanged

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Extract common inline styles from Blade views to named CSS classes in custom.css.

WHAT TO DO:

1. Open `public/Dashboard/assets/css/custom.css`. Add the following new utility classes:

/* Progress bar size variants */
.progress-slim { height: 8px; }
.progress-table { height: 20px; }
.progress-large { height: 30px; }

/* Topbar stat pill */
.topbar-stat-pill {
    display: flex;
    align-items: center;
    padding: 0.25rem 1rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
    font-weight: 700;
}

2. In `resources/views/layouts/dashboard.blade.php`, replace:
   <div class="d-flex align-items-center px-3 py-1 rounded bg-warning text-white">
       <span class="fw-bold" style="font-size: 0.85rem;">...
   
   With:
   <div class="topbar-stat-pill bg-warning text-white">
       <span>...

3. In `resources/views/products/index.blade.php`, replace:
   <div class="progress" style="height: 20px;">
   With:
   <div class="progress progress-table">

4. In `resources/views/products/pending.blade.php`, replace progress bar height inline styles similarly.

5. In `resources/views/products/show.blade.php`:
   Replace: <div class="progress progress-lg"> (and the inline CSS .progress-lg { height: 30px; })
   With: <div class="progress progress-large"> (defined in custom.css)
   Remove the @push('styles') block that defined .progress-lg if it's now covered.

6. Search all blade files for remaining `style="` attributes and document them — do not remove them all at once, only the ones with classes defined above.

TEST: Visual appearance is completely unchanged. Use browser dev tools to confirm the CSS classes are applied.
```

**Dependencies:** None  
**Files Affected:** `public/Dashboard/assets/css/custom.css`, multiple view files

---

### TICKET-015 · Add Document Clone Feature
**Priority:** 🟡 NICE-TO-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (2 hours)

#### Description
Operators frequently create the same batch document across multiple shifts with the same product name and type but a new batch number. Currently they must re-enter all fields from scratch. A "Clone" button that pre-populates the create form (except batch number) would save significant data entry time.

#### Acceptance Criteria
- [ ] A "Clone" button/link appears on the Document Detail page (`/products/{id}`)
- [ ] Clicking it redirects to `/products/create` with `?name=...&stage=...&type=...` query parameters (batch number intentionally excluded)
- [ ] The create form pre-fills from these query parameters
- [ ] The draft auto-save does not interfere with pre-filled clone data
- [ ] The original document is not modified in any way

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add a "Clone Document" button to the document detail page that opens the create form pre-filled with the same product details (minus batch number).

WHAT TO DO:

1. In `resources/views/products/show.blade.php`, find the action buttons section (card-action div).

2. Add a clone button link:
<a href="{{ route('products.create', ['name' => $product->name, 'stage' => $product->stage, 'type' => $product->type]) }}" 
   class="btn btn-info">
    <i class="fa fa-copy me-2"></i>Clone Document
</a>

3. In `resources/views/products/create.blade.php`, the first document row's inputs already use `value="{{ request('name') }}"` etc. — verify this is the case for name, stage, and type fields.

4. For the type select, update it to pre-select the cloned type:
   <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
   (This pattern is likely already in place — verify.)

5. In the JS draft restore logic, add a check:
   const isCloning = urlParams.has('name');
   if (!isCloning && localStorage.getItem('doc_tracker_draft')) { ... restore draft ... }
   
   (This check already exists — verify it's working correctly to skip draft restore when cloning.)

6. No controller changes needed — the create() method already passes data from request() to the view.

TEST: Navigate to any document detail page. Click Clone. The create form opens with the name, stage, and type pre-filled but batch number empty.
```

**Dependencies:** None  
**Files Affected:** `resources/views/products/show.blade.php`, `resources/views/products/create.blade.php`

---

### TICKET-016 · Add Submission Date Edit on Submitted Documents View
**Priority:** 🟠 SHOULD-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (2 hours)

#### Description
The `updateSubmissionDate` route already exists (`PATCH /products/{product}/submission-date`) but it is unclear if there is a UI for it on the submitted documents list. Supervisors sometimes need to correct a submission timestamp (e.g., when a document was submitted late due to a system issue). This UI should be accessible from the submitted view.

#### Acceptance Criteria
- [ ] Each row on the Submitted Documents page has an editable submission date field
- [ ] Clicking/activating the date field allows the supervisor to change the date
- [ ] Submitting the change calls `PATCH /products/{id}/submission-date` with the new date
- [ ] A success flash message confirms the change
- [ ] The edit is only available on submitted documents (not pending)

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add an inline editable submission date to each row in the submitted documents table.

WHAT TO DO:

1. Open `resources/views/products/submitted.blade.php`. Find the table row structure.

2. In the submission date column cell, replace the static text with an inline form:

<td>
    <form action="{{ route('products.updateSubmissionDate', $product) }}" method="POST" class="d-inline">
        @csrf
        @method('PATCH')
        <input type="date" name="submission_date" 
               value="{{ $product->submission_date?->format('Y-m-d') }}"
               class="form-control form-control-sm" 
               style="width: 140px; display: inline-block;"
               onchange="this.form.submit()"
               title="Click to change submission date">
    </form>
</td>

3. In `ProductController@updateSubmissionDate()`, verify the method:
   - Accepts `submission_date` from the request
   - Validates it as a valid date
   - Updates the product's `submission_date` field
   - Returns redirect back with a success message

4. If `updateSubmissionDate()` doesn't exist or is incomplete, implement it:
public function updateSubmissionDate(Request $request, Product $product)
{
    $request->validate(['submission_date' => 'required|date']);
    $product->update(['submission_date' => $request->submission_date]);
    return redirect()->back()->with('success', 'Submission date updated successfully.');
}

TEST: On the submitted documents page, change the date of one entry. Confirm the page reloads with a success message and the date is updated in the database.
```

**Dependencies:** None  
**Files Affected:** `resources/views/products/submitted.blade.php`, `app/Http/Controllers/ProductController.php`

---

### TICKET-017 · Replace CDN-Loaded Chart.js with npm Package
**Priority:** 🟡 NICE-TO-HAVE  
**Phase:** 2 — v1 Enhancement  
**Estimated Complexity:** Low (1 hour)

#### Description
Chart.js is loaded from `https://cdn.jsdelivr.net/npm/chart.js` on the All Documents page. This creates a dependency on an external CDN. If the CDN is unavailable, the analytics chart fails to render. It should be installed as an npm package and bundled with Vite.

#### Acceptance Criteria
- [ ] `chart.js` is installed as an npm dependency (`npm install chart.js`)
- [ ] Chart.js is imported in a dedicated JS file or in the `@push('scripts')` block using Vite's module system
- [ ] The CDN `<script>` tag in `index.blade.php` is removed
- [ ] The chart renders correctly without any CDN dependency
- [ ] No other pages are affected

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker. The app uses Vite as its build tool.

TASK: Replace the CDN-loaded Chart.js with an npm-installed version.

WHAT TO DO:

1. Install Chart.js:
   npm install chart.js

2. In `resources/views/products/index.blade.php`, find the @push('scripts') block.

3. Remove this line:
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

4. Change the script block to use Vite's module type:
   <script type="module">
       import Chart from '/resources/js/vendor/chart.js';
       // ... rest of chart initialization code ...
   </script>
   
   OR (simpler approach) create a dedicated Vite entry point:
   - Create `resources/js/charts.js` with:
     import Chart from 'chart.js/auto';
     window.Chart = Chart;
   
   - Add it to vite.config.js as an additional input
   - Load it with @vite(['resources/js/charts.js']) in the page head
   - Then use Chart as a global in the existing script block

5. The simplest approach for an existing jQuery-based app:
   In `resources/js/app.js`, add:
   import Chart from 'chart.js/auto';
   window.Chart = Chart;
   
   Then Chart is globally available on all pages via the existing @vite(['resources/js/app.js']) loaded in auth pages. BUT: dashboard pages don't load Vite. Instead, add a dedicated Vite import only for this page.

6. Run `npm run build` or `npm run dev` to compile.
   
   Update `dashboard.blade.php` to include the chart JS bundle only on the index page via @stack.

TEST: Turn off internet. Load /products. The chart still renders correctly.
```

**Dependencies:** None  
**Files Affected:** `package.json`, `resources/js/app.js` or new `resources/js/charts.js`, `resources/views/products/index.blade.php`

---

## Phase 3 — v2 New Features

These are deliberate deferrals from v1 scope. They are the next development cycle priorities. Design them after collecting user feedback from the deployed v1.

---

### TICKET-018 · Role-Based Access Control (RBAC) — Supervisor Role
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** High (2–3 days)

#### Description
Currently all users have identical permissions. The production team needs a Supervisor role who can see all operators' documents (not just their own), run cross-operator reports, and access aggregate analytics.

#### Acceptance Criteria
- [ ] A `role` column is added to the `users` table with values: `operator` (default), `supervisor`, `admin`
- [ ] A Supervisor can view all documents across all operators on a new "Team Overview" page
- [ ] A Supervisor can filter by operator name on the Team Overview page
- [ ] A Supervisor can export CSV across all operators
- [ ] Supervisors still cannot edit or delete other operators' documents
- [ ] Operators only see their own data (current behavior unchanged)
- [ ] A middleware class `EnsureUserHasRole` is created and applied to supervisor-only routes

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Implement a basic role-based access control system with two roles: 'operator' and 'supervisor'.

PHASE A — Database:
1. php artisan make:migration add_role_to_users_table
   Add: $table->string('role')->default('operator');
2. In User model, add a role helper: public function isSupervisor(): bool { return $this->role === 'supervisor'; }

PHASE B — Middleware:
1. php artisan make:middleware EnsureSupervisor
   In handle(): if (!auth()->user()->isSupervisor()) { abort(403); }
2. Register as 'supervisor' alias in bootstrap/app.php

PHASE C — Supervisor Overview Controller:
1. php artisan make:controller SupervisorController
2. Create index() method: fetches ALL products (no user scope — use Product::withoutGlobalScope('user'))
3. Supports filter by user_id (operator) and date range
4. Returns view with $products and $operators = User::where('role', 'operator')->get()

PHASE D — Routes:
Route::middleware(['auth', 'supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/overview', [SupervisorController::class, 'index'])->name('supervisor.overview');
    Route::get('/export', [SupervisorController::class, 'exportCsv'])->name('supervisor.export');
});

PHASE E — View:
Create resources/views/supervisor/overview.blade.php extending the dashboard layout.
Filter form: operator dropdown (all users), date range inputs, type selector.
Table: Document Name | Operator | Batch No | Type | Stage | Status | Progress

PHASE F — Sidebar:
In dashboard.blade.php, add a conditional nav section for supervisors:
@if(Auth::user()->isSupervisor())
<li class="nav-item ..."><a href="{{ route('supervisor.overview') }}">Team Overview</a></li>
@endif

TEST: Set a user's role to 'supervisor' in the database. Log in as that user. Navigate to /supervisor/overview. See documents from ALL operators. Confirm an operator cannot access /supervisor/overview (gets 403).
```

**Dependencies:** None (foundational for TICKET-019 to TICKET-022)  
**Files Affected:** New migration, `app/Models/User.php`, new middleware, new controller, new routes, new views, `dashboard.blade.php`

---

### TICKET-019 · Dynamic Product Catalog Admin Panel
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** Medium (1 day)

#### Description
After TICKET-011 moves the product catalog to a database, an admin UI is needed to manage the catalog without database access. Production supervisors need to add new products as Healthtek's product line grows.

#### Acceptance Criteria
- [ ] A Catalog Management page is accessible only to admins/supervisors
- [ ] Page shows all catalog entries in a table with Name, Batch Prefix, Type
- [ ] Admin can add a new product (form: name, batch_prefix, type)
- [ ] Admin can edit an existing catalog entry
- [ ] Admin can delete a catalog entry (with confirmation)
- [ ] Changes are reflected immediately in the autocomplete on the Create Document page

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

PREREQUISITE: TICKET-011 (product_catalog table) and TICKET-018 (RBAC) must be completed first.

TASK: Build a CRUD admin panel for managing the product catalog.

WHAT TO DO:
1. php artisan make:controller ProductCatalogController --resource

2. Implement all 7 resource methods (index, create, store, show, edit, update, destroy).
   - index: paginate(20) all catalog entries, pass to view
   - store/update: validate name (required, max 255), batch_prefix (nullable, max 50), type (nullable, in: Injection,Suspension,Tablet,Capsule)
   - destroy: delete + redirect with success message

3. Routes (supervisor/admin only):
   Route::middleware(['auth', 'supervisor'])->resource('catalog', ProductCatalogController::class);

4. Views — create resources/views/catalog/:
   - index.blade.php: table of entries + "Add Product" button + pagination
   - create.blade.php: form card with 3 inputs
   - edit.blade.php: pre-filled form card

5. Add to sidebar under supervisor section:
   <a href="{{ route('catalog.index') }}">Product Catalog</a>

TEST: Log in as supervisor. Add a new product "TestDrug 100mg Tablet" with batch "999P" and type "Tablet". Navigate to /products/create. Type "TestDrug" in the name field — it should appear in the autocomplete.
```

**Dependencies:** TICKET-011, TICKET-018  
**Files Affected:** New controller, new views, `routes/web.php`

---

### TICKET-020 · Full Audit Log — Field-Level Change History
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** High (2 days)

#### Description
For GMP compliance (and potential FDA 21 CFR Part 11 requirements), all changes to batch documents must be logged with: what changed, what the old value was, what the new value is, who made the change, and when. This creates a tamper-evident audit trail.

#### Acceptance Criteria
- [ ] Every create, update, and delete action on the `products` table creates an audit log entry
- [ ] Log entry records: `user_id`, `product_id`, `action` (created/updated/deleted/submitted/restored), `field_name`, `old_value`, `new_value`, `ip_address`, `timestamp`
- [ ] An audit log view is accessible to supervisors showing the change history for each document
- [ ] Log entries are immutable — no edit or delete route exists for audit records
- [ ] The `deleted_at` soft delete is also logged as an action

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

PREREQUISITE: TICKET-018 (RBAC) should be completed for audit log viewing.

TASK: Implement an immutable audit log for all changes to batch documents.

STEP 1 — Audit Log Table:
php artisan make:migration create_audit_logs_table

Schema:
$table->id();
$table->foreignId('user_id')->constrained();
$table->unsignedBigInteger('product_id');
$table->string('action');           // created, updated, deleted, submitted, restored
$table->string('field_name')->nullable();  // which field changed
$table->text('old_value')->nullable();
$table->text('new_value')->nullable();
$table->string('ip_address')->nullable();
$table->timestamps();

STEP 2 — AuditLog Model:
php artisan make:model AuditLog
No $fillable restrictions needed (internal use only).
Do NOT add soft deletes — audit logs must be permanent.

STEP 3 — AuditLogService:
Create app/Services/AuditLogService.php with a static log() method:
public static function log(string $action, Product $product, array $changes = []): void
{
    foreach ($changes as $field => [$old, $new]) {
        AuditLog::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'action' => $action,
            'field_name' => $field,
            'old_value' => $old,
            'new_value' => $new,
            'ip_address' => request()->ip(),
        ]);
    }
    if (empty($changes)) {
        AuditLog::create([...without field columns...]);
    }
}

STEP 4 — Hook into ProductController:
In store(): call AuditLogService::log('created', $product)
In update(): compare old/new values, call AuditLogService::log('updated', $product, $changes)
In submit(): AuditLogService::log('submitted', $product)
In destroy(): AuditLogService::log('deleted', $product)
In restore(): AuditLogService::log('restored', $product)

STEP 5 — Audit Log Viewer (supervisor only):
Route: GET /products/{product}/history → AuditLogController@show
View: table of log entries for that document, newest first

TEST: Update a document's remarks field. Check the audit_logs table — a record should exist with field_name='remarks', old_value=old text, new_value=new text.
```

**Dependencies:** TICKET-018 (for viewer access control)  
**Files Affected:** New migration, new model, new service, `ProductController.php`, new audit view

---

### TICKET-021 · Automated Pending Document Alerts
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** Medium (1–2 days)

#### Description
Documents that remain pending for more than a configurable number of hours represent potential production delays. A scheduled job should alert the document owner (and optionally a supervisor) when a document has been stuck in "pending" status for too long.

#### Acceptance Criteria
- [ ] A scheduled Artisan command checks for pending documents older than 8 hours (configurable)
- [ ] For each stale document, an email is sent to the owner with: document name, batch number, how long it's been pending, a direct link to the document
- [ ] The command runs on a schedule (e.g., every 2 hours during work hours: 8am–6pm)
- [ ] The schedule is defined in `routes/console.php`
- [ ] Alerts are not sent for documents created outside of working hours

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

PREREQUISITE: TICKET-006 (mail service must be configured).

TASK: Create a scheduled job that alerts users about documents that have been pending for more than 8 hours.

STEP 1 — Artisan Command:
php artisan make:command AlertStalePendingDocuments

In handle():
$threshold = now()->subHours(8);
$staleProducts = Product::withoutGlobalScope('user')
    ->where('status', 'pending')
    ->where('created_at', '<', $threshold)
    ->with('user')
    ->get();

foreach ($staleProducts as $product) {
    Mail::to($product->user->email)->queue(new StalePendingDocumentMail($product));
}

STEP 2 — Mailable:
php artisan make:mail StalePendingDocumentMail --markdown=emails.stale-pending

Accept $product in constructor. Email subject: "Action Required: Pending Document Needs Attention".
Email body: document name, batch number, time pending (use Carbon::parse($product->created_at)->diffForHumans()), link to document.

STEP 3 — Schedule in routes/console.php:
Schedule::command('documents:alert-stale')
    ->everyTwoHours()
    ->between('08:00', '18:00')
    ->weekdays();

STEP 4 — Heroku Scheduler:
Add Heroku Scheduler add-on and configure: `php artisan schedule:run` every 10 minutes.

TEST: Create a document and manually set its created_at to 9 hours ago in the database. Run `php artisan documents:alert-stale`. Confirm the email is sent to the document owner.
```

**Dependencies:** TICKET-006, TICKET-018  
**Files Affected:** New command, new mailable, `routes/console.php`

---

### TICKET-022 · Supervisor Team Analytics Dashboard
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** Medium (2 days)

#### Description
The current analytics are personal (per user, last 7 days). Supervisors need aggregate analytics across the whole team: total documents by operator, submission rate, average time from creation to submission, and batch completion by product type.

#### Acceptance Criteria
- [ ] A dedicated Supervisor Analytics page shows team-level metrics
- [ ] Metrics shown: total pending vs. submitted by each operator (table), submission trend by day (last 30 days), breakdown by product type (Injection/Suspension/Tablet/Capsule)
- [ ] Date range filter is available
- [ ] All charts use Chart.js (consistent with existing dashboard)
- [ ] Page is only accessible to users with the supervisor role

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

PREREQUISITE: TICKET-018 (RBAC).

TASK: Build a Supervisor Analytics page with team-level metrics.

WHAT TO DO:

1. Add a new method to SupervisorController: analyticsIndex()

2. Queries (all without user global scope):
   a. By Operator:
      $byOperator = Product::withoutGlobalScope('user')
          ->selectRaw('user_id, status, COUNT(*) as count')
          ->groupBy('user_id', 'status')
          ->with('user:id,name')
          ->get();
   
   b. 30-day trend:
      $trend = Product::withoutGlobalScope('user')
          ->where('status', 'submitted')
          ->where('submission_date', '>=', now()->subDays(30))
          ->selectRaw('DATE(submission_date) as date, COUNT(*) as count')
          ->groupBy('date')
          ->orderBy('date')
          ->get();
   
   c. By Type:
      $byType = Product::withoutGlobalScope('user')
          ->selectRaw('type, status, COUNT(*) as count')
          ->groupBy('type', 'status')
          ->get();

3. Route: GET /supervisor/analytics → SupervisorController@analyticsIndex → name('supervisor.analytics')

4. View: resources/views/supervisor/analytics.blade.php
   - Summary cards row: Total Pending (all operators), Total Submitted, Active Operators
   - Chart 1: Line chart — 30-day submission trend (Chart.js)
   - Chart 2: Doughnut chart — breakdown by product type (Chart.js)
   - Table: One row per operator, columns: Name | Pending | Submitted | Total | Submission Rate %

5. Add to supervisor sidebar section: Analytics link.

TEST: Log in as supervisor. Navigate to /supervisor/analytics. Confirm the operator table shows all users and their document counts. Confirm charts render.
```

**Dependencies:** TICKET-018  
**Files Affected:** `SupervisorController.php`, new view, `routes/web.php`

---

### TICKET-023 · Electronic Signature on Document Submission
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** High (2–3 days)

#### Description
For FDA 21 CFR Part 11 and GMP compliance, electronic signatures on document submissions may be required. An e-signature means the submitter must re-confirm their identity (by entering their password) at the moment of submission. This creates a cryptographically tied, non-repudiable approval record.

#### Acceptance Criteria
- [ ] A password re-entry step is required before submitting any document
- [ ] The signature is stored as: `user_id`, `product_id`, `signed_at`, `ip_address`, `user_agent`, and a hash of the document state at signature time
- [ ] The signature record is immutable (no edit or delete route)
- [ ] The document detail page shows "Electronically signed by [Name] on [Date] at [Time]" after submission
- [ ] An incorrect password at signature time shows an error and does not submit the document

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add an electronic signature step to document submission that requires the user to confirm their identity with their password.

STEP 1 — E-Signature Table:
php artisan make:migration create_electronic_signatures_table

Schema:
$table->id();
$table->foreignId('user_id')->constrained();
$table->foreignId('product_id')->constrained();
$table->string('document_hash'); // SHA-256 of document state at signing
$table->string('ip_address');
$table->string('user_agent');
$table->timestamp('signed_at');

STEP 2 — Update Submit Modal:
In show.blade.php, update the submitModal form to add a password field:
<div class="form-group mt-3">
    <label for="signature_password">Confirm your identity — enter your password to sign:</label>
    <input type="password" class="form-control" name="signature_password" id="signature_password" required>
</div>

STEP 3 — Update ProductController@submit():
public function submit(Request $request, Product $product)
{
    // Verify password
    if (!Hash::check($request->signature_password, auth()->user()->password)) {
        return redirect()->back()->withErrors(['signature_password' => 'Incorrect password. Submission cancelled.']);
    }
    
    // Create document hash
    $documentState = json_encode($product->only(['name', 'batch_no', 'stage', 'type', 'line_clearance', 'review', 'confirmation', 'remarks']));
    $hash = hash('sha256', $documentState);
    
    // Record e-signature
    ElectronicSignature::create([
        'user_id' => auth()->id(),
        'product_id' => $product->id,
        'document_hash' => $hash,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'signed_at' => now(),
    ]);
    
    // Continue with existing submission logic ...
}

STEP 4 — Display Signature on Detail Page:
In show.blade.php, after the status section:
@if($product->signature)
<div class="alert alert-success">
    <i class="fas fa-signature me-2"></i>
    Electronically signed by <strong>{{ $product->signature->user->name }}</strong>
    on {{ $product->signature->signed_at->format('M d, Y') }} at {{ $product->signature->signed_at->format('h:i A') }}
</div>
@endif

TEST: Submit a document. Verify the electronic_signatures table has a new record. Verify the detail page shows the signature block. Try submitting with wrong password — confirm submission is rejected.
```

**Dependencies:** TICKET-020 (audit log pattern to follow)  
**Files Affected:** New migration, `ProductController.php`, `resources/views/products/show.blade.php`

---

### TICKET-024 · Document Assignment to Specific Operators
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** Medium (2 days)

#### Description
Currently each operator manages only documents they personally created. In a larger team, supervisors may need to create a document and assign it to a specific operator, or transfer ownership of a document between operators.

#### Acceptance Criteria
- [ ] An `assigned_to` column is added to the `products` table (nullable FK to `users.id`)
- [ ] Supervisors can assign a document to any operator when creating or editing it
- [ ] Assigned operators see assigned documents in their Pending/All Documents views (not just self-created)
- [ ] A document's page shows "Assigned to: [Operator Name]" when assigned
- [ ] Assignment changes are recorded in the audit log

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

PREREQUISITE: TICKET-018 (RBAC), TICKET-020 (audit log).

TASK: Add document assignment functionality so supervisors can assign documents to specific operators.

STEP 1 — Migration:
Add nullable assigned_to FK to products:
$table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

STEP 2 — Update Global Scope on Product:
Change from: WHERE user_id = auth()->id()
To: WHERE user_id = auth()->id() OR assigned_to = auth()->id()

static::addGlobalScope('user', function (Builder $builder) {
    if (auth()->check()) {
        $id = auth()->id();
        $builder->where(function($q) use ($id) {
            $q->where('user_id', $id)->orWhere('assigned_to', $id);
        });
    }
});

STEP 3 — Update Create/Edit Forms (supervisors only):
Add an operator dropdown to the create and edit forms, visible only to supervisors:
@if(Auth::user()->isSupervisor())
<div class="form-group">
    <label>Assign to Operator</label>
    <select name="assigned_to" class="form-control form-select">
        <option value="">Self (unassigned)</option>
        @foreach($operators as $operator)
            <option value="{{ $operator->id }}" {{ old('assigned_to') == $operator->id ? 'selected' : '' }}>{{ $operator->name }}</option>
        @endforeach
    </select>
</div>
@endif

STEP 4 — Update Controller:
Pass $operators = User::where('role', 'operator')->get() to create/edit views.
In store() and update(), handle the assigned_to field (only allow supervisors to set it).

STEP 5 — Display assignment on detail page:
@if($product->assignedTo)
<div class="mb-3">
    <label class="form-label text-muted">Assigned To</label>
    <div class="fw-bold">{{ $product->assignedTo->name }}</div>
</div>
@endif

TEST: Log in as supervisor. Create a document and assign it to Operator B. Log out. Log in as Operator B. Confirm the document appears in Operator B's Pending Documents list.
```

**Dependencies:** TICKET-018, TICKET-020  
**Files Affected:** New migration, `Product.php` (global scope update), `ProductController.php`, create/edit views, show view

---

### TICKET-025 · In-App Notification System
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** High (2–3 days)

#### Description
When a supervisor assigns a document to an operator, or when a document is near a deadline, the operator should receive an in-app notification (visible in the navbar) rather than only email alerts.

#### Acceptance Criteria
- [ ] A notification bell icon appears in the top navbar with an unread count badge
- [ ] Clicking the bell shows a dropdown of recent notifications (last 10)
- [ ] Unread notifications are marked with a dot/highlight
- [ ] Clicking a notification marks it as read and navigates to the relevant document
- [ ] Notifications are created programmatically (Laravel's built-in Notification system)
- [ ] The unread count updates without a full page reload (polling every 60 seconds via fetch())

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add an in-app notification system using Laravel's built-in database notification channel.

STEP 1 — Setup:
php artisan notifications:table
php artisan migrate

STEP 2 — Create a Notification class:
php artisan make:notification DocumentAssignedNotification

In the class, implement via('database') channel.
toDatabase() returns:
[
    'product_id' => $this->product->id,
    'product_name' => $this->product->name,
    'message' => "A document has been assigned to you: {$this->product->name} (Batch: {$this->product->batch_no})",
    'url' => route('products.show', $this->product),
]

STEP 3 — Send notification on document assignment:
In ProductController@store(), after saving with assigned_to:
if ($product->assigned_to && $product->assigned_to !== auth()->id()) {
    $product->assignedTo->notify(new DocumentAssignedNotification($product));
}

STEP 4 — Notification API Endpoint:
Route::get('/notifications', fn() => auth()->user()->unreadNotifications->take(10))->name('notifications.index');
Route::post('/notifications/{id}/read', fn($id) => auth()->user()->notifications()->find($id)?->markAsRead())->name('notifications.read');

STEP 5 — Navbar Bell Icon (dashboard.blade.php):
Add before the user dropdown:
<li class="nav-item dropdown" id="notificationBell">
    <a class="nav-link" href="#" data-bs-toggle="dropdown">
        <i class="fas fa-bell"></i>
        <span class="badge bg-danger" id="notifCount" style="display:none;"></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" id="notifDropdown" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
        <!-- Populated by JS -->
    </ul>
</li>

STEP 6 — Polling JS (add to dashboard.blade.php bottom scripts):
function loadNotifications() {
    fetch('/notifications')
        .then(r => r.json())
        .then(data => {
            const count = data.length;
            const badge = document.getElementById('notifCount');
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
            // populate dropdown...
        });
}
loadNotifications();
setInterval(loadNotifications, 60000); // poll every 60s

TEST: Assign a document to an operator. Log in as that operator. Within 60 seconds, the bell icon shows a red badge with count 1. Click the bell — see the notification. Click the notification — navigate to the document — notification marked as read — badge disappears.
```

**Dependencies:** TICKET-024 (assignments to trigger notifications), TICKET-018 (roles)  
**Files Affected:** New notification class, `ProductController.php`, `dashboard.blade.php`, `routes/web.php`

---

### TICKET-026 · Barcode/QR Scan for Physical Document Tagging
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** High (3+ days — requires hardware consideration)

#### Description
Physical batch documents in a pharmaceutical facility have paper records. Printing a QR code on each batch document and scanning it to instantly pull up the digital record in DocTracker would bridge the physical-digital gap and significantly speed up supervisor spot-checks.

#### Acceptance Criteria
- [ ] Each submitted document detail page has a "Print QR Code" button
- [ ] Clicking generates a QR code image linking to `/{product_id}` (authenticated URL)
- [ ] Scanning the QR code on a mobile device opens the document detail page (requires login if not authenticated)
- [ ] The QR code can be printed from the browser
- [ ] QR code is generated client-side using a JavaScript library (no external API)

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

TASK: Add QR code generation for each batch document so supervisors can scan physical documents to pull up their digital record.

STEP 1 — Install QR Code library (client-side):
Use the qrcode.js library from npm:
npm install qrcode

Or use CDN in the view: <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>

STEP 2 — Add a QR code section to show.blade.php:
@if($product->isSubmitted())
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Document QR Code</h4>
    </div>
    <div class="card-body text-center">
        <canvas id="qrCodeCanvas"></canvas>
        <div class="mt-3">
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print QR Code
            </button>
        </div>
        <p class="text-muted small mt-2">Scan to access this document on any device.</p>
    </div>
</div>
@endif

STEP 3 — JS to generate QR code:
@push('scripts')
<script>
const documentUrl = "{{ route('products.show', $product) }}";
QRCode.toCanvas(document.getElementById('qrCodeCanvas'), documentUrl, { width: 200 }, function(error) {
    if (error) console.error(error);
});
</script>
@endpush

STEP 4 — Print stylesheet (optional):
Add a @media print CSS rule that hides the sidebar, navbar, and all action buttons — showing only the document title, QR code, and key fields.

TEST: Navigate to any submitted document. Confirm the QR code canvas renders. Scan with a phone — confirm it navigates to the correct document URL.
```

**Dependencies:** None  
**Files Affected:** `resources/views/products/show.blade.php`, `package.json` (or CDN)

---

### TICKET-027 · Multi-Facility / Multi-Tenant Support
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** Very High (1 week+)

#### Description
If Healthtek expands to multiple production facilities, each facility would need isolated data, separate user pools, and potentially separate product catalogs. This requires converting DocTracker from a single-tenant to a multi-tenant architecture.

#### Acceptance Criteria
- [ ] A `facilities` table exists with: name, code, address, timezone
- [ ] A `facility_id` FK is added to: `users`, `products`, `sap_errors`, `product_catalog`
- [ ] All global scopes filter by both `user_id` AND `facility_id`
- [ ] A super-admin can create facilities and assign users to facilities
- [ ] Users cannot see data from other facilities
- [ ] Each facility's product catalog is independent

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

IMPORTANT: This is a major architectural change. Read through the entire plan before starting. Back up the database before running any migrations.

TASK: Add multi-facility support by introducing a facilities table and tenant-scoping all data.

STEP 1 — Facilities Table:
php artisan make:model Facility -mf

Migration schema:
$table->id();
$table->string('name');
$table->string('code', 20)->unique(); // e.g., 'HTK-KHI', 'HTK-LHR'
$table->string('timezone')->default('Asia/Karachi');
$table->timestamps();

STEP 2 — Add facility_id to users:
php artisan make:migration add_facility_id_to_users_table
$table->foreignId('facility_id')->nullable()->constrained()->onDelete('set null');

STEP 3 — Add facility_id to products and sap_errors:
Similar migrations for both tables. 
Backfill: assign all existing records to a default "Facility 1".

STEP 4 — Update Global Scopes:
In Product model and SapError model, add facility_id to the scope:
$builder->where('user_id', auth()->id())
        ->where('facility_id', auth()->user()->facility_id);

STEP 5 — Middleware:
Create EnsureFacilityAssigned middleware:
If auth()->user()->facility_id is null → redirect to an "Account Setup" page.

STEP 6 — Super Admin Panel (separate from supervisor):
Routes under /admin prefix, requiring a 'super_admin' role.
Manage facilities (CRUD), assign users to facilities.

TEST: Create two facilities. Assign User A to Facility 1, User B to Facility 2. Create documents as User A. Log in as User B. Confirm User B sees zero documents. Assign User B to Facility 1 — User B now sees Facility 1 documents.
```

**Dependencies:** TICKET-018 (RBAC roles must exist first)  
**Files Affected:** New model/migration/seeder for Facility, multiple model scope updates, new admin panel

---

### TICKET-028 · SAP / ERP Bi-Directional Sync API
**Priority:** 🔵 V2  
**Phase:** 3 — v2 Feature  
**Estimated Complexity:** Very High (1–2 weeks — requires SAP access)

#### Description
DocTracker currently operates as a standalone tracking layer on top of SAP. A future phase would allow DocTracker to sync batch data from SAP (so operators don't need to manually enter what's already in SAP) and push submission confirmations back to SAP.

#### Acceptance Criteria
- [ ] A secure API endpoint exists for SAP to push batch creation events to DocTracker
- [ ] When SAP creates a new batch, DocTracker automatically creates the corresponding pending document
- [ ] A webhook or scheduled job can push submitted document confirmation back to SAP
- [ ] API authentication uses API tokens (not session cookies)
- [ ] All SAP sync events are logged in a dedicated `sync_logs` table

#### AI Coding Prompt
```
You are working on a Laravel 11 application called DocTracker.

PREREQUISITE: This requires SAP API access credentials and a defined data contract with the SAP team.

TASK: Create a secure inbound API endpoint that SAP can call to push batch creation data into DocTracker.

STEP 1 — API Token Authentication:
Install Laravel Sanctum: composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

Create a dedicated SAP API user in the database and generate a token for it.

STEP 2 — API Routes (routes/api.php):
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/batches', [SapSyncController::class, 'receiveBatch']);
    Route::get('/batches/{product}/status', [SapSyncController::class, 'getBatchStatus']);
});

STEP 3 — SapSyncController:
php artisan make:controller Api/SapSyncController

receiveBatch() accepts:
{
    "batch_no": "H-2501",
    "product_name": "Caricef 100mg Suspension",
    "stage": "Pre-Line Clearance",
    "type": "Suspension",
    "assigned_user_email": "operator@healthtek.com"
}

Validates input, finds or creates the assigned user by email, creates a Product record with status=pending.
Returns: { "success": true, "product_id": 123 }

STEP 4 — Sync Log Table:
php artisan make:migration create_sync_logs_table
Columns: id, direction (inbound/outbound), sap_batch_no, product_id, payload (JSON), status (success/failure), error_message, timestamps

STEP 5 — Outbound Webhook (optional):
When a document is submitted, trigger an outbound call to SAP's webhook URL:
Http::withToken(config('services.sap.api_key'))
    ->post(config('services.sap.webhook_url'), ['batch_no' => $product->batch_no, 'status' => 'submitted', 'submitted_at' => $product->submission_date]);

TEST: Use Postman or curl to POST to /api/v1/batches with a Bearer token and a sample batch payload. Confirm a new pending product is created in the database.
```

**Dependencies:** TICKET-018 (assign to operators), SAP API credentials  
**Files Affected:** New API controller, `routes/api.php`, Sanctum installation, new sync log migration

---

## Ticket Summary Table

| Ticket | Name | Phase | Priority | Complexity |
|--------|------|-------|----------|-----------|
| TICKET-001 | Disable Public Registration | 1 | 🔴 Must-Have | Low |
| TICKET-002 | SapError Global Scope | 1 | 🔴 Must-Have | Low |
| TICKET-003 | Production Env Vars & Custom Error Pages | 1 | 🔴 Must-Have | Low |
| TICKET-004 | user_id NOT NULL on Products | 1 | 🔴 Must-Have | Low |
| TICKET-005 | Generic S3 Error Messages | 1 | 🟠 Should-Have | Low |
| TICKET-006 | Mail Service for Password Reset | 1 | 🟠 Should-Have | Low |
| TICKET-007 | Whitelist Preference Keys | 1 | 🟡 Nice-to-Have | Low |
| TICKET-008 | PDF Export Error Handling | 1 | 🟠 Should-Have | Low |
| TICKET-009 | SAP Errors Pagination | 2 | 🟠 Should-Have | Low |
| TICKET-010 | SAP Errors Search | 2 | 🟠 Should-Have | Low |
| TICKET-011 | Product Catalog to Database | 2 | 🟠 Should-Have | Medium |
| TICKET-012 | Account Lockout Email | 2 | 🟡 Nice-to-Have | Low |
| TICKET-013 | Account Deletion Warning | 2 | 🟠 Should-Have | Low |
| TICKET-014 | Inline Style Cleanup | 2 | 🟡 Nice-to-Have | Low |
| TICKET-015 | Document Clone Feature | 2 | 🟡 Nice-to-Have | Low |
| TICKET-016 | Submission Date Edit UI | 2 | 🟠 Should-Have | Low |
| TICKET-017 | Chart.js from npm | 2 | 🟡 Nice-to-Have | Low |
| TICKET-018 | RBAC — Supervisor Role | 3 | 🔵 V2 | High |
| TICKET-019 | Product Catalog Admin Panel | 3 | 🔵 V2 | Medium |
| TICKET-020 | Audit Log (Field-Level) | 3 | 🔵 V2 | High |
| TICKET-021 | Automated Pending Alerts | 3 | 🔵 V2 | Medium |
| TICKET-022 | Supervisor Team Analytics | 3 | 🔵 V2 | Medium |
| TICKET-023 | Electronic Signature | 3 | 🔵 V2 | High |
| TICKET-024 | Document Assignment | 3 | 🔵 V2 | Medium |
| TICKET-025 | In-App Notifications | 3 | 🔵 V2 | High |
| TICKET-026 | QR Code Scan | 3 | 🔵 V2 | High |
| TICKET-027 | Multi-Facility Support | 3 | 🔵 V2 | Very High |
| TICKET-028 | SAP/ERP API Sync | 3 | 🔵 V2 | Very High |

---

## Recommended Sprint Plan

### Sprint 0 (Pre-Launch — 1 week)
TICKET-001, 002, 003, 004, 005, 006, 008  
**Goal:** App is safe for production use by Healthtek employees.

### Sprint 1 (Stabilization — 2 weeks after launch)
TICKET-007, 009, 010, 013, 015, 016  
**Goal:** Core workflows are polished; knowledge base is usable at scale.

### Sprint 2 (Catalog & Quality — 2 weeks)
TICKET-011, 012, 014, 017  
**Goal:** Product data is database-driven; tech debt reduced.

### Sprint 3 (v2 Foundation — 3 weeks)
TICKET-018, 019, 020  
**Goal:** RBAC and audit log in place; foundational for all v2 features.

### Sprint 4–5 (v2 Features — 4–6 weeks)
TICKET-021, 022, 023, 024, 025  
**Goal:** Full supervisor workflow, compliance features, and team collaboration.

### Sprint 6+ (Advanced)
TICKET-026, 027, 028  
**Goal:** Hardware integration, scale, and ERP connectivity.

---

*Document prepared from codebase audit, PRD, Security Audit, and Technical Architecture Document for DocTracker v1 (Laravel 11, June 2026 build).*
