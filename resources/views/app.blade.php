<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Doc Tracker')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/custom-select.css') }}" rel="stylesheet">
    <style>
        :root {
            /* Simplified Color Palette - Only 3 Colors */
            --pure-white: #FFFFFF;
            --dark-blue: #1A1A2E;
            --pure-black: #000000;
            
            /* Gradients */
            --blue-gradient: linear-gradient(135deg, var(--dark-blue) 0%, var(--pure-black) 100%);
            --white-gradient: linear-gradient(135deg, var(--pure-white) 0%, #F5F5F5 100%);
            
            /* Shadows */
            --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 12px rgba(0, 0, 0, 0.15);
            --shadow-dark: 0 8px 24px rgba(0, 0, 0, 0.2);
            
            /* Border Radius */
            --border-radius: 12px;
            --border-radius-lg: 20px;
            --border-radius-xl: 28px;
            
            /* Typography */
            --font-serif: 'Playfair Display', serif;
            --font-sans: 'Poppins', sans-serif;
        }

        * {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        body {
            background: var(--pure-white);
            font-family: var(--font-sans);
            font-weight: 400;
            line-height: 1.7;
            color: var(--pure-black);
            min-height: 100vh;
            letter-spacing: 0.3px;
        }

        /* Elegant Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-serif);
            font-weight: 600;
            color: var(--dark-blue);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .display-1, .display-2, .display-3, .display-4, .display-5 {
            font-family: var(--font-serif);
            font-weight: 300;
        }

        .lead {
            font-family: var(--font-sans);
            font-weight: 300;
            color: var(--pure-black);
        }

        .navbar {
            background: var(--dark-blue);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-medium);
            border-bottom: 2px solid var(--pure-black);
            padding: 0.6rem 0;
            position: relative;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--pure-black);
        }

        .navbar-brand {
            font-family: var(--font-serif);
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
            color: var(--pure-white) !important;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            position: relative;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
            color: var(--pure-white) !important;
        }

        .navbar-brand i {
            color: var(--pure-white);
            margin-right: 8px;
            font-size: 1rem;
        }

        .nav-link {
            font-family: var(--font-sans);
            font-weight: 500;
            border-radius: var(--border-radius);
            margin: 0 4px;
            padding: 8px 14px !important;
            position: relative;
            overflow: hidden;
            color: var(--pure-white) !important;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--pure-white);
            transition: left 0.4s ease;
            opacity: 0.9;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            left: 0;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            color: var(--dark-blue) !important;
            box-shadow: var(--shadow-light);
        }

        .nav-link.active {
            color: var(--dark-blue) !important;
            font-weight: 600;
        }

        .nav-link i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        .card {
            border: 1px solid var(--dark-blue);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            backdrop-filter: blur(20px);
            background: var(--pure-white);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--dark-blue);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-dark);
            border-color: var(--pure-black);
        }

        .card-header {
            background: var(--white-gradient);
            border-bottom: 2px solid var(--dark-blue);
            padding: 1.2rem;
            font-family: var(--font-serif);
            font-weight: 600;
            font-size: 1rem;
            color: var(--dark-blue);
            letter-spacing: -0.01em;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-luxury {
            background: var(--white-gradient);
            border: 2px solid var(--dark-blue);
            box-shadow: var(--shadow-medium);
        }

        .btn {
            border-radius: var(--border-radius);
            font-family: var(--font-sans);
            font-weight: 500;
            padding: 6px 14px;
            border: none;
            position: relative;
            overflow: hidden;
            text-transform: none;
            letter-spacing: 0.3px;
            font-size: 0.75rem;
            cursor: pointer;
            line-height: 1.5;
            transition: all 0.2s ease;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--dark-blue);
            color: var(--pure-white);
            box-shadow: var(--shadow-light);
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            background: var(--pure-black);
            color: var(--pure-white);
            box-shadow: var(--shadow-dark);
        }

        .btn-secondary {
            background: var(--pure-white);
            color: var(--dark-blue);
            box-shadow: var(--shadow-light);
            border: 2px solid var(--dark-blue);
        }

        .btn-secondary:hover {
            background: var(--dark-blue);
            color: var(--pure-white);
        }

        .btn-success {
            background: var(--dark-blue);
            color: var(--pure-white);
            box-shadow: var(--shadow-light);
        }

        .btn-luxury {
            background: var(--blue-gradient);
            color: var(--pure-white);
            box-shadow: var(--shadow-medium);
            border: 2px solid var(--dark-blue);
        }

        .btn-luxury:hover {
            background: var(--pure-black);
            color: var(--pure-white);
            transform: translateY(-4px);
        }

        .btn-outline-primary {
            border: 2px solid var(--dark-blue);
            color: var(--dark-blue);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--dark-blue);
            border-color: var(--dark-blue);
            color: var(--pure-white);
        }

        .btn-outline-secondary {
            border: 2px solid var(--pure-black);
            color: var(--pure-black);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--pure-black);
            border-color: var(--pure-black);
            color: var(--pure-white);
        }

        .form-control, .form-select {
            border: 2px solid var(--dark-blue);
            border-radius: var(--border-radius);
            padding: 8px 12px;
            font-family: var(--font-sans);
            font-weight: 400;
            background: var(--pure-white);
            transition: all 0.3s ease;
            font-size: 0.8rem;
            color: var(--pure-black);
            line-height: 1.5;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--pure-black);
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
            background: var(--pure-white);
            outline: none;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--dark-blue);
            font-weight: 600;
        }

        .form-floating > label {
            color: var(--pure-black);
            font-family: var(--font-sans);
            font-weight: 500;
        }

        .input-group-text {
            background: var(--white-gradient);
            border: 2px solid var(--dark-blue);
            border-radius: var(--border-radius);
            color: var(--dark-blue);
            font-weight: 500;
        }

        .table {
            background: var(--pure-white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: none;
            border: 1px solid var(--dark-blue);
        }

        .table th {
            background: var(--dark-blue);
            border: none;
            font-family: var(--font-serif);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 1px;
            padding: 0.8rem;
            color: var(--pure-white);
            position: relative;
        }

        .table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--pure-black);
        }

        .table td {
            border: none;
            padding: 0.8rem;
            vertical-align: middle;
            font-weight: 400;
            color: var(--pure-black);
        }

        .table tr:hover {
            background: transparent;
            transform: none;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background: rgba(0, 0, 0, 0.02);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-family: var(--font-sans);
            font-weight: 600;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .stage-badge {
            background: var(--dark-blue);
            color: var(--pure-white);
            box-shadow: var(--shadow-light);
            border: 1px solid var(--pure-black);
        }

        .status-pending {
            background: var(--pure-white);
            color: var(--pure-black);
            border: 2px solid var(--pure-black);
            box-shadow: var(--shadow-light);
        }

        .status-submitted {
            background: var(--dark-blue);
            color: var(--pure-white);
            box-shadow: var(--shadow-light);
            border: 1px solid var(--pure-black);
        }

        .badge-luxury {
            background: var(--blue-gradient);
            color: var(--pure-white);
            box-shadow: var(--shadow-medium);
        }

        .alert {
            border: none;
            border-radius: var(--border-radius-lg);
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-medium);
            font-family: var(--font-sans);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-success {
            background: var(--pure-white);
            color: var(--pure-black);
            border: 1px solid var(--dark-blue);
        }

        .alert-success::before {
            background: var(--dark-blue);
        }

        .alert-danger {
            background: var(--pure-white);
            color: var(--pure-black);
            border: 1px solid var(--pure-black);
        }

        .alert-danger::before {
            background: var(--pure-black);
        }

        .progress {
            height: 12px;
            border-radius: 25px;
            background: var(--pure-white);
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid var(--dark-blue);
        }

        .progress-bar {
            background: var(--dark-blue);
            border-radius: 25px;
            transition: width 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: var(--shadow-light);
        }

        .form-check-input {
            border: 2px solid var(--dark-blue);
            border-radius: 6px;
            width: 1.2em;
            height: 1.2em;
        }

        .form-check-input:checked {
            background: var(--dark-blue);
            border-color: var(--dark-blue);
            box-shadow: var(--shadow-light);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        /* Modern Toggle (replaces default checkbox appearance) */
        .toggle {
            --toggle-width: 56px;
            --toggle-height: 32px;
            --toggle-radius: 999px;
            position: relative;
            display: inline-block;
            width: var(--toggle-width);
            height: var(--toggle-height);
        }

        .toggle input { /* hide native checkbox */
            appearance: none;
            -webkit-appearance: none;
            width: 0;
            height: 0;
            opacity: 0;
            position: absolute;
        }

        .toggle .track {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.1);
            border-radius: var(--toggle-radius);
            border: 1px solid var(--dark-blue);
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
            transition: background 240ms ease, box-shadow 240ms ease, transform 240ms ease;
            padding: 4px;
        }

        .toggle .knob {
            width: calc(var(--toggle-height) - 8px);
            height: calc(var(--toggle-height) - 8px);
            background: var(--pure-white);
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            transform: translateX(0);
            transition: transform 280ms cubic-bezier(.2,.9,.3,1), background 240ms ease;
        }

        .toggle input:checked + .track {
            background: var(--dark-blue);
            box-shadow: var(--shadow-light);
        }

        .toggle input:checked + .track .knob {
            transform: translateX(calc(var(--toggle-width) - var(--toggle-height)));
            background: var(--pure-white);
        }

        /* Search bar luxury styling */
        .search-input {
            border-radius: 999px;
            padding: 12px 18px;
            border: 2px solid var(--dark-blue);
            box-shadow: var(--shadow-light);
            transition: box-shadow 240ms ease, border-color 240ms ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--pure-black);
            box-shadow: var(--shadow-medium);
        }

        /* Export button special outline */
        .btn-export {
            border-radius: 999px;
            padding: 10px 18px;
            border: 2px solid var(--dark-blue);
            background: transparent;
            color: var(--dark-blue);
            box-shadow: none;
            transition: background 220ms ease, color 220ms ease, box-shadow 220ms ease;
        }

        .btn-export:hover {
            background: var(--dark-blue);
            color: var(--pure-white);
            box-shadow: var(--shadow-medium);
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            font-family: var(--font-sans);
        }

        .breadcrumb-item {
            color: var(--pure-black);
        }

        .breadcrumb-item.active {
            color: var(--dark-blue);
            font-weight: 600;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "→";
            color: var(--dark-blue);
            font-weight: bold;
        }

        .modal-content {
            border: 2px solid var(--dark-blue);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-dark);
            backdrop-filter: blur(20px);
            background: var(--pure-white);
        }

        .modal-header {
            background: var(--white-gradient);
            border-bottom: 2px solid var(--dark-blue);
            padding: 2rem;
            font-family: var(--font-serif);
        }

        .modal-body {
            padding: 2.5rem;
        }

        .modal-title {
            color: var(--dark-blue);
            font-weight: 600;
        }

        /* Luxury Animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes goldShine {
            0% {
                background-position: -200% center;
            }
            100% {
                background-position: 200% center;
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }

        .shine-effect {
            background: linear-gradient(90deg, var(--luxury-gold) 0%, var(--luxury-gold-light) 50%, var(--luxury-gold) 100%);
            background-size: 200% 100%;
            animation: goldShine 2s infinite;
        }

        /* Custom Luxury Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--pure-white);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--dark-blue);
            border-radius: 6px;
            border: 2px solid var(--pure-white);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--pure-black);
        }

        /* Responsive Luxury Design */
        @media (max-width: 1200px) {
            .container {
                max-width: 95%;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .card-header {
                padding: 1rem;
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 0.75rem;
            }
            
            .table th, .table td {
                padding: 0.6rem;
                font-size: 0.75rem;
            }
            
            .badge {
                padding: 5px 10px;
                font-size: 0.65rem;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .card-body {
                padding: 0.8rem;
            }
            
            .display-5 {
                font-size: 1.5rem;
            }
            
            .btn {
                padding: 6px 12px;
            }
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 220px;
            background: var(--dark-blue);
            box-shadow: var(--shadow-medium);
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.2rem 1rem;
            border-bottom: 2px solid var(--pure-black);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-title {
            color: var(--pure-white);
            font-family: var(--font-serif);
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--pure-white);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(90deg);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.7rem 1rem;
            color: var(--pure-white);
            text-decoration: none;
            font-family: var(--font-sans);
            font-weight: 500;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
            font-size: 0.85rem;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--pure-white);
            transition: left 0.3s ease;
            opacity: 0.1;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            left: 0;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: var(--pure-white);
            border-left-color: var(--pure-white);
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link i {
            font-size: 0.9rem;
            width: 18px;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 1rem 1.5rem;
        }

        .mobile-menu-btn {
            position: fixed;
            top: 0.7rem;
            left: 0.7rem;
            z-index: 999;
            background: var(--dark-blue);
            border: 2px solid var(--pure-white);
            color: var(--pure-white);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: var(--pure-black);
            color: var(--pure-white);
            transform: scale(1.1);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .main-content {
            margin-left: 0;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            background: var(--pure-white);
        }

        .content-wrapper {
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            background: var(--pure-white);
            border-radius: var(--border-radius-lg);
            padding: 1.2rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-medium);
            border-left: 4px solid var(--dark-blue);
        }

        .page-title {
            font-family: var(--font-serif);
            font-weight: 600;
            color: var(--dark-blue);
            margin: 0 0 0.3rem 0;
            font-size: 1.3rem;
            line-height: 1.3;
        }

        .page-subtitle {
            color: var(--pure-black);
            font-size: 0.8rem;
            margin: 0;
            line-height: 1.4;
            margin: 0.3rem 0 0 0;
            font-weight: 400;
        }

        /* Mobile-First Card Design */
        .mobile-card {
            background: var(--pure-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            margin-bottom: 1rem;
            overflow: hidden;
            border: 1px solid var(--dark-blue);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .mobile-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-dark);
        }

        .mobile-card-header {
            background: var(--white-gradient);
            padding: 1rem 1.2rem;
            border-bottom: 2px solid var(--dark-blue);
        }

        .mobile-card-body {
            padding: 1.2rem;
        }

        .mobile-card-title {
            font-family: var(--font-serif);
            font-weight: 600;
            color: var(--dark-blue);
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        /* Desktop Adjustments */
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 220px;
            }

            .mobile-menu-btn {
                display: none;
            }

            .sidebar-overlay {
                display: none;
            }
        }

        /* Mobile Optimizations */
        @media (max-width: 991px) {
            .content-wrapper {
                padding: 0.8rem;
                padding-top: 3.5rem;
            }

            .page-header {
                padding: 1rem 1.2rem;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .mobile-card-body {
                padding: 1rem;
            }

            .table-responsive {
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-soft);
            }

            .btn {
                padding: 8px 14px;
                font-size: 0.75rem;
            }

            .form-control, .form-select {
                padding: 8px 12px;
            }
        }

        @media (max-width: 576px) {
            .content-wrapper {
                padding: 0.6rem;
                padding-top: 3rem;
            }

            .page-header {
                padding: 0.8rem 1rem;
                margin-bottom: 0.8rem;
            }

            .page-title {
                font-size: 1.1rem;
            }

            .mobile-card-body {
                padding: 0.8rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-flex.gap-2 .btn {
                width: auto;
                flex: 1;
            }
        }

        /* Utility Classes */
        .text-luxury-gold {
            color: var(--dark-blue) !important;
        }

        .text-deep-navy {
            color: var(--dark-blue) !important;
        }

        .bg-luxury-gold {
            background: var(--dark-blue) !important;
        }

        .bg-deep-navy {
            background: var(--dark-blue) !important;
        }

        .border-luxury {
            border: 2px solid var(--dark-blue) !important;
        }

        .shadow-luxury {
            box-shadow: var(--shadow-medium) !important;
        }

        .shadow-elegant {
            box-shadow: var(--shadow-medium) !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="sidebar-title">
                <i class="fas fa-file-lines me-2"></i>
                Doc Tracker
            </h4>
            <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                <i class="fas fa-file-lines me-3"></i>
                <span>All Documents</span>
            </a>
            <a href="{{ route('products.create') }}" class="sidebar-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                <i class="fas fa-plus me-3"></i>
                <span>Add Document</span>
            </a>
            <a href="{{ route('products.submitted') }}" class="sidebar-link {{ request()->routeIs('products.submitted') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check me-3"></i>
                <span>Submitted</span>
            </a>
            <a href="{{ route('products.daily') }}" class="sidebar-link {{ request()->routeIs('products.daily') ? 'active' : '' }}">
                <i class="fas fa-calendar-day me-3"></i>
                <span>Today's Docs</span>
            </a>
            <div class="sidebar-divider"></div>
            <a href="{{ route('products.export') }}" class="sidebar-link">
                <i class="fas fa-download me-3"></i>
                <span>Export CSV</span>
            </a>
        </nav>
    </div>

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn d-lg-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay d-lg-none" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-x-circle me-1"></i>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar functionality (keeps original behavior)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileBtn = document.querySelector('.mobile-menu-btn');

            if (window.innerWidth < 992) {
                if (!sidebar.contains(event.target) && !mobileBtn.contains(event.target)) {
                    sidebar.classList.remove('active');
                    document.querySelector('.sidebar-overlay').classList.remove('active');
                }
            }
        });

        // Smooth page scroll for internal links
        document.addEventListener('click', function (e) {
            const anchor = e.target.closest('a[href^="#"]');
            if (!anchor) return;
            const href = anchor.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // Reveal on scroll using IntersectionObserver
        (function setupReveal() {
            const revealItems = document.querySelectorAll('.reveal, .card, .mobile-card, .page-header');
            if (!('IntersectionObserver' in window)) {
                // Fallback: reveal all
                revealItems.forEach(el => el.classList.add('fade-in-up'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.12
            });

            revealItems.forEach(el => observer.observe(el));
        })();

        // Checkbox -> animated toggle helpers
        document.addEventListener('DOMContentLoaded', function() {
            // Keep original card fade-in but stagger more softly for luxury feel
            const cards = document.querySelectorAll('.card, .mobile-card');
            cards.forEach((card, index) => {
                setTimeout(() => card.classList.add('fade-in-up'), index * 80);
            });

            // Enhance checkboxes: when toggle-style present, animate label backgrounds
            const checkInputs = document.querySelectorAll('.form-check-input');
            checkInputs.forEach(input => {
                input.addEventListener('change', (e) => {
                    const parent = input.closest('.form-check');
                    if (!parent) return;
                    if (input.checked) {
                        parent.style.background = 'rgba(212,175,55,0.06)';
                        parent.classList.add('shadow-luxury');
                    } else {
                        parent.style.background = 'rgba(108,117,125,0.06)';
                        parent.classList.remove('shadow-luxury');
                    }
                });
            });

            // Fancy input focus glow for form controls
            const inputs = document.querySelectorAll('.form-control, .form-select, .search-input');
            inputs.forEach(i => {
                i.addEventListener('focus', () => i.classList.add('focused'));
                i.addEventListener('blur', () => i.classList.remove('focused'));
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                document.getElementById('sidebar').classList.remove('active');
                document.querySelector('.sidebar-overlay').classList.remove('active');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
