<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Product Stage Tracker')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* Luxury Color Palette */
            --luxury-gold: #D4AF37;
            --luxury-gold-light: #E4BF47;
            --luxury-gold-dark: #B8951F;
            --deep-navy: #1A1A2E;
            --deep-navy-light: #2A2A3E;
            --deep-navy-dark: #0A0A1E;
            --pure-white: #FFFFFF;
            --light-grey: #F5F5F5;
            --medium-grey: #E0E0E0;
            --dark-grey: #6C757D;
            
            /* Gradients */
            --gold-gradient: linear-gradient(135deg, var(--luxury-gold) 0%, var(--luxury-gold-light) 100%);
            --navy-gradient: linear-gradient(135deg, var(--deep-navy) 0%, var(--deep-navy-light) 100%);
            --elegant-gradient: linear-gradient(135deg, var(--luxury-gold) 0%, var(--deep-navy) 100%);
            --subtle-gradient: linear-gradient(135deg, var(--pure-white) 0%, var(--light-grey) 100%);
            
            /* Shadows */
            --shadow-luxury: 0 8px 32px rgba(212, 175, 55, 0.15);
            --shadow-elegant: 0 12px 48px rgba(26, 26, 46, 0.1);
            --shadow-soft: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 16px 64px rgba(26, 26, 46, 0.15);
            
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
            background: linear-gradient(135deg, var(--light-grey) 0%, var(--pure-white) 50%, var(--light-grey) 100%);
            font-family: var(--font-sans);
            font-weight: 400;
            line-height: 1.7;
            color: var(--deep-navy);
            min-height: 100vh;
            letter-spacing: 0.3px;
        }

        /* Elegant Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-serif);
            font-weight: 600;
            color: var(--deep-navy);
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
            color: var(--dark-grey);
        }

        .navbar {
            background: var(--navy-gradient);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-elegant);
            border-bottom: 2px solid var(--luxury-gold);
            padding: 1.2rem 0;
            position: relative;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gold-gradient);
        }

        .navbar-brand {
            font-family: var(--font-serif);
            font-weight: 600;
            font-size: 1.75rem;
            letter-spacing: -0.02em;
            color: var(--pure-white) !important;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            position: relative;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
            color: var(--luxury-gold) !important;
        }

        .navbar-brand i {
            color: var(--luxury-gold);
            margin-right: 12px;
            font-size: 1.5rem;
        }

        .nav-link {
            font-family: var(--font-sans);
            font-weight: 500;
            border-radius: var(--border-radius);
            margin: 0 8px;
            padding: 12px 20px !important;
            position: relative;
            overflow: hidden;
            color: var(--pure-white) !important;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gold-gradient);
            transition: left 0.4s ease;
            opacity: 0.9;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            left: 0;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            color: var(--deep-navy) !important;
            box-shadow: var(--shadow-luxury);
        }

        .nav-link.active {
            color: var(--deep-navy) !important;
            font-weight: 600;
        }

        .nav-link i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        .card {
            border: 1px solid var(--medium-grey);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-elegant);
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
            background: var(--gold-gradient);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: var(--luxury-gold);
        }

        .card-header {
            background: var(--subtle-gradient);
            border-bottom: 2px solid var(--luxury-gold);
            padding: 2rem;
            font-family: var(--font-serif);
            font-weight: 600;
            font-size: 1.3rem;
            color: var(--deep-navy);
            letter-spacing: -0.01em;
        }

        .card-body {
            padding: 2.5rem;
        }

        .card-luxury {
            background: linear-gradient(135deg, var(--pure-white) 0%, var(--light-grey) 100%);
            border: 2px solid var(--luxury-gold);
            box-shadow: var(--shadow-luxury);
        }

        .btn {
            border-radius: var(--border-radius);
            font-family: var(--font-sans);
            font-weight: 500;
            padding: 10px 20px;
            border: none;
            position: relative;
            overflow: hidden;
            text-transform: none;
            letter-spacing: 0.3px;
            font-size: 0.85rem;
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
            box-shadow: var(--shadow-luxury);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--gold-gradient);
            color: var(--deep-navy);
            box-shadow: var(--shadow-luxury);
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            background: var(--luxury-gold-light);
            color: var(--deep-navy);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.4);
        }

        .btn-secondary {
            background: var(--navy-gradient);
            color: var(--pure-white);
            box-shadow: var(--shadow-elegant);
        }

        .btn-secondary:hover {
            background: var(--deep-navy-light);
            color: var(--luxury-gold);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: var(--pure-white);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-luxury {
            background: var(--elegant-gradient);
            color: var(--pure-white);
            box-shadow: var(--shadow-luxury);
            border: 2px solid var(--luxury-gold);
        }

        .btn-luxury:hover {
            background: var(--gold-gradient);
            color: var(--deep-navy);
            transform: translateY(-4px);
        }

        .btn-outline-primary {
            border: 2px solid var(--luxury-gold);
            color: var(--luxury-gold);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--gold-gradient);
            border-color: var(--luxury-gold);
            color: var(--deep-navy);
        }

        .btn-outline-secondary {
            border: 2px solid var(--deep-navy);
            color: var(--deep-navy);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--navy-gradient);
            border-color: var(--deep-navy);
            color: var(--pure-white);
        }

        .form-control, .form-select {
            border: 2px solid var(--medium-grey);
            border-radius: var(--border-radius);
            padding: 12px 16px;
            font-family: var(--font-sans);
            font-weight: 400;
            background: var(--pure-white);
            transition: all 0.3s ease;
            font-size: 0.9rem;
            color: var(--deep-navy);
            line-height: 1.5;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--luxury-gold);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15);
            background: var(--pure-white);
            outline: none;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--luxury-gold);
            font-weight: 600;
        }

        .form-floating > label {
            color: var(--dark-grey);
            font-family: var(--font-sans);
            font-weight: 500;
        }

        .input-group-text {
            background: var(--light-grey);
            border: 2px solid var(--medium-grey);
            border-radius: var(--border-radius);
            color: var(--luxury-gold);
            font-weight: 500;
        }

        .table {
            background: var(--pure-white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-elegant);
            border: 1px solid var(--medium-grey);
        }

        .table th {
            background: linear-gradient(135deg, var(--light-grey) 0%, var(--medium-grey) 100%);
            border: none;
            font-family: var(--font-serif);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            padding: 1.2rem;
            color: var(--deep-navy);
            position: relative;
        }

        .table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gold-gradient);
        }

        .table td {
            border: none;
            padding: 1.2rem;
            vertical-align: middle;
            font-weight: 400;
            color: var(--deep-navy);
        }

        .table tr:hover {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(255, 255, 255, 0.8) 100%);
            transform: translateX(4px);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background: rgba(245, 245, 245, 0.3);
        }

        .badge {
            padding: 10px 18px;
            border-radius: 25px;
            font-family: var(--font-sans);
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .stage-badge {
            background: var(--gold-gradient);
            color: var(--deep-navy);
            box-shadow: var(--shadow-luxury);
            border: 1px solid var(--luxury-gold-dark);
        }

        .status-pending {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
            color: var(--pure-white);
            box-shadow: 0 3px 12px rgba(108, 117, 125, 0.3);
        }

        .status-submitted {
            background: var(--gold-gradient);
            color: var(--deep-navy);
            box-shadow: var(--shadow-luxury);
            border: 1px solid var(--luxury-gold-dark);
        }

        .badge-luxury {
            background: var(--elegant-gradient);
            color: var(--pure-white);
            box-shadow: var(--shadow-luxury);
        }

        .alert {
            border: none;
            border-radius: var(--border-radius-lg);
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-elegant);
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
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(228, 191, 71, 0.05) 100%);
            color: var(--deep-navy);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .alert-success::before {
            background: var(--gold-gradient);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(233, 84, 92, 0.05) 100%);
            color: var(--deep-navy);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .alert-danger::before {
            background: linear-gradient(135deg, #dc3545 0%, #e9545c 100%);
        }

        .progress {
            height: 12px;
            border-radius: 25px;
            background: var(--light-grey);
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress-bar {
            background: var(--gold-gradient);
            border-radius: 25px;
            transition: width 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.4);
        }

        .form-check-input {
            border: 2px solid var(--medium-grey);
            border-radius: 6px;
            width: 1.2em;
            height: 1.2em;
        }

        .form-check-input:checked {
            background: var(--gold-gradient);
            border-color: var(--luxury-gold);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            font-family: var(--font-sans);
        }

        .breadcrumb-item {
            color: var(--dark-grey);
        }

        .breadcrumb-item.active {
            color: var(--luxury-gold);
            font-weight: 600;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "→";
            color: var(--luxury-gold);
            font-weight: bold;
        }

        .modal-content {
            border: 2px solid var(--luxury-gold);
            border-radius: var(--border-radius-xl);
            box-shadow: 0 25px 80px rgba(26, 26, 46, 0.3);
            backdrop-filter: blur(20px);
            background: var(--pure-white);
        }

        .modal-header {
            background: var(--subtle-gradient);
            border-bottom: 2px solid var(--luxury-gold);
            padding: 2rem;
            font-family: var(--font-serif);
        }

        .modal-body {
            padding: 2.5rem;
        }

        .modal-title {
            color: var(--deep-navy);
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
            background: var(--light-grey);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gold-gradient);
            border-radius: 6px;
            border: 2px solid var(--light-grey);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--luxury-gold-dark);
        }

        /* Responsive Luxury Design */
        @media (max-width: 1200px) {
            .container {
                max-width: 95%;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .card-header {
                padding: 1.5rem;
                font-size: 1.1rem;
            }
            
            .btn {
                padding: 12px 24px;
                font-size: 0.9rem;
            }
            
            .table th, .table td {
                padding: 0.8rem;
                font-size: 0.85rem;
            }
            
            .badge {
                padding: 6px 12px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 0.8rem 0;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .display-5 {
                font-size: 2rem;
            }
            
            .btn {
                padding: 10px 20px;
            }
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--navy-gradient);
            box-shadow: var(--shadow-elegant);
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 2px solid var(--luxury-gold);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-title {
            color: var(--pure-white);
            font-family: var(--font-serif);
            font-weight: 600;
            margin: 0;
            font-size: 1.4rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--luxury-gold);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(212, 175, 55, 0.1);
            transform: rotate(90deg);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--pure-white);
            text-decoration: none;
            font-family: var(--font-sans);
            font-weight: 500;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gold-gradient);
            transition: left 0.3s ease;
            opacity: 0.1;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            left: 0;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: var(--luxury-gold);
            border-left-color: var(--luxury-gold);
            background: rgba(212, 175, 55, 0.05);
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 20px;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(212, 175, 55, 0.2);
            margin: 1rem 1.5rem;
        }

        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 999;
            background: var(--navy-gradient);
            border: 2px solid var(--luxury-gold);
            color: var(--luxury-gold);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: var(--shadow-luxury);
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: var(--gold-gradient);
            color: var(--deep-navy);
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
            background: linear-gradient(135deg, var(--light-grey) 0%, var(--pure-white) 50%, var(--light-grey) 100%);
        }

        .content-wrapper {
            padding: 1.25rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            background: var(--pure-white);
            border-radius: var(--border-radius-lg);
            padding: 1.75rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-elegant);
            border-left: 4px solid var(--luxury-gold);
        }

        .page-title {
            font-family: var(--font-serif);
            font-weight: 600;
            color: var(--deep-navy);
            margin: 0 0 0.5rem 0;
            font-size: 1.75rem;
            line-height: 1.3;
        }

        .page-subtitle {
            color: var(--dark-grey);
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.4;
            margin: 0.5rem 0 0 0;
            font-weight: 400;
        }

        /* Mobile-First Card Design */
        .mobile-card {
            background: var(--pure-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-elegant);
            margin-bottom: 1.25rem;
            overflow: hidden;
            border: 1px solid var(--medium-grey);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .mobile-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .mobile-card-header {
            background: var(--subtle-gradient);
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid var(--luxury-gold);
        }

        .mobile-card-body {
            padding: 1.5rem;
        }

        .mobile-card-title {
            font-family: var(--font-serif);
            font-weight: 600;
            color: var(--deep-navy);
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.3;
        }

        /* Desktop Adjustments */
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 280px;
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
                padding: 1rem;
                padding-top: 5rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .mobile-card-body {
                padding: 1.5rem;
            }

            .table-responsive {
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-soft);
            }

            .btn {
                padding: 12px 20px;
                font-size: 0.9rem;
            }

            .form-control, .form-select {
                padding: 12px 16px;
            }
        }

        @media (max-width: 576px) {
            .content-wrapper {
                padding: 0.75rem;
                padding-top: 4rem;
            }

            .page-header {
                padding: 1.25rem;
                margin-bottom: 1rem;
            }

            .page-title {
                font-size: 1.4rem;
            }

            .mobile-card-body {
                padding: 1rem;
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
            color: var(--luxury-gold) !important;
        }

        .text-deep-navy {
            color: var(--deep-navy) !important;
        }

        .bg-luxury-gold {
            background: var(--gold-gradient) !important;
        }

        .bg-deep-navy {
            background: var(--navy-gradient) !important;
        }

        .border-luxury {
            border: 2px solid var(--luxury-gold) !important;
        }

        .shadow-luxury {
            box-shadow: var(--shadow-luxury) !important;
        }

        .shadow-elegant {
            box-shadow: var(--shadow-elegant) !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="sidebar-title">
                <i class="fas fa-crown me-2"></i>
                Product Tracker
            </h4>
            <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                <i class="fas fa-list me-3"></i>
                <span>Product List</span>
            </a>
            <a href="{{ route('products.create') }}" class="sidebar-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle me-3"></i>
                <span>Add Product</span>
            </a>
            <a href="{{ route('products.submitted') }}" class="sidebar-link {{ request()->routeIs('products.submitted') ? 'active' : '' }}">
                <i class="fas fa-check-double me-3"></i>
                <span>Submitted</span>
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
        // Sidebar functionality
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

        // Add fade-in animation to cards on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card, .mobile-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in-up');
                }, index * 100);
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
