# 🎨 Frontend Specification Document — DocTracker
**Client:** Healthtek Pvt Ltd  
**Author:** UI/UX & Frontend Architecture Review  
**Version:** 1.0 (Audited from live codebase — Laravel 11 + Kaiadmin + Bootstrap 5 + Tailwind)  
**Audience:** Frontend Developers, Designers, and Technical Leads

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Design System — Colors](#2-design-system--colors)
3. [Design System — Typography](#3-design-system--typography)
4. [Design System — Spacing & Layout](#4-design-system--spacing--layout)
5. [Component Specifications](#5-component-specifications)
   - Buttons
   - Inputs & Forms
   - Cards
   - Modals
   - Badges & Status Indicators
   - Tables
   - Progress Bars
   - Alerts / Flash Messages
   - Autocomplete Dropdown
   - Sidebar Navigation
6. [Page-Level Layout Rules](#6-page-level-layout-rules)
7. [JavaScript Interaction Patterns](#7-javascript-interaction-patterns)
8. [Third-Party Service Integration Spec](#8-third-party-service-integration-spec)
9. [Responsive Design Rules](#9-responsive-design-rules)
10. [Known Tech Debt & Recommendations](#10-known-tech-debt--recommendations)

---

## 1. Architecture Overview

### Dual-Framework Design System

DocTracker uses **two CSS frameworks simultaneously**, which is the most important thing to understand before touching any frontend code.

| Framework | Where It Lives | What It Controls |
|-----------|---------------|-----------------|
| **Kaiadmin** (admin template) | `public/Dashboard/assets/css/kaiadmin.min.css` | Sidebar, navbar, cards, buttons, tables, badges — the entire app shell |
| **Bootstrap 5** | `public/Dashboard/assets/css/bootstrap.min.css` | Grid system, utilities, modals, form controls, progress bars |
| **TailwindCSS** | `resources/css/app.css` → compiled to `public/build/` | Auth pages only (login, register) — all Tailwind classes |
| **Custom CSS** | `public/Dashboard/assets/css/custom.css` | Overrides and one-off styles for dashboard pages |

**Critical rule:** Auth pages (`login.blade.php`, `register.blade.php`) use **Tailwind only** — they load via `@vite`. All other pages use **Kaiadmin + Bootstrap** — they load from `public/Dashboard/assets/`. These two groups should **never mix**. Do not use `@vite` in dashboard pages or Kaiadmin classes in auth pages.

### JavaScript Stack

| Library | Source | Role |
|---------|--------|------|
| **jQuery 3.7.1** | `/Dashboard/assets/js/core/jquery-3.7.1.min.js` | DOM manipulation, event binding across all dashboard pages |
| **Bootstrap 5 JS** | `/Dashboard/assets/js/core/bootstrap.min.js` | Modal open/close, tooltips, dropdowns, alerts |
| **Popper.js** | `/Dashboard/assets/js/core/popper.min.js` | Tooltip/popover positioning (required by Bootstrap) |
| **Kaiadmin JS** | `/Dashboard/assets/js/kaiadmin.min.js` | Sidebar toggle, sidebar animation, nav-item activation |
| **jQuery Scrollbar** | `/Dashboard/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js` | Custom scrollbar in sidebar |
| **jQuery Datatables** | `/Dashboard/assets/js/plugin/datatables/datatables.min.js` | Available but used only on specific pages |
| **Alpine.js** | Bundled via Vite (npm `alpinejs`) | Auth pages only — reactive UI |
| **Chart.js** | CDN: `https://cdn.jsdelivr.net/npm/chart.js` | Dashboard analytics line chart |

---

## 2. Design System — Colors

### 2.1 Primary Palette (Kaiadmin Theme)

These are the core colors extracted from actual usage throughout the Blade views and JS.

| Token Name | Hex | Usage |
|-----------|-----|-------|
| **Primary Blue** | `#1572E8` | Primary buttons (`btn-primary`), active sidebar links, focused inputs, chart accent |
| **Info Cyan** | `#48ABF7` | Info buttons (`btn-info`), edit action icons |
| **Success Green** | `#31CE36` | Success badges, submitted status, success alert backgrounds, progress bars |
| **Warning Amber** | `#FFAD46` | Warning badges, pending status indicator in topbar |
| **Danger Red** | `#F25961` | Danger buttons, delete actions, error alert backgrounds |
| **Dark Navy** | `#1A1A2E` | Sidebar background (`data-background-color="dark"`), dark cards, dark badges on type |
| **Secondary Gray** | `#6C757D` | Secondary buttons, muted text, input placeholders |
| **Light Gray** | `#F4F5F7` | Page background, table striping, light form group backgrounds |
| **White** | `#FFFFFF` | Card backgrounds, modal backgrounds, input backgrounds |

### 2.2 Analytics Chart Colors

Extracted from `index.blade.php` Chart.js configuration:

| Token | Hex | Usage |
|-------|-----|-------|
| **Chart Line** | `#00F2FE` | Line chart border color — bright cyan |
| **Chart Fill** | `rgba(0, 242, 254, 0.1)` | Line chart area fill — semi-transparent cyan |
| **Chart Points** | `#4FACFE` | Data point circles — lighter blue |
| **Chart Point Hover** | `#FFFFFF` | Hover state of data point background |
| **Chart Grid Lines** | `rgba(255, 255, 255, 0.1)` | Y-axis grid lines on dark card |
| **Chart Tick Text** | `rgba(255, 255, 255, 0.7)` | Axis labels on dark card |

### 2.3 Status Color Semantics

Every document has a status. Colors must consistently communicate the same meaning across the entire app:

| Status | Color | Hex (approx.) | Class Used |
|--------|-------|---------------|-----------|
| Pending | Amber/Yellow | `#FFAD46` | `badge-warning` / `status-pending` |
| Submitted | Green | `#31CE36` | `badge-success` / `status-submitted` |
| Completed (stage) | Green | `#31CE36` | `badge-success` |
| In Progress | Blue | `#1572E8` | `badge-primary` |

### 2.4 Auth Page Palette (Tailwind)

Used only on `login.blade.php` and `register.blade.php`:

| Token | Tailwind Class | Hex Equivalent | Usage |
|-------|---------------|---------------|-------|
| Page background | `bg-gray-50` | `#F9FAFB` | Full page background |
| Card background | `bg-white` | `#FFFFFF` | Login/register form card |
| Card border | `border-slate-300` | `#CBD5E1` | Form card border |
| Primary button | `bg-blue-600` | `#2563EB` | Sign in / Sign up buttons |
| Button hover | `bg-blue-700` | `#1D4ED8` | Button hover state |
| Input border | `outline-slate-300` | `#CBD5E1` | Default input outline |
| Input focus | `outline-blue-600` | `#2563EB` | Focused input outline |
| Label text | `text-slate-900` | `#0F172A` | Form labels |
| Muted text | `text-slate-600` | `#475569` | Subtitle / help text |
| Error text | `text-red-600` | `#DC2626` | Validation error messages |
| Link color | `text-blue-700` | `#1D4ED8` | "Forgot password?" and "Sign up" links |

---

## 3. Design System — Typography

### 3.1 Dashboard Font — Public Sans

**Font:** Public Sans  
**Source:** Google Fonts, loaded via WebFont Loader from `public/Dashboard/assets/js/plugin/webfont/webfont.min.js`

```javascript
WebFont.load({
    google: { families: ["Public Sans:300,400,500,600,700"] }
});
```

| Weight | Name | Usage |
|--------|------|-------|
| 300 | Light | Rarely used — only very subtle helper text |
| 400 | Regular | Body text, table cells, form values |
| 500 | Medium | Nav items, card subtitles |
| 600 | Semi-Bold | Card headers, badges, table column values |
| 700 | Bold | Page titles (`h3.fw-bold`), key metrics |

**Page titles:** `h3` with `.fw-bold` class → `font-weight: 700`  
**Card titles:** `h4.card-title` → `font-weight: 600`  
**Table heading:** `th` inside `thead` → `font-weight: 600`  
**Body default:** 14px base size, 1.5 line-height  
**Small/muted:** `small.text-muted` → 12px, `#6C757D`

### 3.2 Auth Page Font — Figtree + Tailwind Defaults

**Font:** Figtree  
**Source:** Configured in `tailwind.config.js` as the default sans-serif font

```javascript
theme: {
    extend: {
        fontFamily: {
            sans: ['Figtree', ...defaultTheme.fontFamily.sans],
        },
    },
}
```

| Element | Tailwind Classes | Size / Weight |
|---------|-----------------|--------------|
| App name brand | `text-xl font-bold` | 20px / 700 |
| Page heading | `text-2xl font-bold` | 24px / 700 |
| Subtitle | `text-sm leading-relaxed` | 14px / 400 |
| Labels | `text-xs font-medium` | 12px / 500 |
| Input text | `text-sm` | 14px / 400 |
| Button text | `text-sm font-semibold` | 14px / 600 |
| Link text | `text-xs font-medium` | 12px / 500 |
| Error text | `text-xs` | 12px / 400 |

### 3.3 Icon Libraries

| Library | Source | Prefix | Used For |
|---------|--------|--------|---------|
| Font Awesome 5 Solid | Bundled with Kaiadmin | `fas` `fa` | Nav icons, action buttons, form icons |
| Font Awesome 5 Regular | Bundled with Kaiadmin | `far` | Calendar icon on mobile cards |
| Font Awesome 5 Brands | Bundled with Kaiadmin | `fab` | Not actively used |
| Simple Line Icons | Bundled with Kaiadmin | `icon-` | Breadcrumb home/arrow icons (`icon-home`, `icon-arrow-right`) |
| Kaiadmin Icons | Bundled with Kaiadmin | `gg-` | Sidebar toggle buttons (`gg-menu-right`, `gg-menu-left`, `gg-more-vertical-alt`) |

---

## 4. Design System — Spacing & Layout

### 4.1 Dashboard Layout Shell

```
┌─────────────────────────────────────────────────┐
│  .wrapper (flex row, full viewport height)       │
│                                                  │
│  ┌──────────┐  ┌────────────────────────────┐   │
│  │ .sidebar │  │ .main-panel                │   │
│  │          │  │                            │   │
│  │ width:   │  │ ┌────────────────────────┐ │   │
│  │ 250px    │  │ │ .main-header           │ │   │
│  │          │  │ │  (navbar + topbar)      │ │   │
│  │ collapsed│  │ └────────────────────────┘ │   │
│  │ width:   │  │                            │   │
│  │ 75px     │  │ ┌────────────────────────┐ │   │
│  │          │  │ │ .container             │ │   │
│  │          │  │ │   .page-inner          │ │   │
│  │          │  │ │     @yield('content')  │ │   │
│  │          │  │ └────────────────────────┘ │   │
│  │          │  │                            │   │
│  │          │  │ ┌────────────────────────┐ │   │
│  │          │  │ │ .footer                │ │   │
│  │          │  │ └────────────────────────┘ │   │
│  └──────────┘  └────────────────────────────┘   │
└─────────────────────────────────────────────────┘
```

### 4.2 Spacing Scale (Bootstrap 5 utilities used)

| Space Token | Rem Value | Pixel Equivalent | Usage |
|-------------|-----------|-----------------|-------|
| `mb-1` | 0.25rem | 4px | Tight element spacing |
| `mb-2` | 0.5rem | 8px | Close related elements |
| `mb-3` | 1rem | 16px | Standard section spacing |
| `mb-4` | 1.5rem | 24px | Between major form groups, cards |
| `mb-5` | 3rem | 48px | Large section separation |
| `p-2` | 0.5rem | 8px | Compact badge/chip padding |
| `p-3` | 1rem | 16px | Document row inner padding |
| `px-2` | 0.5rem | 8px | Horizontal padding on codes/badges |
| `py-1` | 0.25rem | 4px | Vertical padding on codes/badges |
| `gap-2` | 0.5rem | 8px | Flex gap between action buttons |
| `gap-3` | 1rem | 16px | Topbar stat badge gap |

### 4.3 Grid System

**Dashboard pages:** Bootstrap 5 12-column grid  
- Full width card: `col-md-12`
- Half-half columns: `col-md-6`
- Filter form: `col-md-3`, `col-md-2`, `col-md-3`
- Document form fields: `col-md-6 col-lg-3` (2 columns on medium, 4 on large)

**Auth pages:** Tailwind grid  
- Layout: `grid lg:grid-cols-2` — form on left, illustration on right on large screens; stacked on mobile

### 4.4 Container Width

Dashboard uses `.container` class — standard Bootstrap container with `max-width` responsive breakpoints.

---

## 5. Component Specifications

### 5.1 Buttons

All dashboard buttons use Kaiadmin's extended Bootstrap button system.

#### Variants

| Variant | Class | Hex Background | Use Case |
|---------|-------|---------------|---------|
| Primary | `btn btn-primary` | `#1572E8` | Main CTA: Add Document, Filter, Submit |
| Success | `btn btn-success` | `#31CE36` | Save actions: Save Documents, Save Error |
| Danger | `btn btn-danger` | `#F25961` | Destructive: Cancel, Delete, Logout dropdown |
| Secondary | `btn btn-secondary` | `#6C757D` | Non-destructive cancel: Clear filter |
| Dark | `btn btn-dark` | `#1A1A2E` | Alternate CTA: Add New Document (empty state) |
| Info | `btn btn-info` | `#48ABF7` | Informational: not used for primary actions |
| Link-Primary | `btn btn-link btn-primary` | Transparent | Table action icons (view) |
| Link-Info | `btn btn-link btn-info` | Transparent | Table action icons (edit) |
| Link-Danger | `btn btn-link btn-danger` | Transparent | Table action icons (delete) |

#### Modifiers

| Modifier | Class | Effect |
|----------|-------|--------|
| Rounded | `btn-round` | Full pill border-radius |
| Small | `btn-sm` | Smaller padding, smaller text |
| Icon only | `btn-icon` | Square aspect ratio, icon centered |
| Border only | `btn-border` | Outlined style |
| Full width | Add `w-100` or `flex-grow-1` | Fills container |

#### States

| State | Visual |
|-------|--------|
| Default | Solid color, white text |
| Hover | Slightly darker shade of same color |
| Disabled | `disabled` attribute → 50% opacity, no cursor |
| Loading | `fa-spinner fa-spin` icon injected, button disabled |

#### Auth Page Buttons (Tailwind)

```html
<button class="w-full py-1.5 px-3.5 text-sm rounded-md font-semibold 
               cursor-pointer tracking-wide text-white border border-blue-600 
               bg-blue-600 hover:bg-blue-700 transition-all 
               focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
```

**Height:** `py-1.5` = 6px vertical padding  
**Border radius:** `rounded-md` = 6px  
**Transition:** `transition-all` on all properties  
**Focus ring:** `focus-visible:ring-2 focus-visible:ring-blue-500`

---

### 5.2 Inputs & Form Controls

#### Dashboard Form Controls (Bootstrap)

```html
<input type="text" class="form-control" placeholder="...">
<select class="form-select form-control" name="type">
<textarea class="form-control" rows="4">
```

| State | Visual |
|-------|--------|
| Default | `border: 1px solid #DEE2E6`, `background: #FFFFFF`, `border-radius: 4px` |
| Focus | Blue outline — color defined by Kaiadmin theme |
| Error | `.border-red-500` or error class — red border |
| Disabled | Gray background, no cursor |

**Input group (search bar):**
```html
<div class="input-group">
    <span class="input-group-text"><i class="fa fa-search"></i></span>
    <input type="text" name="search" class="form-control" placeholder="Search...">
</div>
```

**Label style:**
```html
<label>Field Name <span class="text-danger">*</span></label>
```
Required fields always show a red asterisk.

**Error feedback under field:**
```html
<small class="form-text text-danger">{{ $message }}</small>
```

#### Auth Page Inputs (Tailwind)

```html
<input type="email" 
       class="px-2.5 py-1.5 text-sm text-slate-900 rounded-md bg-white w-full border 
              outline-1 -outline-offset-1 outline-slate-300 
              focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 
              @error('email') border-red-500 @enderror">
```

| State | Classes |
|-------|---------|
| Default | `outline-1 outline-slate-300` |
| Focus | `focus:outline-2 focus:outline-blue-600` |
| Error | `border-red-500` (added by `@error` Blade directive) |

**Error text under auth inputs:**
```html
<p class="mt-1 text-xs text-red-600">{{ $message }}</p>
```

#### Form Group Spacing Rule

All dashboard form groups have:
```css
.form-group { margin-bottom: 1.5rem; }
```

This is defined inline in `create.blade.php` `@push('styles')`.

---

### 5.3 Cards

Cards are the primary content container across the entire dashboard.

#### Standard Card Anatomy

```html
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h4 class="card-title">Title Here</h4>
            <a href="#" class="btn btn-primary btn-round ms-auto">Action</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Content -->
        <div class="card-action">  <!-- Bottom action bar -->
            <button type="submit" class="btn btn-success">Save</button>
            <a href="#" class="btn btn-danger">Cancel</a>
        </div>
    </div>
</div>
```

**Card action bar:**
```css
.card-action {
    padding-top: 1rem;
    border-top: 1px solid #eee;
}
```

#### Dark Analytics Card

```html
<div class="card bg-dark text-white shadow-sm">
    <div class="card-body">
        <h5 class="card-title text-white mb-3">
            <i class="fas fa-chart-line me-2"></i>Submission Analytics
        </h5>
        <div style="height: 250px;">
            <canvas id="submissionChart"></canvas>
        </div>
    </div>
</div>
```
Chart card height: fixed `250px`  
Background: `#1A1A2E` (Bootstrap `bg-dark`)  
Title: white, with Font Awesome icon prefix

#### Mobile Document Card (Pending Page)

Used only on `< lg` breakpoint. Each pending document renders as a card instead of a table row:

```html
<div class="card shadow-sm mb-3 product-card">
    <div class="card-body p-3">
        <!-- Checkbox + Product Name + Date -->
        <!-- 2-col info grid: Batch | Type -->
        <!-- Full-width: Stage -->
        <!-- Progress bar (8px height) -->
        <!-- Action buttons: flex row, outline style -->
    </div>
</div>
```

Mobile card buttons use outline variants: `btn-outline-primary`, `btn-outline-info`, `btn-outline-danger`

---

### 5.4 Modals

All modals use Bootstrap 5 modal system.

#### Submit Confirmation Modal

```html
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this product?</p>
                <div class="alert alert-info">
                    <strong>{{ $product->name }}</strong>
                    <small>Batch: {{ $product->batch_no }} | Stage: {{ $product->stage }}</small>
                </div>
                <p class="text-muted mb-0">Once submitted, this product cannot be modified.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.submit', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Yes, Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
```

#### Delete Confirmation Modal

```html
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <!-- Red header distinguishes destructive modal from submit modal -->
        </div>
    </div>
</div>
```

**Key difference:** Delete modal header uses `bg-danger text-white` + `btn-close-white` to visually signal a destructive action. Submit modal has a standard white header.

**Trigger pattern:**
```html
<button data-bs-toggle="modal" data-bs-target="#submitModal">Submit</button>
```
Modals are only rendered in the DOM if the relevant condition is met (e.g., `@if($product->isReadyForSubmission() && !$product->isSubmitted())`).

---

### 5.5 Badges & Status Indicators

#### Status Badges

```html
<span class="badge badge-success">Submitted</span>
<span class="badge badge-warning">Pending</span>
```

Kaiadmin's `.badge-*` classes differ slightly from Bootstrap's `bg-*` — they include padding, border-radius, and font-weight already configured.

#### Dynamic Status Badge (Pending Page)

```html
<span class="badge status-{{ strtolower($product->status) }}">
    {{ ucfirst($product->status) }}
</span>
```

This uses dynamically generated class names: `.status-pending` (amber) and `.status-submitted` (green).

#### Type Badge

```html
<span class="badge bg-dark text-white">{{ $product->type }}</span>
```

All four product types (Injection, Suspension, Tablet, Capsule) use the same dark badge.

#### Stage Badge

```html
<span class="stage-badge">{{ $product->stage }}</span>
```

`.stage-badge` is a custom class defined in `custom.css` — should display with a neutral background, subtle border, and the stage text.

#### Topbar Stat Badges

```html
<div class="d-flex align-items-center px-3 py-1 rounded bg-warning text-white">
    <i class="fas fa-hourglass-half me-2"></i>
    <span class="fw-bold" style="font-size: 0.85rem;">{{ $pendingCount }} Pending</span>
</div>
<div class="d-flex align-items-center px-3 py-1 rounded bg-success text-white">
    <i class="fas fa-check-double me-2"></i>
    <span class="fw-bold" style="font-size: 0.85rem;">{{ $submittedCount }} Submitted</span>
</div>
```

These appear in the navbar on large screens only (`d-none d-lg-flex`). Real-time counts are calculated by PHP on every page load.

---

### 5.6 Tables

All document list tables use: `table table-striped table-hover` on All Documents. Pending page uses `table table-hover` without striping.

#### Standard Table Structure

```html
<div class="table-responsive">
    <table class="display table table-striped table-hover">
        <thead>
            <tr>
                <th>Document Name</th>
                <th>Batch No</th>
                <th>Type</th>
                <th>Stage</th>
                <th>Status</th>
                <th>Progress</th>
                <th style="width: 10%">Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows -->
        </tbody>
    </table>
</div>
```

**Product name cell pattern:**
```html
<td>
    <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
    <small class="text-muted">{{ $product->created_at->format('M d, Y') }}</small>
</td>
```

**Batch number cell pattern:**
```html
<td>
    <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
</td>
```

**Empty state (no results):**
```html
<tr>
    <td colspan="7" class="text-center py-5">
        <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
        <h5 class="text-muted">No Documents Found</h5>
        <p class="text-muted">Start by adding your first document.</p>
        <a href="#" class="btn btn-primary mt-3">
            <i class="fa fa-plus me-2"></i>Add First Document
        </a>
    </td>
</tr>
```

**Pagination:**
```html
{{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
```
Centered with `d-flex justify-content-center`, spaced with `mt-4`.

---

### 5.7 Progress Bars

**Large (Document Detail page):**
```html
<div class="progress progress-lg" style="">
    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
        {{ $completed }}/3 Tasks ({{ number_format($percentage, 0) }}%)
    </div>
</div>
```
```css
.progress-lg { height: 30px; }
```

**Standard (Table rows):**
```html
<div class="progress" style="height: 20px;">
    <div class="progress-bar bg-success" ...>{{ $completed }}/3</div>
</div>
```

**Slim (Mobile cards):**
```html
<div class="progress" style="height: 8px;">
    <div class="progress-bar bg-dark" ...></div>
</div>
```

**Rule:** Progress bars always display `$completed / 3` tasks. When all 3 are done, bar is full width and green. Progress bar colors: `bg-success` (green) on desktop, `bg-dark` (navy) on mobile cards.

---

### 5.8 Alerts & Flash Messages

Flash messages are rendered in `dashboard.blade.php` and appear at the top of every page's content area, above `@yield('content')`.

#### Success Alert

```html
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
```

#### Error Alert

```html
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
```

#### Validation Error List Alert

```html
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Validation Errors:</strong>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

All alerts: `fade show` classes enable Bootstrap's slide-down animation. `alert-dismissible` adds the close button. All are dismissible by the user.

**Session keys used:** `success`, `error`, `warning` (warning used by bulk submit partial results).

---

### 5.9 Autocomplete Dropdown

A fully custom JavaScript autocomplete — not a library. Lives in `create.blade.php`.

**Anatomy:**
```html
<div class="dropdown">  <!-- wrapper gets 'dropdown' class added by JS -->
    <input type="text" class="form-control" name="name[]">
    <ul class="dropdown-menu w-100 shadow-sm autocomplete-menu" 
        style="max-height: 200px; overflow-y: auto;">
        <li class="d-flex align-items-center justify-content-between pe-2">
            <a class="dropdown-item py-2 flex-grow-1" href="#">
                Matched text with <strong class="text-primary">highlighted</strong> portion
            </a>
            <button class="btn btn-link text-danger p-0 ms-2">
                <i class="fa fa-times"></i>  <!-- Remove from suggestions -->
            </button>
        </li>
        <!-- "Add new" option at bottom: -->
        <li>
            <a class="dropdown-item py-2 text-success fw-bold" href="#">
                <i class="fa fa-plus-circle me-1"></i> Add "custom value" to suggestions
            </a>
        </li>
    </ul>
</div>
```

**Behavior:**
- Triggers on `input` event (every keystroke)
- Filters by `.toLowerCase().includes()` — case-insensitive substring match
- Matched portion is wrapped in `<strong class="text-primary">` for visual highlighting
- Each suggestion has a red `×` to hide it (saved to `user_preferences`)
- If typed text has no exact match → "Add to suggestions" option appears in green
- Clicking outside dismisses dropdown
- Selecting a name from the dropdown auto-fills the batch number if it exists in `productBatchMap`

---

### 5.10 Sidebar Navigation

```html
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="#" class="logo">
                <span class="navbar-brand text-white fw-bold" style="font-size: 1.2rem;">Doc Tracker</span>
            </a>
            <!-- Toggle buttons -->
        </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fas fa-th-list"></i>
                        <p>All Documents</p>
                    </a>
                </li>
                <!-- Section divider -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Actions</h4>
                </li>
            </ul>
        </div>
    </div>
</div>
```

**Active state:** determined server-side using `request()->routeIs()` — adds `.active` class to `li.nav-item`.

**Sidebar sections:**

| Section | Nav Items |
|---------|-----------|
| (unlabeled) | All Documents, Pending Documents, Submitted Documents, Daily Documents, SAP Errors |
| Actions | Add New Document, Export to CSV, Recycle Bin |

**Icon mapping:**

| Page | Icon Class |
|------|-----------|
| All Documents | `fas fa-th-list` |
| Pending | `fas fa-hourglass-half` |
| Submitted | `fas fa-check-double` |
| Daily | `fas fa-calendar-alt` |
| SAP Errors | `fas fa-exclamation-triangle` |
| Add Document | `fa fa-plus` |
| Export CSV | `fa fa-file-excel` |
| Recycle Bin | `fas fa-trash` |

---

## 6. Page-Level Layout Rules

### All Documents (`/products`)
- Full-width dark analytics card (Chart.js) at top
- Full-width document table card below
- Filter bar: `col-md-3` search | `col-md-2` type | `col-md-2` date from | `col-md-2` date to | `col-md-3` buttons
- Table columns: Name + date sub-row | Batch (code style) | Type (dark badge) | Stage (stage-badge) | Status (color badge) | Progress bar | Actions (icon buttons)

### Pending Documents (`/products/pending`)
- Table view on `≥ lg` (992px+): same columns as All Documents + checkbox column
- Card view on `< lg`: one card per document with 2-col grid info layout
- Bulk submit button in filter bar: disabled until ≥ 1 checkbox selected
- Bulk count displayed in button: `Submit (N)`

### Add Document (`/products/create`)
- Single card with dynamic multi-row form
- Each row: 4 columns (Name | Batch | Stage | Type) on large, 2 on medium
- "Add Another Document" and "Duplicate Last" buttons below rows
- Form auto-saves to `localStorage` on every input change
- Drafts restored on page load with a notification toast

### Document Detail (`/products/{id}`)
- Two cards stacked: Document Information (display only) + Update Details (form)
- Update Details card hidden if `!$product->isEditable()`
- Progress bar `progress-lg` (30px height) visible in form
- Submit Product and Delete buttons only appear when conditions are met

### SAP Errors (`/sap-errors/create`)
- Single card: 2-col row (Title | T-Code) + full-width Description textarea + full-width File input

### Profile
- Standard profile edit form (Breeze-generated, customized for avatar upload)

---

## 7. JavaScript Interaction Patterns

### 7.1 Form Auto-Save Draft (localStorage)

**Location:** `create.blade.php`  
**Storage key:** `doc_tracker_draft`  
**Trigger:** `input` and `change` events on the entire form  
**Data saved:** `{ names: [], batch_nos: [], stages: [], types: [] }`  
**Draft cleared:** on successful form submit (`localStorage.removeItem`)  
**Draft restored:** on `DOMContentLoaded` if key exists and no URL query params  
**Restoration feedback:** jQuery `$.notify()` toast notification (bottom-right, info type, 3s)

### 7.2 Double-Submit Prevention

**Location:** `create.blade.php`, `pending.blade.php`  
**Pattern:** On form submit event, disable the submit button and replace its text with a spinner:
```javascript
submitBtn.disabled = true;
submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';
```

### 7.3 Preference Save (XHR)

**Location:** `create.blade.php`  
**Trigger:** User hides or adds a suggestion in the autocomplete dropdown  
**Method:** `fetch()` POST to `{{ route('preferences.update') }}`  
**Headers:** `Content-Type: application/json`, `X-CSRF-TOKEN` from meta tag  
**Body:** `{ key: "hidden_suggestions", value: [...] }`  
**Response:** `{ success: true }` — no UI feedback on success (fire-and-forget)

### 7.4 Bulk Submit

**Location:** `pending.blade.php`  
**Checkboxes:** `.product-checkbox` inputs (one per row on desktop + one per card on mobile)  
**Select All:** `#selectAllDesktop` checkbox in table header  
**Count display:** `#bulkCount` span inside the Submit button  
**Submit button:** `#bulkSubmitBtn` — disabled until ≥ 1 checked  
**Form:** `#bulkSubmitForm` (hidden) — hidden inputs with `name="product_ids[]"` injected dynamically  
**Confirm:** `window.confirm()` native dialog before submission  
**Loading state:** Button text replaced with spinner, button disabled

### 7.5 Tooltip Initialization

```javascript
$('[data-bs-toggle="tooltip"]').tooltip();
```
Called on `$(document).ready()` on pages with action icon buttons.

### 7.6 Chart Initialization (Dashboard)

```javascript
const ctx = document.getElementById('submissionChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,    // ['Jun 14', 'Jun 15', ... 'Jun 20']
        datasets: [{ ... }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { /* axis tick/grid color config */ }
    }
});
```
Data is server-rendered PHP JSON, embedded directly in `<script>`:
```javascript
const chartData = @json(json_decode($chartDataJson));
```

---

## 8. Third-Party Service Integration Spec

---

### Integration 1: AWS S3 — File Storage

**What it does:** Stores user profile avatars and SAP error screenshots in a cloud bucket. Required because Heroku's filesystem is ephemeral (files disappear on server restart).

**Service:** Amazon Web Services, Simple Storage Service (S3)  
**PHP SDK:** `league/flysystem-aws-s3-v3:3.0` (installed via Composer)  
**Laravel Disk:** `s3` (configured in `config/filesystems.php`)

#### Upload — Avatar

| Field | Detail |
|-------|--------|
| **Trigger** | User submits avatar upload form on Profile page |
| **HTTP Method** | POST |
| **Laravel Route** | `POST /profile/avatar` → `ProfileController@updateAvatar` |
| **Form field name** | `avatar` |
| **Validation rules** | required, image, mimes: png/jpg/jpeg/webp, max: 2048 KB (2MB) |
| **S3 Call** | `$request->file('avatar')->store('avatars', 's3')` |
| **S3 Path** | `avatars/{random-uuid}.{ext}` |
| **Stored in DB** | `users.avatar` column (path only, not full URL) |
| **Old file cleanup** | If `$user->avatar` exists, `Storage::disk('s3')->delete($user->avatar)` before upload |
| **On failure** | `try/catch` → `back()->withErrors(['avatar' => 'Upload failed: {message}'])` |
| **On success** | `$user->update(['avatar' => $path])`, `back()->with('status', 'avatar-updated')` |

#### Upload — SAP Error Screenshot

| Field | Detail |
|-------|--------|
| **Trigger** | User submits SAP error form with image attached |
| **HTTP Method** | POST |
| **Laravel Route** | `POST /sap-errors` → `SapErrorController@store` |
| **Form field name** | `image` |
| **Validation rules** | nullable, image, mimes: png/jpg/jpeg/webp, max: 5120 KB (5MB) |
| **S3 Call** | `$request->file('image')->store('errors', 's3')` |
| **S3 Path** | `errors/{random-uuid}.{ext}` |
| **Stored in DB** | `sap_errors.image_path` column (path only) |
| **Old file cleanup** | On update: delete old S3 file before storing new one. On delete: delete S3 file before removing DB record |
| **On failure** | `try/catch` → `back()->withInput()->withErrors(['image' => 'Image upload failed: {message}'])` |

#### Retrieve — Generating Public URLs

| Context | Code | Output |
|---------|------|--------|
| Avatar in navbar | `Storage::disk('s3')->url(Auth::user()->avatar)` | `https://bucket.s3.region.amazonaws.com/avatars/uuid.jpg` |
| Avatar in dropdown | Same as above | Same format |
| SAP error screenshot | `Storage::disk('s3')->url($sapError->image_path)` | `https://bucket.s3.region.amazonaws.com/errors/uuid.jpg` |

#### Delete

| Context | Code |
|---------|------|
| Avatar on account delete | `Storage::disk('s3')->delete($user->avatar)` |
| Avatar on new upload | `Storage::disk('s3')->delete($user->avatar)` (before new upload) |
| SAP error image on record delete | `Storage::disk('s3')->delete($sapError->image_path)` |
| SAP error image on image replace | `Storage::disk('s3')->delete($sapError->image_path)` |
| Remove image manually (edit form) | `request->has('remove_image')` → delete and set `image_path = null` |

#### Required Environment Variables

```env
AWS_ACCESS_KEY_ID=your-iam-access-key
AWS_SECRET_ACCESS_KEY=your-iam-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=                          # Optional: CDN/CloudFront URL
AWS_ENDPOINT=                     # Optional: for S3-compatible services
AWS_USE_PATH_STYLE_ENDPOINT=false # Set true only for MinIO/Backblaze
```

**S3 disk throw config:** `'throw' => true` — S3 errors throw exceptions (caught by controllers)

---

### Integration 2: UI Avatars API — Auto-Generated Profile Pictures

**What it does:** Generates a colorful avatar image from the user's initials when no custom avatar is uploaded. Free, no authentication required.

**Service:** UI Avatars (https://ui-avatars.com)  
**Type:** External HTTP image URL (no authentication, no SDK)  
**When used:** When `$user->avatar` is NULL and the user email is not a hardcoded special case

#### Usage in Dashboard Layout

```html
<img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" 
     alt="Profile" 
     class="avatar-img rounded-circle" 
     width="40" height="40">
```

| Parameter | Value | Description |
|-----------|-------|-------------|
| `name` | `urlencode($user->name)` | User's full name, URL-encoded |
| `background` | `random` | Random background color per name |

**Fallback chain:**
```
1. $user->avatar → S3 URL (custom uploaded photo)
2. $user->email === 'admin@hamzaka.me' → local asset (admin.png)
3. All others → ui-avatars.com URL
```

**Used in:** Navbar dropdown (40×40, `rounded-circle`), dropdown menu body (50×50, `rounded`)

**Note:** This is a privacy consideration — usernames are sent to a third-party server. For pharmaceutical environments, consider replacing with a local letter-avatar generator if data residency is a concern.

---

### Integration 3: Chart.js — Analytics Chart

**What it does:** Renders the 7-day document submission trend as a line chart on the All Documents page.

**Service:** Chart.js  
**Source:** CDN — `https://cdn.jsdelivr.net/npm/chart.js`  
**Type:** Client-side JavaScript library (loaded from JSDelivr CDN)  
**No API key required**

#### Data Flow

```
1. Laravel query runs in ProductController@index
2. Groups submitted products by submission_date for last 7 days
3. Builds $labels = ['Jun 14', 'Jun 15', ...] and $data = [3, 0, 5, ...]
4. JSON-encodes as $chartDataJson and passes to view
5. Blade embeds: const chartData = @json(json_decode($chartDataJson));
6. Chart.js reads chartData.labels and chartData.data
7. Renders as smooth line chart on dark card canvas
```

#### Chart Configuration

| Property | Value |
|----------|-------|
| Type | `'line'` |
| Canvas ID | `submissionChart` |
| Canvas container height | `250px` (fixed, not responsive ratio) |
| Line color | `#00F2FE` |
| Fill color | `rgba(0, 242, 254, 0.1)` |
| Tension (curve) | `0.4` (smooth, not angular) |
| Legend | Hidden (`display: false`) |
| Y-axis min | 0, step size 1 (integer counts) |
| Y-axis tick color | `rgba(255, 255, 255, 0.7)` |
| Y-axis grid color | `rgba(255, 255, 255, 0.1)` |
| X-axis grid | Hidden (`display: false`) |
| Responsive | `true` |
| Aspect ratio | `false` (uses fixed container height) |

---

### Integration 4: WebFont Loader + Google Fonts — Typography

**What it does:** Loads the Public Sans font family from Google Fonts asynchronously.

**Service:** WebFont Loader (by Google/Typekit) + Google Fonts API  
**Source:** `public/Dashboard/assets/js/plugin/webfont/webfont.min.js` (local copy)  
**Font served from:** `fonts.googleapis.com` (Google's CDN)

#### Configuration

```javascript
WebFont.load({
    google: {
        families: ["Public Sans:300,400,500,600,700"]
    },
    custom: {
        families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons"
        ],
        urls: ["/Dashboard/assets/css/fonts.min.css"]
    },
    active: function() {
        sessionStorage.fonts = true;  // Cache flag to avoid FOUC
    }
});
```

**Fonts loaded:**
- `Public Sans` weights 300, 400, 500, 600, 700 (from Google)
- Font Awesome 5 (Solid, Regular, Brands) from local `fonts.min.css`
- Simple Line Icons from local `fonts.min.css`

**FOUC prevention:** `sessionStorage.fonts = true` is set in the `active` callback. Check this flag on page load to apply fonts immediately without FLASH.

---

### Integration 5: Bootstrap 5 + jQuery (CDN-equivalent, local copy)

**What it does:** Provides the UI component library (modals, forms, grid, alerts) and DOM manipulation.

**Source:** All served from local `public/Dashboard/assets/` — no CDN dependency for these core files.

| File | Path |
|------|------|
| Bootstrap CSS | `Dashboard/assets/css/bootstrap.min.css` |
| Bootstrap JS | `Dashboard/assets/js/core/bootstrap.min.js` |
| jQuery 3.7.1 | `Dashboard/assets/js/core/jquery-3.7.1.min.js` |
| Popper.js | `Dashboard/assets/js/core/popper.min.js` |

**Bootstrap features actively used:**
- Grid system (`.container`, `.row`, `.col-*`)
- Modals (`modal fade`, `data-bs-toggle="modal"`)
- Alerts (`alert alert-*`, `alert-dismissible`, `fade show`)
- Progress bars (`.progress`, `.progress-bar`)
- Badges (`.badge bg-*`)
- Dropdowns (`.dropdown-menu`, `.dropdown-item`)
- Tables (`.table`, `.table-striped`, `.table-hover`)
- Tooltips (`data-bs-toggle="tooltip"`)
- Buttons (`.btn`, `.btn-*`)
- Form controls (`.form-control`, `.form-select`, `.input-group`)
- Pagination (Bootstrap 5 style via Laravel's `pagination::bootstrap-5`)

---

### Integration 6: User Preferences API — Internal

**What it does:** Persists per-user autocomplete preferences (hidden suggestions, custom suggestions) to the database so they sync across devices.

**Type:** Internal Laravel JSON API endpoint — not a third-party service  
**Route:** `POST /preferences` → `UserPreferenceController@update`

#### Request Format

```javascript
fetch('/preferences', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        key: 'hidden_suggestions',    // or 'added_name', 'added_batch_no', 'added_stage'
        value: ['Product A', 'Product B']
    })
});
```

#### Request Schema

| Field | Type | Validation | Description |
|-------|------|-----------|-------------|
| `key` | string | required, string | Preference identifier |
| `value` | array or null | nullable, array | JSON array of values |

#### Response Schema

```json
{ "success": true }
```

#### Active Preference Keys

| Key | Stores | Value Type |
|-----|--------|-----------|
| `hidden_suggestions` | Suggestion items dismissed with × | `string[]` |
| `added_name` | Custom product names added by user | `string[]` |
| `added_batch_no` | Custom batch numbers added by user | `string[]` |
| `added_stage` | Custom stage names added by user | `string[]` |

**Error handling:** No error handling — fire and forget. If the request fails, the user's preference change is lost but UI remains functional.

---

## 9. Responsive Design Rules

### Breakpoints (Bootstrap 5)

| Name | Min-Width | Description |
|------|-----------|-------------|
| xs | 0 | Mobile portrait — base styles |
| sm | 576px | Mobile landscape |
| md | 768px | Tablet |
| lg | 992px | Desktop — sidebar visible |
| xl | 1200px | Wide desktop |

### Key Responsive Behaviors

| Feature | Mobile (< lg) | Desktop (≥ lg) |
|---------|--------------|----------------|
| Sidebar | Hidden by default, toggled via button | Always visible, collapsible to 75px |
| Topbar stat badges | Hidden (`d-none`) | Visible (`d-lg-flex`) |
| Document list | Card-based layout | Table layout |
| Document form | 2 columns per row | 4 columns per row |
| Auth layout | Stacked (form above, image below) | Side by side (50/50) |
| Navbar profile | Avatar + name visible | Avatar + name visible |

### Mobile-Specific CSS

```css
@media (max-width: 767.98px) {
    .page-inner { padding-bottom: 2rem; }
    .card-body { padding: 1rem 0.75rem; }
    .row.mb-4 { margin-bottom: 1rem !important; }
    .input-group { margin-bottom: 0.75rem; }
}
```

---

## 10. Known Tech Debt & Recommendations

| Issue | Location | Impact | Recommended Fix |
|-------|----------|--------|----------------|
| **Dual CSS frameworks loaded on dashboard** | `dashboard.blade.php` | ~30KB extra CSS, specificity conflicts | Remove Bootstrap CDN fallbacks; let Kaiadmin's bundled Bootstrap be the single source |
| **`style=""` inline styles** | Various views | Hard to maintain, can't be overridden in CSS | Extract all inline `style=""` to named CSS classes in `custom.css` |
| **External image (ui-avatars.com)** | `dashboard.blade.php` | Privacy: user names sent to third-party | Replace with a local PHP letter-avatar generator (`<?php /* SVG based on initials */ ?>`) |
| **Chart.js loaded from CDN** | `index.blade.php` `@push('scripts')` | Dependency on external CDN availability | Move to npm: `npm install chart.js` and import in `app.js` |
| **No custom `custom.css` documented** | `public/Dashboard/assets/css/custom.css` | Unknown overrides could conflict | Document every rule in `custom.css` with a comment explaining what it overrides and why |
| **Hardcoded `productBatchMap` in JS** | `create.blade.php` | Adding products requires code deploy | Move to a database table + API endpoint; return as JSON from controller |
| **Font Awesome loaded 3 times** | `dashboard.blade.php` fonts.min.css | Small page weight issue | Audit whether all 3 variants (Solid, Regular, Brands) are actually used; remove unused |
| **localStorage draft not cleared on navigation** | `create.blade.php` | User may see stale draft on next visit | Add `beforeunload` check or a "Clear Draft" button |

---

*Document prepared from full frontend audit of DocTracker v1 (Laravel 11 + Kaiadmin + Bootstrap 5 + TailwindCSS, June 2026 build). All component specs, color values, and integration details reflect the actual deployed state of the application.*
