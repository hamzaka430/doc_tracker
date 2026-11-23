# 📊 Doc Tracker - Production Document Tracking & Support Communication System

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Heroku Deployment](https://img.shields.io/badge/Heroku-Live%20v61-430098?style=for-the-badge&logo=heroku&logoColor=white)](https://doctracking-3064bacd0985.herokuapp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

A modern, enterprise-ready Laravel 11 application for tracking pharmaceutical production products, batch documents, and injection vials across manufacturing stages. Built with a responsive mobile-first UI, dual-database isolation, automated cloud heartbeat workflows, and an end-to-end encrypted Telegram-styled real-time support communication log.

🌐 **Live Production App**: [https://doctracking-3064bacd0985.herokuapp.com/](https://doctracking-3064bacd0985.herokuapp.com/)

---

## 📑 Table of Contents

- [Core Capabilities](#-core-capabilities)
- [Support Communication Log (Secret Chat)](#-support-communication-log-secret-chat)
- [Technology Stack](#-technology-stack)
- [Architectural & Security Standards](#-architectural--security-standards)
- [Installation & Local Setup](#-installation--local-setup)
- [Database Configuration & Dual DB Isolation](#-database-configuration--dual-db-isolation)
- [Key Application Routes](#-key-application-routes)
- [Project Directory Structure](#-project-directory-structure)
- [Deployment Guide](#-deployment-guide)
- [Author & Credits](#-author--credits)

---

## ✨ Core Capabilities

### 1. Document & Batch Tracking
- **Multi-Stage Tracking**: Manage products across customizable production stages (*Pre-Line Clearance*, *In-Process*, *Post-Line Clearance*).
- **Vials Tracking**: Specialized tracking for injection vials with batch-specific metrics, counts, and daily calculations.
- **Smart Auto-Fill**: Auto-populates batch numbers and codes based on selected product names from predefined database records.
- **Bulk Submit System**: Process, confirm, and submit multiple pending batch documents with a single click.
- **Recycle Bin (Soft Deletes)**: Safely inspect, recover, or permanently purge soft-deleted documents.
- **PDF Generation**: Export daily document summaries formatted for printing with Single-Page and Two-Column layout options.
- **Excel / CSV Export**: Instant export of batch logs and submitted records for auditing.

### 2. SAP Error Logging & Cloud Storage
- **SAP Error Tracking**: Record SAP errors with T-Codes, rich descriptions, and timestamps.
- **High-Resolution Screenshots**: Cloudflare R2 / AWS S3 integration for storing error screenshots and user profile avatars with pre-signed secure URLs.

### 3. Analytics & Notifications
- **Visual Analytics**: Interactive charts tracking 7-day document throughput and submission velocity.
- **Real-Time Sidebar Unread Badge**: Dynamic pill badge in the navigation sidebar displaying unread support messages in real-time.

---

## 💬 Support Communication Log (Secret Chat)

A high-performance, Telegram-inspired real-time communication log built for secure support operations:

### 📱 Responsive Viewport Lock (Zero Outer Scroll)
- **Zero Outer Page Scroll**: Strict viewport locking (`html, body { overflow: hidden; height: 100dvh; }`) across all screen sizes (Desktop, Tablet, Mobile).
- **Independent Stream Scroll**: The outer page stays completely fixed under the top navbar down to the screen edge; only the internal message stream (`#chatContainer`) scrolls smoothly with native momentum scrolling (`-webkit-overflow-scrolling: touch`).
- **Clean Interface**: Breadcrumbs and footer are completely stripped from this page to maximize usable chat area.

### 🔒 Deep Privacy Blur Mode
- **Typing Blur**: When Privacy Blur Mode is active, any message currently being written into the input textarea is automatically blurred (`filter: blur(5px); opacity: 0.45;`) so bystanders cannot read in-progress text.
- **Hover-Peek Unblur**: Hovering the cursor over the input box immediately restores 100% crisp visibility (`filter: blur(0px); opacity: 1;`) to allow effortless inspection before dispatch. When the mouse leaves, it instantly re-blurs.
- **Crisp Empty Placeholder**: When the input is empty, the placeholder (`"Write a message..."`) remains 100% crisp and readable (Instagram & Telegram standard).
- **Bubble Privacy Blur**: Message content, sender names, and avatars can be blurred globally with individual hover-to-peek unblur.

### 🎨 Themes & Mobile Navigation
- **Theme Switcher**: Instant switching between **Black Theme** (`#121624`), **White BG Theme** (`#ffffff`), and custom accent variants with high-contrast token systems.
- **Mobile 3-Dots Menu `(⋮)`**: Clean native dropdown menu providing 1-tap toggles for White BG, Privacy Blur Mode, In-Chat Voice Audio, Settings Modal, and Lock Session.
- **Interactive Quick-Actions**: Swipe or tap messages to access Reply preview quotes, Emoji Reactions with heart burst animations, In-line Editing, Forwarding, Message Pinning, and Unsend for Everyone.
- **Multi-Persona Toggle**: Switch seamlessly between `Mr.` and `Mrs.` personas with persistent localStorage state, dynamically aligning sent messages to the right and received messages to the left.
- **PIN Security Lock**: Protected with a 4-digit numeric PIN, automatic session timeout, and background tab visibility pause.

---

## 🛠️ Technology Stack

| Component | Technologies |
|---|---|
| **Backend Framework** | Laravel 11.x, PHP 8.4 runtime |
| **Primary Database** | MySQL (JawsDB on Heroku / Local MySQL) |
| **Chat Database** | Dedicated Aiven Cloud MySQL with SSL (`ca.pem`) |
| **Object Storage** | Cloudflare R2 / AWS S3 (S3-compatible API) |
| **Dashboard UI** | KaiAdmin Dashboard, Bootstrap 5, Font Awesome 6 |
| **Auth UI** | Tailwind CSS 3, Vite 5 with auto-recovery fallbacks |
| **Automation** | GitHub Actions Keepalive Heartbeat Cron (`.github/workflows/keep_chat_alive.yml`) |
| **Hosting & Cloud** | Heroku (Stack: `heroku-24`, web dyno, Nginx/Apache + PHP-FPM) |

---

## 🚨 Architectural & Security Standards

### 1. Dual-Framework UI Isolation
Doc Tracker runs **two CSS frameworks simultaneously** that are strictly separated:
- **Dashboard Views**: Styled using **KaiAdmin + Bootstrap 5** (loaded from `public/Dashboard/assets/`). Tailwind utility classes are forbidden in dashboard layouts.
- **Authentication Views**: Styled using **Tailwind CSS 3** (compiled via Vite to `public/build/`). Bootstrap classes are forbidden in auth views.

### 2. Dual-Database Isolation (Zero JawsDB Chat Polling)
- Core application data (`users`, `products`, `vials`, `sap_errors`) lives on the primary `mysql` connection.
- The real-time chat subsystem (`chat_messages`, `chat_settings`) lives on the secondary `mysql_chat` connection (Aiven MySQL).
- Polling requests (`/chat/messages`) query **only** the `mysql_chat` connection, preventing any exhaustion of free-tier JawsDB hourly limits.
- Session and cache drivers are configured to `file` (`SESSION_DRIVER=file`, `CACHE_DRIVER=file`).

### 3. Automated Database Keepalive Workflow
- Free cloud databases power off after periods of inactivity.
- A GitHub Actions workflow (`.github/workflows/keep_chat_alive.yml`) automatically triggers `/api/chat-db-keepalive` every 6 hours to maintain database warmth.

---

## 🚀 Installation & Local Setup

### 1. Clone the repository
```bash
git clone https://github.com/hamzaka430/doc_tracker.git
cd doc_tracker
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
Configure `.env` with primary and chat database connections:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doc_tracker
DB_USERNAME=root
DB_PASSWORD=

# Secondary Chat Database
CHAT_DB_CONNECTION=mysql_chat
CHAT_DB_HOST=your-aiven-db-host.aivencloud.com
CHAT_DB_PORT=your-port
CHAT_DB_DATABASE=defaultdb
CHAT_DB_USERNAME=avnadmin
CHAT_DB_PASSWORD=your-aiven-password
CHAT_DB_SSL_CA=ca.pem
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

### 6. Build Assets & Start Server
```bash
# Compile Tailwind production assets
npm run build

# Start local server
php artisan serve
```
Visit **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in your browser.

---

## 🔌 Key Application Routes

| Method | Endpoint | Description | Access |
|---|---|---|---|
| `GET` | `/` | Login / Dashboard redirect | Guest / Auth |
| `GET` | `/products` | Main product dashboard list | Auth |
| `GET` | `/products/pending` | Filtered pending documents | Auth |
| `GET` | `/products/submitted` | Filtered submitted documents | Auth |
| `GET` | `/products/daily` | Daily documents overview | Auth |
| `GET` | `/products/daily/pdf` | Export daily summary PDF | Auth |
| `GET` | `/products/export` | Export documents to CSV | Auth |
| `GET` | `/products/trash` | Recycle bin (Soft deletes) | Auth |
| `POST` | `/products/bulk-submit` | Bulk process pending batches | Auth |
| `GET` | `/sap-errors` | SAP errors log & screenshots | Auth |
| `GET` | `/chat` | Support Communication Log interface | Auth |
| `GET` | `/chat/messages` | Real-time chat polling endpoint | Auth |
| `POST` | `/chat/send` | Send message (new or edit submit) | Auth |
| `POST` | `/chat/lock` | Lock chat PIN session | Auth |
| `GET` | `/chat/unread-count` | Real-time unread messages count | Auth |
| `GET` | `/api/chat-db-keepalive` | Database heartbeat ping endpoint | Public |

---

## 📁 Project Directory Structure

```
doc_tracker/
├── .github/
│   └── workflows/
│       └── keep_chat_alive.yml       # 6-hour cron keepalive workflow
├── app/
│   ├── Http/
│   │   ├── Controllers/              # ProductController, ChatController, ProfileController, SapErrorController
│   │   └── Middleware/               # Authentication and session handling
│   ├── Models/                       # Product, ChatMessage, ChatSetting, SapError, User
│   └── Providers/
│       └── AppServiceProvider.php    # Timezone, SSL enforcement, and Vite asset recovery
├── config/                           # database.php, filesystems.php, app.php
├── database/
│   ├── migrations/                   # Schema migrations for default and chat databases
│   └── seeders/                      # Database seeders
├── docs/                             # Technical PRD, Architecture, and Security specs
├── public/
│   ├── Dashboard/assets/             # KaiAdmin & Bootstrap 5 theme assets
│   ├── build/                        # Compiled Tailwind auth assets
│   └── index.php                     # Laravel HTTP entrypoint
├── resources/
│   ├── css/ & js/                    # Tailwind source files
│   └── views/
│       ├── auth/                     # Tailwind authentication views
│       ├── chat/                     # Support communication log & chat views
│       ├── layouts/                  # KaiAdmin dashboard layout
│       └── products/                 # Document management views
├── routes/
│   ├── web.php                       # Web application routes
│   └── console.php                   # Artisan scheduled commands
├── storage/                          # Logs, sessions, and uploads
├── ca.pem                            # Aiven Cloud MySQL SSL Certificate Authority
├── composer.json                     # PHP dependencies & autoload hooks
├── package.json                      # Node packages & build scripts
└── Procfile                          # Heroku web dyno process declaration
```

---

## 🚀 Deployment Guide

### Heroku CLI Deployment
Deploy updates directly to Heroku with the Heroku CLI:
```bash
# Login with Heroku API key
heroku auth:whoami

# Deploy main branch
git push heroku main
```

---

## 👨‍💻 Author & Credits

- **Developer**: Muhammad Hamza
- **GitHub**: [@hamzaka430](https://github.com/hamzaka430)
- **Repository**: [hamzaka430/doc_tracker](https://github.com/hamzaka430/doc_tracker)
- **Design & Architecture**: [Dezignwise](https://dezignwise.com)

---
*Doc Tracker — Precision pharmaceutical batch document tracking and secure communication.*
