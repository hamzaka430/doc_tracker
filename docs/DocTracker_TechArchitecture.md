# 🏗️ Technical Architecture Document — DocTracker
**Client:** Healthtek Pvt Ltd  
**Author:** Software Architecture Review  
**Version:** 1.0 (Based on live codebase — Laravel 11, June 2026)  
**Stack:** PHP 8.2 · Laravel 11 · MySQL · AWS S3 · Heroku  

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack — With Rationale](#2-technology-stack)
3. [File & Folder Structure](#3-file--folder-structure)
4. [Database Schema](#4-database-schema)
5. [Entity Relationships](#5-entity-relationships)
6. [Request Lifecycle](#6-request-lifecycle)
7. [Storage Architecture](#7-storage-architecture)
8. [Environment Variables Reference](#8-environment-variables-reference)
9. [Architectural Observations & Recommendations](#9-architectural-observations--recommendations)

---

## 1. System Overview

DocTracker is a **monolithic, server-rendered web application** following the MVC (Model-View-Controller) pattern. All logic lives in a single Laravel 11 codebase — there is no separate API backend or frontend SPA.

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT BROWSER                          │
│              (Blade-rendered HTML + Alpine.js + CSS)            │
└────────────────────────────┬────────────────────────────────────┘
                             │ HTTPS
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    HEROKU DYNO (Web Process)                    │
│                                                                 │
│   ┌─────────────┐   ┌──────────────┐   ┌──────────────────┐   │
│   │  Middleware  │──▶│  Controller  │──▶│   Blade View     │   │
│   │  (Auth,CSRF) │   │  (Business   │   │  (HTML Response) │   │
│   └─────────────┘   │   Logic)     │   └──────────────────┘   │
│                      └──────┬───────┘                          │
│                             │                                   │
│                      ┌──────▼───────┐                          │
│                      │  Eloquent    │                          │
│                      │    ORM       │                          │
│                      └──────┬───────┘                          │
└─────────────────────────────┼───────────────────────────────────┘
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
     ┌────────────┐   ┌───────────┐   ┌───────────────┐
     │   MySQL    │   │  AWS S3   │   │ Laravel Cache │
     │  Database  │   │  Bucket   │   │  (File-based) │
     │(JawsDB on  │   │ (Avatars, │   └───────────────┘
     │  Heroku)   │   │ Screenshots│
     └────────────┘   └───────────┘
```

**Architecture pattern:** Traditional MVC monolith  
**Rendering:** Server-side (Blade templates, no SPA)  
**Session handling:** Database-backed sessions  
**Auth mechanism:** Session cookies (Laravel Breeze scaffolding)

---

## 2. Technology Stack

### 2.1 Backend

| Technology | Version | Why This Choice |
|-----------|---------|----------------|
| **PHP** | 8.2+ | Minimum required by Laravel 11; brings fibers, named arguments, readonly properties, and performance improvements over PHP 8.0 |
| **Laravel 11** | ^11.31 | Battle-tested PHP framework with built-in auth scaffolding (Breeze), ORM (Eloquent), PDF generation support, queue system, and strong community. Ideal for rapid internal-tool development. Laravel 11 ships with a slimmed-down directory structure and improved performance |
| **Laravel Breeze** | ^2.3 | Lightweight authentication scaffolding — provides login, registration, password reset, and profile management out of the box with minimal opinionation. Preferred over Jetstream (too heavy) or manual auth (too slow) |
| **barryvdh/laravel-dompdf** | ^3.1 | Industry-standard PDF generation for Laravel. Used to render grouped batch document lists as downloadable PDFs. DomPDF converts Blade views → HTML → PDF, which means templates are reused |
| **league/flysystem-aws-s3-v3** | 3.0 | Laravel's filesystem abstraction layer for AWS S3. Enables `Storage::disk('s3')->put()` calls to work identically to local disk calls — clean, testable interface |
| **Carbon** | (bundled with Laravel) | Date/time manipulation for submission timestamps, 7-day chart range calculations, and the 6-hour editability window logic |

### 2.2 Frontend

| Technology | Version | Why This Choice |
|-----------|---------|----------------|
| **Blade Templates** | (Laravel built-in) | Server-rendered HTML directly from Laravel. No build step for templates, no hydration complexity. Perfect for a form-heavy internal tool where SEO is irrelevant and interactivity is light |
| **Alpine.js** | ^3.4.2 | Minimal JavaScript framework (8KB) for reactive UI elements without the overhead of Vue or React. Used for toggling dropdowns, modal state, and dynamic form rows — exactly the use case it was designed for |
| **TailwindCSS** | ^3.1.0 | Utility-first CSS framework. Allows rapid custom UI without fighting pre-designed component aesthetics. Works cleanly with Blade partials |
| **Bootstrap 5** | (via CDN) | Used alongside Tailwind for specific components (cards, form layouts). The dual-framework approach is a code debt item — see §9 |
| **Font Awesome 6** | (via CDN) | Icon library for navigation icons, action buttons, and status indicators |
| **Vite** | ^5.0 | Modern, fast asset bundler. Replaces Laravel Mix (Webpack). Hot Module Replacement (HMR) in development, optimized production bundles. Laravel plugin bundles `app.css` + `app.js` |

### 2.3 Database

| Technology | Version | Why This Choice |
|-----------|---------|----------------|
| **MySQL** | 5.7+ / 8.x | Relational database for all structured data. Chosen for: ACID compliance, familiar query model, strong Laravel/Eloquent support, and compatibility with JawsDB (Heroku MySQL add-on). FIELD() ordering function (used for type-based sort) is MySQL-specific |

### 2.4 Infrastructure & Services

| Service | Purpose | Why This Choice |
|---------|---------|----------------|
| **AWS S3** | File storage for user avatars and SAP error screenshots | Decoupled from the web server (critical on Heroku where the filesystem is ephemeral — files would be lost on dyno restart). S3 provides durability (99.999999999%), CDN-proximity, and fine-grained access control |
| **Heroku** | Application hosting | Platform-as-a-Service — no server provisioning, automatic SSL, built-in environment variable management. Procfile declares the web process: `web: vendor/bin/heroku-php-apache2 public/`. JawsDB add-on provides MySQL |
| **JawsDB** (implied) | Managed MySQL on Heroku | Auto-provisioned MySQL database via Heroku add-on; connection details injected via `JAWSDB_URL` or standard `DB_*` env vars |

### 2.5 Development Tooling

| Tool | Purpose |
|------|---------|
| **Composer** | PHP dependency manager |
| **NPM** | Node.js package manager for frontend assets |
| **Laravel Pint** | PHP code style fixer (PSR-12) |
| **Laravel Sail** | Docker-based dev environment (available, not mandated) |
| **Laravel Pail** | Real-time log tailing in terminal |
| **PHPUnit 11** | Unit and feature testing framework |
| **Faker** | Fake data generation for database seeders |

---

## 3. File & Folder Structure

Below is the complete annotated project tree. Every file and folder is explained.

```
doc_tracker/
│
├── 📁 app/                          # Core application logic (PHP)
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/          # Handle HTTP requests, delegate to models, return views
│   │   │   ├── 📁 Auth/             # Generated by Breeze — login, register, password reset
│   │   │   │   ├── AuthenticatedSessionController.php   # Handles login/logout
│   │   │   │   ├── RegisteredUserController.php          # Handles registration
│   │   │   │   ├── PasswordResetLinkController.php       # Sends reset email
│   │   │   │   └── NewPasswordController.php             # Handles password reset form
│   │   │   ├── Controller.php                            # Base controller (empty, extends nothing)
│   │   │   ├── ProductController.php                     # ⭐ Core — all document CRUD, submit, bulk submit, exports, trash
│   │   │   ├── ProfileController.php                     # User profile updates + avatar S3 upload/delete
│   │   │   ├── SapErrorController.php                    # SAP error knowledge base CRUD + S3 image handling
│   │   │   └── UserPreferenceController.php              # Stores/updates per-user JSON preferences
│   │   │
│   │   ├── 📁 Middleware/           # Request/response interceptors
│   │   │   └── (Laravel default middleware — auth, CSRF, etc.)
│   │   │
│   │   └── 📁 Requests/            # Form Request validation classes
│   │       └── ProfileUpdateRequest.php                  # Validates name + email on profile update
│   │
│   ├── 📁 Models/                   # Eloquent ORM models — represent database tables
│   │   ├── Product.php              # ⭐ Products table + global user scope + clearance logic + editability window
│   │   ├── SapError.php             # SAP errors table + belongsTo(User)
│   │   ├── User.php                 # Users table + hasMany(Products, SapErrors, Preferences)
│   │   └── UserPreference.php       # User preferences table — JSON value casting
│   │
│   └── 📁 Providers/               # Laravel service providers (app bootstrapping)
│       └── AppServiceProvider.php   # Register/boot custom app services
│
├── 📁 bootstrap/                    # Laravel framework bootstrapping (do not modify)
│   ├── app.php                      # Creates the Laravel application instance
│   └── 📁 cache/                    # Auto-generated compiled route/config/service cache
│
├── 📁 config/                       # All application configuration files
│   ├── app.php                      # App name, timezone, locale, debug mode, providers
│   ├── auth.php                     # Auth guards and providers (web = session + users table)
│   ├── cache.php                    # Cache store config (file-based by default)
│   ├── database.php                 # DB connection definitions (mysql, sqlite, pgsql, sqlsrv)
│   ├── filesystems.php              # ⭐ Defines local, public, and S3 disk configurations
│   ├── logging.php                  # Log channel config (stack, single, daily, slack)
│   ├── mail.php                     # Mail driver config (smtp, mailgun, ses, etc.)
│   ├── queue.php                    # Queue driver config (sync, database, redis, sqs)
│   ├── services.php                 # Third-party service keys (Mailgun, Postmark, AWS SES)
│   └── session.php                  # Session driver config (database driver is used here)
│
├── 📁 database/
│   ├── 📁 factories/               # Model factories for test data generation
│   │   └── UserFactory.php
│   ├── 📁 migrations/              # Database schema version control — run in order
│   │   ├── 0001_01_01_000000_create_users_table.php           # users, password_reset_tokens, sessions
│   │   ├── 0001_01_01_000001_create_cache_table.php           # cache, cache_locks
│   │   ├── 0001_01_01_000002_create_jobs_table.php            # jobs, job_batches, failed_jobs
│   │   ├── 2025_10_27_185322_create_products_table.php        # ⭐ Core products table
│   │   ├── 2026_05_17_083038_add_user_id_to_products_table.php # Added user_id FK to products
│   │   ├── 2026_05_17_084605_add_avatar_to_users_table.php    # Added avatar column to users
│   │   ├── 2026_06_17_061118_create_sap_errors_table.php      # SAP errors table
│   │   ├── 2026_06_18_062358_add_indexes_to_products_table.php # Performance indexes
│   │   ├── 2026_06_18_065522_add_deleted_at_to_products_table.php # Soft deletes
│   │   └── 2026_06_18_065522_create_user_preferences_table.php    # User preferences
│   └── 📁 seeders/                 # Seed initial/test data into DB
│       └── DatabaseSeeder.php
│
├── 📁 public/                       # Web root — only this folder is publicly accessible
│   ├── index.php                    # Entry point for all HTTP requests
│   ├── .htaccess                    # Apache URL rewrite rules (mod_rewrite)
│   ├── 📁 assets/                  # Static CSS/JS assets (manually placed, not Vite-compiled)
│   ├── 📁 build/                   # ⭐ Vite-compiled, cache-busted CSS + JS bundles (auto-generated)
│   └── 📁 storage/                 # Symlink → storage/app/public (created by php artisan storage:link)
│
├── 📁 resources/                    # Source files (compiled by Vite before serving)
│   ├── 📁 css/
│   │   └── app.css                  # Main CSS entry point (imports Tailwind directives)
│   ├── 📁 js/
│   │   ├── app.js                   # Main JS entry point (imports Alpine.js, Bootstrap)
│   │   └── bootstrap.js             # Axios setup, CSRF token injection
│   └── 📁 views/                   # Blade template files (.blade.php)
│       ├── 📁 auth/                # Auth pages
│       │   ├── login.blade.php      # Custom split-screen login page
│       │   ├── register.blade.php   # Registration page
│       │   ├── forgot-password.blade.php
│       │   └── reset-password.blade.php
│       ├── 📁 components/          # Reusable Blade components
│       ├── 📁 layouts/             # Page layout shells
│       │   └── dashboard.blade.php  # ⭐ Main app shell — sidebar, nav, content slot
│       ├── 📁 products/            # All document/batch views
│       │   ├── create.blade.php     # Multi-row document creation form
│       │   ├── index.blade.php      # All documents + 7-day chart
│       │   ├── pending.blade.php    # Pending documents with bulk-submit
│       │   ├── submitted.blade.php  # Submitted documents with CSV export
│       │   ├── daily.blade.php      # Daily list view with PDF export trigger
│       │   ├── show.blade.php       # Single document detail + clearance checkboxes
│       │   ├── edit.blade.php       # Edit basic document info
│       │   ├── trash.blade.php      # Recycle bin with restore action
│       │   ├── daily-pdf.blade.php        # PDF template — single column layout
│       │   └── daily-pdf-double.blade.php # PDF template — two column layout
│       ├── 📁 profile/             # User profile pages
│       │   └── edit.blade.php       # Name, email, password, avatar management
│       └── 📁 sap_errors/          # SAP error knowledge base views
│           ├── index.blade.php      # List all SAP errors
│           ├── create.blade.php     # Log new SAP error form
│           ├── show.blade.php       # View a single SAP error with screenshot
│           └── edit.blade.php       # Edit SAP error
│
├── 📁 routes/                       # HTTP route definitions
│   ├── web.php                      # ⭐ All web routes (auth-protected + public)
│   └── auth.php                     # Auth routes (generated by Breeze)
│
├── 📁 storage/                      # Private app storage (never directly web-accessible)
│   ├── 📁 app/
│   │   └── 📁 public/              # Public disk — accessible via /storage symlink
│   ├── 📁 framework/
│   │   ├── 📁 cache/               # Application cache files
│   │   ├── 📁 sessions/            # File-based session storage (if using file driver)
│   │   └── 📁 views/               # Compiled Blade template cache (.php files)
│   └── 📁 logs/
│       └── laravel.log              # Application error and info logs
│
├── 📁 tests/                        # Automated tests
│   ├── 📁 Feature/                 # Integration/HTTP tests
│   └── 📁 Unit/                    # Pure unit tests
│
├── 📁 vendor/                       # Composer dependencies (do not commit to git)
├── 📁 node_modules/                 # NPM dependencies (do not commit to git)
│
├── .env                             # ⭐ Local environment variables (never commit)
├── .env.example                     # Template for .env (safe to commit)
├── .gitignore                       # Excludes vendor/, node_modules/, .env, storage/logs
├── .gitattributes                   # Git line-ending normalization
├── .editorconfig                    # IDE indentation/encoding standards
├── .htaccess                        # Apache rewrite rules (public/ level)
├── artisan                          # Laravel CLI entry point: php artisan <command>
├── composer.json                    # PHP dependency declarations
├── composer.lock                    # Locked dependency versions (commit this)
├── package.json                     # Node dependency declarations
├── package-lock.json                # Locked Node versions (commit this)
├── phpunit.xml                      # PHPUnit test configuration
├── postcss.config.js                # PostCSS plugins config (autoprefixer)
├── tailwind.config.js               # TailwindCSS theme/content config
├── vite.config.js                   # Vite bundler config (entry: app.css + app.js)
├── Procfile                         # Heroku process declarations
├── DocTracker_PRD.md                # Product Requirements Document
└── README.md                        # Technical setup and usage guide
```

---

## 4. Database Schema

The database contains **7 tables**. All were created and versioned through Laravel migrations.

---

### Table 1: `users`
**Purpose:** Stores every registered user account. This is the root entity — all other tables link back to it.

| Column | Data Type | Nullable | Default | Description |
|--------|-----------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key. Uniquely identifies every user |
| `name` | VARCHAR(255) | No | — | Full display name of the user |
| `email` | VARCHAR(255) | No | — | Login email address. Must be unique across all users |
| `email_verified_at` | TIMESTAMP | Yes | NULL | Set when the user clicks a verification email link. Currently not enforced (MustVerifyEmail is commented out) |
| `password` | VARCHAR(255) | No | — | Bcrypt-hashed password. Never stored in plaintext |
| `avatar` | VARCHAR(255) | Yes | NULL | S3 path to the user's profile picture (e.g., `avatars/abc123.jpg`). NULL means no avatar uploaded |
| `remember_token` | VARCHAR(100) | Yes | NULL | Token for "Remember Me" login sessions. Rotated on login |
| `created_at` | TIMESTAMP | Yes | NULL | Record creation time |
| `updated_at` | TIMESTAMP | Yes | NULL | Last record update time |

**Indexes:** `email` (UNIQUE)  
**Key constraint:** `email` must be unique across the entire table

---

### Table 2: `products`
**Purpose:** The central table of the application. Each row represents one batch document for one product in one stage. This is what users create, track, and submit.

| Column | Data Type | Nullable | Default | Description |
|--------|-----------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `user_id` | BIGINT UNSIGNED | No | — | Foreign key → `users.id`. Identifies which operator created this document. Row-level isolation is enforced by a global Eloquent scope |
| `name` | VARCHAR(255) | No | — | Product name (e.g., "Caricef 100mg Suspension"). Free text, but sourced from a predefined catalog in the create form |
| `batch_no` | VARCHAR(255) | No | — | Batch number for this production run (e.g., "H-2501") |
| `stage` | VARCHAR(255) | No | — | Current document stage in the workflow. Free text with predefined options: "In Process", "QA Sign", "Prd Sign", "Return", "Transfer Note", "Hold", "Completed", "Corrections", etc. |
| `type` | VARCHAR(255) | No | — | Product formulation category. Constrained to: `Injection`, `Suspension`, `Tablet`, `Capsule`. Drives display sort order |
| `status` | ENUM | No | `pending` | Document lifecycle state: `pending` (in progress) or `submitted` (fully cleared and closed) |
| `line_clearance` | TINYINT(1) | No | `0` | Clearance gate 1. Cast to boolean. Must be `true` before submission is allowed |
| `review` | TINYINT(1) | No | `0` | Clearance gate 2. Cast to boolean. Must be `true` before submission is allowed |
| `confirmation` | TINYINT(1) | No | `0` | Clearance gate 3. Cast to boolean. Must be `true` before submission is allowed |
| `remarks` | TEXT | Yes | NULL | Free-text notes added by the operator (e.g., exception reasons, special instructions) |
| `submission_date` | DATE | Yes | NULL | The calendar date when the document was submitted. Set automatically by the system on submission |
| `submission_time` | TIME | Yes | NULL | The clock time when the document was submitted. Combined with `submission_date` to calculate the 6-hour editability window |
| `deleted_at` | TIMESTAMP | Yes | NULL | Soft delete timestamp. When set, the row is hidden from all standard queries but remains in the database (Recycle Bin feature). NULL means the record is active |
| `created_at` | TIMESTAMP | Yes | NULL | When the document entry was first created |
| `updated_at` | TIMESTAMP | Yes | NULL | Last modification time |

**Indexes (for query performance):**
- Composite index: `(user_id, status)` — most common filter combination
- Index: `type` — used for type-based filtering
- Index: `name` — used in search queries
- Index: `batch_no` — used in search queries
- Index: `stage` — used in search queries
- Index: `created_at` — used for date-range filtering

**Foreign keys:** `user_id` → `users.id`

**Special business logic on this model:**
- **Global Scope:** Every query automatically filters by `user_id = auth()->id()`. This means `Product::all()` only ever returns the current user's records
- **`isReadyForSubmission()`:** Returns true only if all three clearance booleans are `true`
- **`isEditable()`:** Returns true if the document is still pending, OR if it was submitted within the last 6 hours. After 6 hours, submitted documents are locked

---

### Table 3: `sap_errors`
**Purpose:** A knowledge base of SAP transaction errors. Each row is one documented error with a T-Code, description, and optional screenshot.

| Column | Data Type | Nullable | Default | Description |
|--------|-----------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `user_id` | BIGINT UNSIGNED | No | — | Foreign key → `users.id`. Identifies who logged this error. Used for authorization (only the creator can edit/delete) |
| `title` | VARCHAR(255) | No | — | Short, descriptive title of the error (e.g., "PO not found in MIGO") |
| `sap_tcode` | VARCHAR(100) | Yes | NULL | SAP Transaction Code where the error occurs (e.g., `MIGO`, `ME21N`, `MB1A`). Nullable because not all errors have a specific T-Code |
| `description` | TEXT | Yes | NULL | Full explanation of the error, what caused it, and how to resolve it. Up to 5,000 characters |
| `image_path` | VARCHAR(255) | Yes | NULL | S3 object path to the error screenshot (e.g., `errors/uuid.jpg`). NULL means no screenshot uploaded. Use `Storage::disk('s3')->url($image_path)` to generate the full URL |
| `created_at` | TIMESTAMP | Yes | NULL | When the error was logged |
| `updated_at` | TIMESTAMP | Yes | NULL | Last edit time |

**Foreign keys:** `user_id` → `users.id` with `CASCADE ON DELETE` (if a user is deleted, their SAP errors are deleted too)

---

### Table 4: `user_preferences`
**Purpose:** A flexible key-value store for per-user UI and application preferences. Synced to the database so preferences persist across devices.

| Column | Data Type | Nullable | Default | Description |
|--------|-----------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `user_id` | BIGINT UNSIGNED | No | — | Foreign key → `users.id` |
| `key` | VARCHAR(255) | No | — | Preference identifier (e.g., `"hidden_items"`, `"autocomplete_enabled"`) |
| `value` | JSON | Yes | NULL | The preference value stored as JSON. Cast to PHP array automatically by Eloquent. Allows complex structures (arrays, objects) per key |
| `created_at` | TIMESTAMP | Yes | NULL | When this preference was first set |
| `updated_at` | TIMESTAMP | Yes | NULL | Last updated time |

**Unique constraint:** `(user_id, key)` — each user can only have one row per preference key. This enforces upsert behavior (update if exists, insert if not)  
**Foreign keys:** `user_id` → `users.id` with `CASCADE ON DELETE`

---

### Table 5: `sessions`
**Purpose:** Stores active user sessions server-side (database session driver). More secure than client-only cookie sessions because session data lives on the server.

| Column | Data Type | Description |
|--------|-----------|-------------|
| `id` | VARCHAR | Session identifier (matches the session cookie value) |
| `user_id` | BIGINT | Nullable — links to the logged-in user, NULL for guest sessions |
| `ip_address` | VARCHAR(45) | Client IP (IPv4 or IPv6) |
| `user_agent` | TEXT | Browser/client user agent string |
| `payload` | LONGTEXT | Serialized session data (encrypted) |
| `last_activity` | INT | Unix timestamp of the last request in this session. Used for session expiry |

---

### Table 6: `password_reset_tokens`
**Purpose:** Temporary tokens for the "Forgot Password" flow. A token is generated when a user requests a reset, emailed to them, and consumed once used.

| Column | Data Type | Description |
|--------|-----------|-------------|
| `email` | VARCHAR | Primary key — the email address requesting a reset |
| `token` | VARCHAR | Hashed reset token. The plain token is emailed; only the hash is stored |
| `created_at` | TIMESTAMP | When the token was created. Tokens expire after 60 minutes (configurable) |

---

### Table 7: `cache` + `cache_locks`
**Purpose:** Stores application cache data when using the database cache driver. Not heavily used in v1 (no explicit caching implemented yet), but available.

| Column | Description |
|--------|-------------|
| `key` | Cache key identifier |
| `value` | Serialized cached value |
| `expiration` | Unix timestamp when this cache entry expires |

---

## 5. Entity Relationships

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        string avatar
        timestamp email_verified_at
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTS {
        bigint id PK
        bigint user_id FK
        string name
        string batch_no
        string stage
        string type
        enum status
        boolean line_clearance
        boolean review
        boolean confirmation
        text remarks
        date submission_date
        time submission_time
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    SAP_ERRORS {
        bigint id PK
        bigint user_id FK
        string title
        string sap_tcode
        text description
        string image_path
        timestamp created_at
        timestamp updated_at
    }

    USER_PREFERENCES {
        bigint id PK
        bigint user_id FK
        string key
        json value
        timestamp created_at
        timestamp updated_at
    }

    SESSIONS {
        string id PK
        bigint user_id FK
        string ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    PASSWORD_RESET_TOKENS {
        string email PK
        string token
        timestamp created_at
    }

    USERS ||--o{ PRODUCTS : "creates"
    USERS ||--o{ SAP_ERRORS : "logs"
    USERS ||--o{ USER_PREFERENCES : "has"
    USERS ||--o{ SESSIONS : "opens"
```

### Relationship Summary (Plain English)

| Relationship | Type | Description |
|-------------|------|-------------|
| `User` → `Products` | One-to-Many | One user can create many batch documents. A document belongs to exactly one user |
| `User` → `SapErrors` | One-to-Many | One user can log many SAP errors. An error belongs to exactly one user |
| `User` → `UserPreferences` | One-to-Many | One user can have many preference keys. Each preference belongs to one user |
| `User` → `Sessions` | One-to-Many | One user can have multiple active sessions (different devices/browsers) |

> **Important:** In v1, data isolation is enforced at the **application layer** via a global Eloquent scope on `Product`, and via `where('user_id', auth()->id())` in `SapErrorController`. There are no database-level row-level security policies. This means a bug in the scope bypass would expose data across users — see §9 for the recommendation.

---

## 6. Request Lifecycle

Every web request in DocTracker follows this path:

```
Browser Request
     │
     ▼
public/index.php          ← Entry point; bootstraps Laravel
     │
     ▼
bootstrap/app.php         ← Creates Application, registers middleware
     │
     ▼
HTTP Kernel
     │
     ├── Global Middleware (TrimStrings, ConvertEmptyStringsToNull, etc.)
     │
     ├── Route Middleware Group: 'web'
     │   ├── EncryptCookies
     │   ├── AddQueuedCookiesToResponse
     │   ├── StartSession              ← Loads session from database
     │   ├── ShareErrorsFromSession
     │   └── VerifyCsrfToken          ← Validates CSRF token on all POST/PUT/DELETE
     │
     ├── Route Middleware: 'auth'      ← Redirects to /login if unauthenticated
     │
     ▼
routes/web.php            ← Matches URL to controller action
     │
     ▼
Controller Method
     │
     ├── Validates request (inline validate() or FormRequest)
     ├── Calls Eloquent model methods
     ├── Interacts with S3 if file upload involved
     └── Returns view() or redirect()
     │
     ▼
Blade Template            ← Compiled to PHP, rendered to HTML
     │
     ▼
HTTP Response → Browser
```

---

## 7. Storage Architecture

### S3 Bucket Structure

```
your-s3-bucket/
├── avatars/
│   └── {random-uuid}.{jpg|png|webp}     # User profile pictures (2MB max)
│
└── errors/
    └── {random-uuid}.{jpg|png|webp}     # SAP error screenshots (5MB max)
```

### How Files Are Stored and Retrieved

**Upload (store):**
```php
// Laravel stores the file and returns the S3 path
$path = $request->file('image')->store('errors', 's3');
// $path = "errors/abc123.jpg"
// This relative path is saved to the database
```

**Retrieve (URL generation):**
```php
// In Blade templates:
Storage::disk('s3')->url($sapError->image_path)
// Returns: https://your-bucket.s3.region.amazonaws.com/errors/abc123.jpg
```

**Delete:**
```php
Storage::disk('s3')->delete($sapError->image_path);
// Called on record delete AND on image replacement (to prevent orphaned files)
```

> **⚠️ Important:** The S3 disk has `'throw' => true` — S3 errors will throw exceptions (not silently fail). The controllers wrap S3 calls in try/catch and return validation errors to the user, which is the correct pattern.

### Heroku Ephemeral Filesystem Note

Heroku's dyno filesystem is ephemeral — files written to local disk are lost on every dyno restart or deploy. This is why S3 is mandatory for any user-uploaded content. The `public` disk (local) should only be used for temporary or non-critical files.

---

## 8. Environment Variables Reference

Create a `.env` file at the project root. Below is the complete reference for all variables DocTracker uses:

```env
# ══════════════════════════════════════════════════════════
# APP CORE
# ══════════════════════════════════════════════════════════

APP_NAME="DocTracker"
# The name of your application. Appears in browser tabs and emails.

APP_ENV=production
# Options: local | staging | production
# Controls debug mode visibility and some service behaviors.

APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate
# 32-character random key used to encrypt sessions, cookies, and data.
# CRITICAL: Must be unique per environment. Never share this key.
# Generate with: php artisan key:generate

APP_DEBUG=false
# true = Show detailed error pages (use only in local dev)
# false = Show generic error page (required in production)

APP_URL=https://your-app-name.herokuapp.com
# Full URL of your application. Used for generating file storage URLs,
# email links, and asset URLs.

APP_TIMEZONE=Asia/Karachi
# PHP timezone for date/time calculations.
# DocTracker uses now() extensively for submission timestamps.


# ══════════════════════════════════════════════════════════
# DATABASE
# ══════════════════════════════════════════════════════════

DB_CONNECTION=mysql
# The database driver. Must be 'mysql' for this application.
# (FIELD() ordering is MySQL-specific and will fail on SQLite/Postgres)

DB_HOST=127.0.0.1
# Hostname of your MySQL server.
# On Heroku with JawsDB: use the host from your JAWSDB_URL.

DB_PORT=3306
# Standard MySQL port. Change only if your server uses a custom port.

DB_DATABASE=doc_tracker
# Name of the MySQL database to connect to.

DB_USERNAME=root
# MySQL user with read/write privileges on DB_DATABASE.

DB_PASSWORD=your_secure_password
# MySQL password for DB_USERNAME.


# ══════════════════════════════════════════════════════════
# AWS S3 — REQUIRED FOR FILE UPLOADS
# ══════════════════════════════════════════════════════════

AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE
# IAM user access key. Create a dedicated IAM user with S3-only permissions.
# Do NOT use root account credentials.

AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
# Secret key paired with the access key above.

AWS_DEFAULT_REGION=us-east-1
# The AWS region where your S3 bucket lives (e.g., ap-south-1 for Mumbai).
# Must match the actual bucket region exactly.

AWS_BUCKET=your-doctracker-bucket
# The name of your S3 bucket. Must already exist.

AWS_URL=
# Optional. Custom CDN URL (e.g., CloudFront distribution URL).
# Leave blank to use the default S3 URL format.

AWS_ENDPOINT=
# Optional. For S3-compatible services (MinIO, Backblaze B2, DigitalOcean Spaces).
# Leave blank for real AWS S3.

AWS_USE_PATH_STYLE_ENDPOINT=false
# Set to true only when using MinIO or other S3-compatible services
# that require path-style URLs (http://endpoint/bucket/key).


# ══════════════════════════════════════════════════════════
# SESSION & CACHE
# ══════════════════════════════════════════════════════════

SESSION_DRIVER=database
# Stores session data in the 'sessions' MySQL table.
# More secure than 'cookie' driver (session data lives server-side).
# Alternatives: file | redis | memcached

SESSION_LIFETIME=120
# Session expires after 120 minutes of inactivity.
# After this, users are automatically logged out.

SESSION_ENCRYPT=false
# Encrypt session payload in the database.
# Set to true for enhanced security (minor performance cost).

CACHE_STORE=database
# Cache backend. 'database' uses the 'cache' table.
# Can be upgraded to 'redis' for better performance at scale.


# ══════════════════════════════════════════════════════════
# FILESYSTEM
# ══════════════════════════════════════════════════════════

FILESYSTEM_DISK=local
# Default disk for Storage facade calls without explicit disk.
# Set to 's3' if you want S3 as the default disk.


# ══════════════════════════════════════════════════════════
# LOGGING
# ══════════════════════════════════════════════════════════

LOG_CHANNEL=stack
# How logs are written.
# 'stack' = multiple channels simultaneously.
# 'single' = one laravel.log file.
# 'daily' = rotate log file daily.

LOG_LEVEL=error
# Minimum severity to log: debug | info | notice | warning | error | critical
# Use 'error' in production to avoid performance impact from verbose logging.


# ══════════════════════════════════════════════════════════
# MAIL (Not actively used in v1, but required by Laravel)
# ══════════════════════════════════════════════════════════

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@healthtek.com
MAIL_FROM_NAME="DocTracker"
# Required for the password reset flow (sends reset email).


# ══════════════════════════════════════════════════════════
# QUEUE (Not actively used in v1)
# ══════════════════════════════════════════════════════════

QUEUE_CONNECTION=sync
# 'sync' = jobs run immediately in the same request (no background workers needed).
# Upgrade to 'database' or 'redis' if you add async jobs in v2.
```

### Heroku-Specific Configuration Notes

When deploying to Heroku, set all of the above via the Heroku dashboard (`Settings → Config Vars`) or CLI:

```bash
heroku config:set APP_KEY=base64:your-key-here
heroku config:set AWS_ACCESS_KEY_ID=your-key
# etc.
```

**JawsDB (MySQL on Heroku):** JawsDB injects a `JAWSDB_URL` config var. You must parse it and set the individual `DB_*` variables, OR use a custom `config/database.php` that parses the URL. The recommended approach:

```bash
# Get your JawsDB URL
heroku config:get JAWSDB_URL
# Output: mysql://username:password@hostname:3306/dbname

# Set individual DB vars from the URL
heroku config:set DB_HOST=hostname
heroku config:set DB_DATABASE=dbname
heroku config:set DB_USERNAME=username
heroku config:set DB_PASSWORD=password
```

---

## 9. Architectural Observations & Recommendations

### ✅ What's Done Well

| Decision | Why It's Good |
|---------|--------------|
| **Global Eloquent scope on Product** | Automatic user data isolation — impossible to forget to add `where('user_id')` in a new query |
| **Soft deletes on products** | No permanent data loss from accidental deletes. Recycle Bin is a UX win for non-technical users |
| **S3 for all file uploads** | Correct for Heroku's ephemeral filesystem. Old file deletion on replace prevents S3 cost bleed |
| **Composite index `(user_id, status)`** | Matches the most frequent query pattern: `WHERE user_id = ? AND status = ?` |
| **FormRequest for profile updates** | Clean separation of validation from controller logic |
| **MySQL FIELD() for sort order** | Business-logic sort (Suspension → Injection → Capsule → Tablet) encoded correctly at the DB query level |

### ⚠️ Technical Debt to Address

| Issue | Severity | Recommendation |
|-------|----------|---------------|
| **Dual CSS frameworks (Bootstrap + Tailwind)** | Medium | Pick one. Tailwind is already doing most of the work. Remove Bootstrap CDN imports to reduce page weight (~30KB) and eliminate specificity conflicts |
| **Inline `where('user_id', auth()->id())` in SapErrorController** | Medium | SAP errors lack the global Eloquent scope that products have. Add a global scope to `SapError` model for consistency, or this will cause a bug the moment someone forgets the `where()` clause |
| **No eager loading** | Low | `Product::where(...)` queries without `with('user')` will cause N+1 queries if user data is accessed per-product in a loop. Profile with Laravel Debugbar in development |
| **Hardcoded product catalog** | Low | Product names + batch auto-fill are hardcoded in `ProductController::create()`. Move to a `products_catalog` database table with a simple admin CRUD for maintainability |
| **`isEditable()` 6-hour window hardcoded** | Low | Make the editability window configurable via an env variable or settings table (`DOCUMENT_EDIT_WINDOW_HOURS=6`) |
| **No rate limiting on login** | Medium | Add Laravel's `RateLimiter` to the login route to prevent brute-force attacks |
| **Missing pagination on SAP errors** | Low | `SapError::where(...)->get()` returns all records. As the knowledge base grows, add `->paginate(20)` |

### 🚀 Scaling Roadmap (v2 Recommendations)

| When to scale | What to add |
|--------------|------------|
| **User count > 20** | Add RBAC (roles: operator, supervisor, admin) using Spatie Laravel Permission |
| **Concurrent users > 50** | Switch `SESSION_DRIVER` and `CACHE_STORE` to Redis (Heroku Redis add-on) |
| **Products table > 50,000 rows** | Add database-level partitioning by `user_id` and archive submitted documents older than 1 year to a cold table |
| **Need audit compliance** | Add `activity_log` table using Spatie Laravel Activity Log — tracks every field change with user + timestamp |
| **Need async tasks** | Switch `QUEUE_CONNECTION` to `database`, add a worker dyno in Procfile for email/notification jobs |
| **Multi-facility** | Extract `facility_id` as a new scoping dimension above `user_id` — add to all relevant tables |

---

*Document prepared from live codebase audit of DocTracker v1 (Laravel 11, PHP 8.2, June 2026 build). All table schemas, relationships, and configuration references reflect the actual deployed state of the application.*
