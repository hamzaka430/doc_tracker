# 📊 Doc Tracker - Production Document Tracking System

A modern, enterprise-ready Laravel 11 application for tracking products, batch documents, and injection vials across production stages with a mobile-first UI, dual database isolation, automated cloud heartbeat workflows, and comprehensive audit tracking.

---

## ✨ Features

- **Product Management**: Add, track, edit, and bulk-process products through customizable production stages (e.g. *Pre-Line Clearance*, *In-Process*, *Post-Line Clearance*).
- **Vials Tracking**: Dedicated daily and all-time tracking for injection vials with batch-specific metrics and real-time calculation.
- **Smart Auto-Fill**: Auto-fill batch codes based on selected product names using predefined database mapping.
- **Bulk Submit System**: Process and submit multiple pending documents simultaneously with a single click.
- **PDF Generation**: Generate downloadable PDF summaries of daily documents with multiple layout options (Single Page, Two Column).
- **Recycle Bin (Soft Deletes)**: Safely delete, inspect, and restore documents without permanent data loss.
- **Dashboard Analytics**: Visual line charts tracking 7-day document submission progress.
- **SAP Error Tracking**: Record SAP errors with T-Codes, rich descriptions, and high-resolution screenshots.
- **AWS S3 / Cloudflare R2 Integration**: Secure cloud object storage for profile avatars and SAP error screenshots.
- **Secret Chat System**:
  - Independent database connection (`mysql_chat` on Aiven Cloud MySQL with SSL).
  - 100% isolated from primary JawsDB quotas (zero queries to primary DB during polling).
  - 4-digit PIN security lock with auto-lock and tab visibility pause.
- **Automated Database Keepalive**:
  - Scheduled GitHub Actions workflow (`.github/workflows/keep_chat_alive.yml`) runs every 6 hours.
  - Heartbeat endpoint (`/api/chat-db-keepalive`) prevents Aiven Cloud DB from sleeping or powering off.
- **Dual-Framework UI Architecture**:
  - **Dashboard Pages**: Styled with **Kaiadmin + Bootstrap 5** for high-density, responsive dashboard layouts.
  - **Auth Pages**: Built with **Tailwind CSS** featuring a modern 3D security illustration.
  - **Self-Healing Vite Asset Recovery**: Automatic fallback to compiled production assets if the local Vite dev server is stopped.
- **Global Breadcrumbs**: Standardized next-line breadcrumbs layout for consistent visual hierarchy across all views.
- **Excel / CSV Export**: Instant export of submitted products and vials data for audits and reporting.

---

## 🛠️ Technology Stack

| Layer | Technologies |
|---|---|
| **Backend** | Laravel 11, PHP 8.2+ |
| **Primary Database** | MySQL (JawsDB on Heroku / Local MySQL) |
| **Chat Database** | Dedicated Aiven Cloud MySQL (SSL enabled) |
| **Object Storage** | Cloudflare R2 / AWS S3 |
| **Dashboard UI** | Kaiadmin, Bootstrap 5, Font Awesome, Simple Line Icons |
| **Auth UI** | Tailwind CSS 3, Vite 5 |
| **Automation & CI** | GitHub Actions (Heartbeat Keepalive Workflow) |
| **Hosting** | Heroku (Stack: heroku-24, PHP 8.4 runtime) |

---

## 📋 Requirements

- **PHP** >= 8.2 with extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`, `fileinfo`
- **Composer** 2.x
- **Node.js** >= 18.x & **NPM**
- **MySQL** >= 5.7 or MariaDB >= 10.3
- **Git**

---

## 🚨 Important Architectural & Security Guidelines

### 1. Dual-Framework Frontend Rule
DocTracker uses **two CSS frameworks simultaneously** that must never mix:
* **Dashboard Pages:** Use **Kaiadmin + Bootstrap 5** (loaded from `public/Dashboard/assets/`). Do NOT apply Tailwind classes here.
* **Auth Pages:** Use **Tailwind CSS** (compiled via Vite to `public/build/`). Do NOT use Bootstrap/Kaiadmin classes here.

### 2. Dual-Database Isolation (Zero JawsDB Chat Polling)
* All core entities (`products`, `vials`, `users`, `sap_errors`, `preferences`) reside on the default `mysql` connection.
* The Chat subsystem (`chat_messages`, `chat_settings`) resides on the dedicated `mysql_chat` connection (Aiven MySQL).
* Session and Cache are configured to `file` (`SESSION_DRIVER=file`, `CACHE_DRIVER=file`) to prevent session polling from exhausting JawsDB free-tier hourly query limits.

### 3. Keep-Alive Heartbeat Workflow
* Free-tier cloud databases like Aiven power off after inactivity.
* The GitHub Actions workflow in `.github/workflows/keep_chat_alive.yml` pings `/api/chat-db-keepalive` every 6 hours to maintain connection warmth.

### 4. Comprehensive Documentation
Detailed technical specifications and architecture records are stored in the [`docs/`](docs/) directory:
* [`docs/DocTracker_PRD.md`](docs/DocTracker_PRD.md) - Product Requirements Document
* [`docs/DocTracker_TechArchitecture.md`](docs/DocTracker_TechArchitecture.md) - System Architecture & Database Design
* [`docs/DocTracker_FrontendSpec.md`](docs/DocTracker_FrontendSpec.md) - Frontend & Design System Specification
* [`docs/DocTracker_SecurityDocument.md`](docs/DocTracker_SecurityDocument.md) - Security Standards & Permissions
* [`docs/DocTracker_FeatureTickets.md`](docs/DocTracker_FeatureTickets.md) - Detailed Feature Roadmap & Tickets

---

## 🚀 Installation & Local Setup

### 1. Clone the repository
```bash
git clone https://github.com/hamzaka430/doc_tracker.git
cd doc_tracker
```

### 2. Install PHP and Node dependencies
```bash
composer install
npm install
```

### 3. Configure environment
```bash
# Copy example environment configuration
cp .env.example .env

