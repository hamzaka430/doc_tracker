# 📄 Product Requirements Document — DocTracker
**Client:** Healthtek Pvt Ltd  
**Author:** Product Management  
**Version:** 1.0  
**Status:** ✅ Current Build (v1 — Shipped)  
**Last Updated:** June 2026

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [The Problem](#2-the-problem)
3. [Target Users](#3-target-users)
4. [Product Goals](#4-product-goals)
5. [Core Features — Must-Have vs. Nice-to-Have](#5-core-features)
6. [End-to-End User Flow](#6-end-to-end-user-flow)
7. [MVP Definition](#7-mvp-definition)
8. [Success Metrics (KPIs)](#8-success-metrics)
9. [Out of Scope — Version 1](#9-out-of-scope-v1)
10. [Technical Constraints & Assumptions](#10-technical-constraints--assumptions)
11. [Open Questions & Risks](#11-open-questions--risks)

---

## 1. Executive Summary

**DocTracker** is a web-based document lifecycle management system built exclusively for Healthtek Pvt Ltd — a pharmaceutical manufacturer operating in a GMP (Good Manufacturing Practice) environment. The system provides real-time visibility into the status of batch manufacturing documents across every stage of the production lifecycle.

Every pharmaceutical batch requires a set of accompanying documents (batch records, clearance forms, QA sign-offs) that move through multiple departments — Production, Quality Assurance (QA), and SAP/ERP processing — before a batch is considered complete. DocTracker digitizes and centralizes this workflow, replacing fragmented manual tracking (spreadsheets, physical sign-off sheets, verbal handoffs) with a single source of truth that any authorized user can access in real time.

> **One-line pitch:** *DocTracker tells you — at any moment — exactly which batch document is where, who owns it, and what still needs to happen.*

---

## 2. The Problem

### 2.1 Context

Large-scale pharmaceutical manufacturing is highly document-intensive. For every product batch (e.g., Caricef 100mg Suspension, Batch No. H-2501), there are mandatory documents that must pass through specific stages — Pre-Line Clearance, In-Process Checks, Post-Line Clearance — before final QA sign-off. These stages involve multiple people across departments.

### 2.2 Pain Points (Before DocTracker)

| # | Pain Point | Impact |
|---|-----------|--------|
| 1 | **No centralized tracking system** — documents tracked via spreadsheets or memory | Loss of visibility, version conflicts |
| 2 | **Unclear ownership** — no record of who a document is currently with | Responsibility gaps, finger-pointing |
| 3 | **Unknown stage** — impossible to know if a batch was in Pre-Line, In-Process, or QA Sign without physically asking | Production delays |
| 4 | **No audit trail** — no timestamp of when a document moved through each stage | Compliance risk, no accountability |
| 5 | **Manual reporting** — generating end-of-day lists required manual compilation | Wasted supervisor time |
| 6 | **Error blindspots** — SAP transaction errors were informally communicated and frequently repeated | Recurring operational errors |

### 2.3 Root Cause

The absence of a **single digital system** that all production staff could interact with in real time forced coordination to happen through informal channels (WhatsApp messages, verbal updates, physical registers), making the process inherently opaque and error-prone.

---

## 3. Target Users

### Primary Users

| Persona | Role | Primary Use Case |
|---------|------|-----------------|
| **Production Officer** | Adds batch documents to the system daily; marks clearance stages; submits completed documents | Daily document creation and progression |
| **QA Officer** | Reviews document completion status; verifies clearance checkboxes are all marked | Verification and sign-off readiness |
| **Production Supervisor** | Monitors overall batch progress; reviews pending vs. submitted counts; exports daily reports | Oversight and reporting |

### Secondary Users

| Persona | Role |
|---------|------|
| **IT Admin / App Owner** | Manages user accounts, reviews system health |
| **Compliance / Audit Team** | Reviews historical submission logs and exported records |

### User Environment

- **Setting:** Factory floor, production offices, QA lab — predominantly desktop usage with occasional mobile access
- **Technical Literacy:** Moderate — comfortable with basic web forms; not developers
- **Connectivity:** Stable internal LAN or Wi-Fi within the facility

---

## 4. Product Goals

### Business Goals
1. Eliminate document tracking blind spots across all active production batches
2. Reduce time spent on end-of-day reporting by ≥50%
3. Create an auditable, timestamped history of all document submissions
4. Build an institutional knowledge base for SAP errors to reduce repeated mistakes

### Product Goals
1. Provide real-time status visibility for every batch document
2. Make document submission a structured, checkbox-gated workflow (not ad hoc)
3. Enable supervisors to generate daily reports with a single click
4. Support analytics to track team productivity trends over time

### Non-Goals (for v1)
- Replacing SAP or the ERP system
- Becoming a Quality Management System (QMS)
- Managing documents for departments outside Production

---

## 5. Core Features

### 5.1 Must-Have Features (Shipped in v1)

#### 🔐 Authentication & User Management
- Secure login and registration with a custom, branded UI
- Session-based authentication (Laravel Breeze)
- User profile management with avatar upload (AWS S3)
- All data scoped per authenticated user (`user_id` isolation)

> **Why:** Multi-user factory environment — data must be isolated per operator to preserve accountability.

---

#### 📋 Document (Product Batch) Creation
- Create one or multiple batch documents in a single form submission
- Fields per document: Product Name, Batch No., Stage, Type (Injection / Suspension / Tablet / Capsule)
- Smart auto-fill: selecting a product name pre-populates the associated batch code
- Auto-complete suggestions for product names and batch numbers from database
- Predefined product name catalog (e.g., Caricef 100mg Suspension, Oxidil 500mg IV Injection, etc.)
- Custom stage entry alongside predefined stage options

> **Why:** Operators add multiple batches at the start of a shift. A multi-add interface dramatically reduces data entry time and errors.

---

#### 🗂️ Document List & Filtering
- **All Documents view** (`/products`) — complete list with status, type, batch, and stage
- **Pending Documents view** (`/products/pending`) — only unsubmitted documents
- **Daily List view** (`/products/daily`) — pending documents formatted for daily operational use
- **Submitted Documents view** (`/products/submitted`) — completed batches with submission timestamps
- Search by product name, batch number, or stage
- Filter by type (Injection / Suspension / Tablet / Capsule) and date range
- Custom sort order: Suspension → Injection → Capsule → Tablet (by production priority)
- Pagination (25 items per page)

---

#### ✅ Document Progression & Clearance Workflow
- Three-stage clearance gate per document:
  - **Line Clearance** (formerly Pre-Line Clearance)
  - **Review**
  - **Confirmation** (formerly Post-Line Clearance)
- Checkbox completion required before submission is permitted
- Remarks field for notes and exceptions
- Document details page (`/products/{id}`) showing full clearance state

> **Why:** Enforces the mandatory GMP checklist sequence — documents cannot skip stages.

---

#### 🚀 Submission System
- **Single document submit** — submit one fully-cleared document
- **Bulk submit** — select multiple pending documents and submit all in one action
- Auto-timestamps (date + time) on submission
- On submission: stage updates to `Completed`, status updates to `submitted`
- Skips already-submitted documents gracefully in bulk mode

---

#### 🗑️ Recycle Bin (Soft Deletes)
- Delete a document without permanent data loss
- Deleted documents moved to Trash view (`/products/trash`)
- Restore a document from Trash back to active state
- Only editable (pending, non-locked) documents can be deleted

---

#### 📊 Dashboard Analytics
- Line chart showing document submission count for the last 7 days
- Visual trend analysis for supervisor review
- Live count of pending vs. submitted documents

---

#### 📤 Export & Reporting
- **PDF Export** (Daily List): generates a grouped, print-ready PDF of all pending documents
  - Single-column layout option
  - Two-column layout option (space-efficient for printing)
- **CSV Export** (Submitted): downloads all submitted documents with ID, name, batch, stage, submission date/time, and remarks

---

#### 🐛 SAP Error Tracker
- Log SAP errors with: Title, T-Code (e.g., MIGO, ME21N), Description, Screenshot
- Screenshot upload to AWS S3 for durability
- Full CRUD: create, view, edit, delete SAP error entries
- Serves as an internal knowledge base to prevent recurring SAP mistakes

---

#### 🔧 User Preferences
- Per-user settings stored in the database (synced across devices)
- Preference keys stored as JSON values for flexibility
- Used to persist UI preferences (e.g., hidden items, auto-complete behavior)

---

### 5.2 Nice-to-Have Features (Not in v1)

| Feature | Rationale for Deferral |
|---------|----------------------|
| **Role-based access control (RBAC)** | Currently single-role; multi-role needed once team grows |
| **Real-time notifications** (e.g., push/email when a document is stuck) | Adds infra complexity; manual checks suffice for MVP team size |
| **Document assignment to specific users** | Currently self-managed per operator; handoff tracking is a v2 need |
| **QA sign-off as a separate user action** | QA and Production currently use the same interface |
| **Multi-tenancy / Multi-facility** | Healthtek currently operates from one facility |
| **Barcode / QR code scanning** | Useful for physical document tagging; requires hardware integration |
| **Audit log / change history** | Full field-level change tracking per document; compliance enhancement |
| **Mobile app (native)** | Current responsive web is sufficient; native app adds maintenance cost |
| **API for ERP integration** | SAP bi-directional sync is a future phase |
| **Automated reminders/escalations** | Scheduled job to alert supervisors about stale pending documents |
| **Dark mode** | UI polish; not a functional priority |
| **Multi-language support** | English-only for Healthtek's current team |

---

## 6. End-to-End User Flow

### 6.1 Daily Operator Flow

```
[Login]
   │
   ▼
[Dashboard / All Documents View]
   │  Sees pending count, 7-day submission chart
   │
   ▼
[Add Document(s)]  ──►  /products/create
   │  Selects product name (auto-fill batch code)
   │  Enters stage, selects type
   │  Can add multiple rows in one submission
   │
   ▼
[Pending Documents View]  ──►  /products/pending
   │  Sees newly added documents in queue
   │
   ▼
[Open Document Details]  ──►  /products/{id}
   │  Marks: ✅ Line Clearance
   │  Marks: ✅ Review
   │  Marks: ✅ Confirmation
   │  Adds remarks (optional)
   │
   ▼
[Submit Document]
   │  System validates all 3 clearances are checked
   │  Records submission timestamp
   │  Status → "Submitted", Stage → "Completed"
   │
   ▼
[Repeat for remaining pending documents]
   │  OR use Bulk Submit to process multiple at once
   │
   ▼
[End of Shift: Export Daily PDF]  ──►  /products/daily/pdf
   │  Downloads print-ready grouped batch list
   │
   ▼
[Logout]
```

### 6.2 Supervisor Reporting Flow

```
[Login]
   │
   ▼
[Dashboard]
   │  Reviews 7-day trend chart
   │  Checks total pending vs. submitted counts
   │
   ▼
[Submitted View]  ──►  /products/submitted
   │  Filters by date range
   │  Reviews completion timestamps
   │
   ▼
[Export CSV]  ──►  /products/export
   │  Downloads full submission history
   │
   ▼
[Adjust Submission Date if needed]
   │  Edit submission date/time on a specific document
```

### 6.3 SAP Error Logging Flow

```
[Encounter SAP Error during work]
   │
   ▼
[Navigate to SAP Errors]  ──►  /sap-errors/create
   │  Enter: Title, T-Code, Description
   │  Upload: Error screenshot (stored on AWS S3)
   │
   ▼
[Error saved to Knowledge Base]
   │
   ▼
[Colleagues search SAP Errors]  ──►  /sap-errors
   │  Browse by T-Code or title
   │  View screenshot + description for resolution steps
```

### 6.4 Recovery Flow (Deleted Document)

```
[Document accidentally deleted]
   │
   ▼
[Navigate to Recycle Bin]  ──►  /products/trash
   │  Locate document by name/batch
   │
   ▼
[Restore]
   │  Document returns to Pending state
```

---

## 7. MVP Definition

### What "MVP" Means for DocTracker

The MVP is the **minimum feature set that makes DocTracker more useful than a spreadsheet** for a production team of 5–20 operators. It must:
- Replace the manual tracking register entirely
- Provide real-time status for any active batch document
- Support end-of-shift reporting without manual compilation

### MVP Feature Set (Already Shipped)

| Feature | Status |
|---------|--------|
| User authentication (login/register) | ✅ Shipped |
| Multi-batch document creation | ✅ Shipped |
| Pending / Submitted / All document views | ✅ Shipped |
| Three-stage clearance workflow per document | ✅ Shipped |
| Single and bulk document submission | ✅ Shipped |
| PDF export (daily remaining documents) | ✅ Shipped |
| CSV export (submitted documents) | ✅ Shipped |
| 7-day dashboard analytics chart | ✅ Shipped |
| Soft deletes with Recycle Bin | ✅ Shipped |
| SAP Error knowledge base | ✅ Shipped |
| AWS S3 for file storage | ✅ Shipped |
| User profile with avatar | ✅ Shipped |
| Per-user data isolation | ✅ Shipped |

> **Assessment:** DocTracker has shipped a **complete, production-ready MVP** that significantly exceeds the minimum viable definition. The SAP Error tracker, PDF export with layout options, and user preferences sync are features typically deferred to v1.5 or v2.

---

## 8. Success Metrics

### 8.1 Adoption Metrics

| Metric | Target | Measurement Method |
|--------|--------|--------------------|
| Daily Active Users (DAU) | ≥80% of production staff log in each working day | Laravel session logs |
| Documents created per day | ≥90% of physical batch count digitized | DB count vs. physical register |
| Time to first document creation (new user) | < 5 minutes | Onboarding observation |

### 8.2 Operational Efficiency Metrics

| Metric | Target | Measurement Method |
|--------|--------|--------------------|
| End-of-shift reporting time | Reduced by ≥50% vs. pre-DocTracker | Supervisor time log |
| Document status lookup time | < 30 seconds per document | User testing |
| Zero-query visibility | Supervisor can answer "where is Batch X?" without asking anyone | User interviews |

### 8.3 Quality & Compliance Metrics

| Metric | Target | Measurement Method |
|--------|--------|--------------------|
| Documents submitted without all 3 clearances | 0 (system-enforced) | DB: submissions where any clearance = false |
| Data loss incidents | 0 | Soft delete + recycle bin prevents permanent loss |
| SAP error recurrence (logged errors) | ≥20% reduction after 60 days | SAP Error KB usage tracking |

### 8.4 System Health Metrics

| Metric | Target |
|--------|--------|
| Uptime | ≥99.5% during working hours |
| Page load time | < 2s on internal LAN |
| Export generation time (PDF/CSV) | < 5 seconds for up to 200 records |

---

## 9. Out of Scope — Version 1

These are deliberate exclusions. They may be considered for v2 based on user feedback and organizational growth.

### ❌ Not Building in v1

| Item | Reason |
|------|--------|
| **Role-Based Access Control (RBAC)** | Team size and workflow don't yet require differentiated permissions; all operators have equivalent access |
| **Document assignment / handoff tracking** | Documents are currently self-managed per user; inter-user handoff is a v2 workflow |
| **Real-time notifications (push/email/SMS)** | Polling the pending list is sufficient; notification infra adds disproportionate complexity |
| **SAP / ERP bi-directional sync** | DocTracker is a tracking layer, not an ERP replacement; integration requires SAP API access and a separate engagement |
| **Barcode/QR code integration** | Physical document tagging requires hardware; out of software scope |
| **Full audit log (field-level change history)** | Timestamps on creation/submission exist; per-field change log is a compliance v2 feature |
| **Multi-facility / multi-tenant support** | Single-site deployment for Healthtek; generalization increases complexity with no current demand |
| **Native mobile application** | Responsive web serves the use case; native app unjustifiable at this team scale |
| **In-app messaging or comments** | Remarks field covers light communication needs; Slack/WhatsApp handles the rest |
| **Automated escalation rules** | Alerts when a document has been pending > N hours; schedulable for v2 |
| **Advanced analytics (predictive, historical trends)** | 7-day chart serves v1 needs; deeper BI is a later phase |
| **Multi-language / localization** | English-only for current Healthtek team |
| **Third-party authentication (SSO, Google)** | Not required for internal tool with small user base |

---

## 10. Technical Constraints & Assumptions

### Stack
| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Blade Templates + TailwindCSS + Bootstrap 5 |
| Database | MySQL 5.7+ |
| File Storage | AWS S3 (profile avatars, SAP error screenshots) |
| Build Tool | Vite |
| Deployment | Heroku (Procfile present) |

### Constraints
- **Single-database architecture:** All tenants (users) share one database; isolation is by `user_id` column, not schema-level separation
- **No real-time stack:** No WebSockets or broadcasting; all updates require a page reload or AJAX call
- **Predefined product catalog:** Product names and batch code mappings are seeded in the codebase; dynamic catalog management is not in v1
- **Stage definition is flexible but unstructured:** Stages like "On Process", "QA Sign", "Production" are freeform strings; no enforced stage transition rules exist

### Assumptions
- Healthtek operates from a single production site
- All users have a browser-capable device (PC or tablet) on the factory floor
- The facility has stable internal network connectivity
- An administrator will onboard new users manually (no self-serve registration is exposed in production)

---

## 11. Open Questions & Risks

### Open Questions

| # | Question | Impact | Owner |
|---|----------|--------|-------|
| Q1 | Should documents be assignable to a specific operator, or remain self-managed? | Workflow design for v2 | Product + Operations Lead |
| Q2 | Is there a regulatory requirement (e.g., FDA 21 CFR Part 11) for electronic records and signatures? | May mandate full audit logging and e-signatures | Compliance Team |
| Q3 | How should supervisors access data across all operators (not just their own)? | Requires RBAC implementation | Product + IT |
| Q4 | What is the expected growth in product catalog? Will predefined names stay static? | Determines if catalog management UI is needed | Production Team |
| Q5 | Should submitted documents be permanently locked, or can corrections be made with an audit note? | Compliance vs. operational flexibility trade-off | QA + Operations |

### Risks

| Risk | Severity | Mitigation |
|------|----------|-----------|
| **Adoption failure** — operators revert to spreadsheets out of habit | High | Supervised onboarding, quick-win demos, reduce data entry friction |
| **Data isolation gap** — operators can theoretically see each other's data if `user_id` filtering fails | Medium | Code audit; consider row-level security enforcement |
| **Predefined catalog drift** — real products added to batches but not in the catalog cause manual entry errors | Medium | Build a dynamic product catalog management UI in v1.5 |
| **Single point of failure** — no offline fallback if the server goes down | Medium | Ensure hosting uptime SLA; document manual backup procedure |
| **Compliance gap** — if GMP audit requires electronic signatures (not just timestamps) | High | Engage compliance team early; design e-signature module for v2 |
| **S3 cost overrun** — screenshot uploads grow unbounded | Low | Implement file size limits and retention policy |

---

## Appendix: Data Model Summary

### Products Table (Core)

| Column | Type | Purpose |
|--------|------|---------|
| `id` | BIGINT PK | Unique document identifier |
| `user_id` | BIGINT FK | Owner/creator (auth isolation) |
| `name` | VARCHAR(255) | Product name (e.g., "Caricef 100mg Suspension") |
| `batch_no` | VARCHAR(255) | Batch number |
| `stage` | VARCHAR(255) | Current stage (freeform string) |
| `type` | ENUM | Injection / Suspension / Tablet / Capsule |
| `status` | ENUM | `pending` / `submitted` |
| `line_clearance` | BOOLEAN | Clearance gate 1 |
| `review` | BOOLEAN | Clearance gate 2 |
| `confirmation` | BOOLEAN | Clearance gate 3 |
| `remarks` | TEXT | Optional notes |
| `submission_date` | DATE | Date of submission |
| `submission_time` | TIME | Time of submission |
| `deleted_at` | TIMESTAMP | Soft delete timestamp (Recycle Bin) |

### SAP Errors Table

| Column | Purpose |
|--------|---------|
| `title` | Short error label |
| `sap_tcode` | Transaction code (e.g., MIGO) |
| `description` | Full error explanation |
| `image_path` | AWS S3 URL to screenshot |

---

*Document prepared based on codebase audit of DocTracker v1 (Laravel 11, June 2026 build) and product context provided by the client.*