# Generate application encryption key
php artisan key:generate
```

### 4. Configure database in `.env`
Update `.env` with your local database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doc_tracker
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run migrations & seeders
```bash
php artisan migrate --seed
```

### 6. Build frontend assets
```bash
# Build production assets
npm run build

# Or run Vite dev server
npm run dev
```

### 7. Start local server
```bash
php artisan serve
```
Open **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in your browser.

---

## 🔌 Key Application Routes

| Method | Endpoint | Description | Middleware |
|---|---|---|---|
| `GET` | `/` | Redirects to login / dashboard | Guest / Auth |
| `GET` | `/products` | Main product dashboard list | `auth` |
| `GET` | `/products/create` | Create new product document | `auth` |
| `POST` | `/products` | Store new product document | `auth` |
| `GET` | `/products/pending` | Filtered list of pending documents | `auth` |
| `GET` | `/products/submitted` | Filtered list of submitted documents | `auth` |
| `GET` | `/products/daily` | Daily documents view | `auth` |
| `GET` | `/products/daily/pdf` | Export daily documents as PDF | `auth` |
| `GET` | `/products/export` | Export documents to CSV | `auth` |
| `GET` | `/products/trash` | Soft-deleted documents recycle bin | `auth` |
| `POST` | `/products/bulk-submit` | Bulk submit selected pending documents | `auth` |
| `GET` | `/sap-errors` | SAP error log and screenshots list | `auth` |
| `GET` | `/chat` | Secret chat support interface | `auth` |
| `GET` | `/chat/messages` | Chat polling endpoint (zero JawsDB) | Public / Session |
| `GET` | `/api/chat-db-keepalive` | Automated keepalive heartbeat endpoint | Public |

---

## 📁 Project Directory Structure

```
doc_tracker/
├── .github/
│   └── workflows/
│       └── keep_chat_alive.yml   # Scheduled DB heartbeat workflow (runs every 6h)
├── app/
│   ├── Http/
│   │   ├── Controllers/          # ProductController, ChatController, ProfileController, etc.
│   │   └── Middleware/           # Session & auth middleware
│   ├── Models/                   # Product, ChatMessage, ChatSetting, SapError, User
│   └── Providers/
│       └── AppServiceProvider.php# Timezone, SSL, and Vite self-healing recovery logic
├── config/                       # database.php, filesystems.php, app.php
├── database/
│   ├── migrations/               # Schema migrations for both primary and chat databases
│   └── seeders/                  # Default user and sample data seeders
├── docs/                         # PRD, Architecture, Security, and UI specifications
│   ├── DocTracker_FeatureTickets.md
│   ├── DocTracker_FrontendSpec.md
│   ├── DocTracker_PRD.md
│   ├── DocTracker_SecurityDocument.md
│   └── DocTracker_TechArchitecture.md
├── public/
│   ├── Dashboard/assets/         # Kaiadmin & Bootstrap theme assets (CSS, JS, Fonts)
│   ├── build/                    # Compiled Vite assets for Tailwind auth views
│   ├── login_signup_illustration.webp # 3D authentication artwork
│   └── index.php                 # Laravel HTTP entrypoint
├── resources/
│   ├── css/ & js/                # Source Tailwind CSS & JS files
│   └── views/                    # Blade templates (products, chat, auth, layouts)
├── routes/
│   ├── web.php                   # Web and keepalive routes
│   └── console.php               # Artisan commands (chat:ping)
├── storage/                      # File uploads, sessions, and framework cache
├── ca.pem                        # Aiven Cloud MySQL SSL Certificate Authority
├── composer.json                 # PHP dependencies & post-autoload dump hooks
├── package.json                  # Node dependencies & Vite build scripts
└── Procfile                      # Heroku deployment process definition
```

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "feat: add your feature"`
4. Push to branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

---

## 📝 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Hamza Zaka**
- GitHub: [@hamzaka430](https://github.com/hamzaka430)
- Repository: [hamzaka430/doc_tracker](https://github.com/hamzaka430/doc_tracker)
- Powered by: [Dezignwise](https://dezignwise.com)
