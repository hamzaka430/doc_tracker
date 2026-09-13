<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="cfKEsMIfxwFRVAhQXaVEJ1udj4SKWM_vhURBP6Qeb_E" />
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="Doc Tracker is a comprehensive Document Tracking System designed for secure product management and stage tracking.">
    
    <title>@yield('title', 'Doc Tracker - Document Tracking System')</title>
    
    <link rel="icon" href="{{ asset('Dashboard/assets/img/favicon.svg') }}" type="image/svg+xml" />

    <!-- Fonts and icons -->
    <script src="{{ asset('Dashboard/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('Dashboard/assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/custom.css') }}" />
    
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <script>
            // Apply sidebar state immediately to prevent flicker
            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                document.querySelector('.wrapper').classList.add('sidebar_minimize');
            }
        </script>
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark">
                    <a href="{{ route('products.index') }}" class="logo">
                        <span class="navbar-brand text-white fw-bold" style="font-size: 1.2rem;">
                            Doc Tracker
                        </span>
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
                <!-- End Logo Header -->
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        
                        <!-- Document Management Section -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Document Management</h4>
                        </li>
                        <li class="nav-item {{ request()->routeIs('products.daily') ? 'active' : '' }}">
                            <a href="{{ route('products.daily') }}">
                                <i class="fas fa-inbox"></i>
                                <p>Daily Documents</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('products.pending') ? 'active' : '' }}">
                            <a href="{{ route('products.pending') }}">
                                <i class="fas fa-tasks"></i>
                                <p>Pending Documents</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('products.submitted') ? 'active' : '' }}">
                            <a href="{{ route('products.submitted') }}">
                                <i class="fas fa-check-circle"></i>
                                <p>Submitted Documents</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
                            <a href="{{ route('products.index') }}">
                                <i class="fas fa-folder-open"></i>
                                <p>All Documents</p>
                            </a>
                        </li>

                        <!-- Vials & Injections Section -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Vials Management</h4>
                        </li>
                        <li class="nav-item {{ request()->routeIs('injection_details.daily') ? 'active' : '' }}">
                            <a href="{{ route('injection_details.daily') }}">
                                <i class="fas fa-stream"></i>
                                <p>Daily Vials Data</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('injection_details.index') ? 'active' : '' }}">
                            <a href="{{ route('injection_details.index') }}">
                                <i class="fas fa-database"></i>
                                <p>All Vials Data</p>
                            </a>
                        </li>
                        
                        <!-- Operations & Actions Section -->
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Operations & Actions</h4>
                        </li>
                        @php
                            $isAddNewActive = request()->routeIs('products.create') || request()->routeIs('injection_details.create');
                        @endphp
                        <li class="nav-item {{ $isAddNewActive ? 'active' : '' }}">
                            <a data-bs-toggle="collapse" href="#addNewNav" class="{{ $isAddNewActive ? '' : 'collapsed' }}" aria-expanded="{{ $isAddNewActive ? 'true' : 'false' }}">
                                <i class="fas fa-plus-circle"></i>
                                <p>Add New</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ $isAddNewActive ? 'show' : '' }}" id="addNewNav">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->routeIs('products.create') ? 'active' : '' }}">
                                        <a href="{{ route('products.create') }}">
                                            <span class="sub-item">Add New Document</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('injection_details.create') ? 'active' : '' }}">
                                        <a href="{{ route('injection_details.create') }}">
                                            <span class="sub-item">Add Manual Vial</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item {{ request()->routeIs('sap-errors.*') ? 'active' : '' }}">
                            <a href="{{ route('sap-errors.index') }}">
                                <i class="fas fa-shield-alt"></i>
                                <p>SAP Errors</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                            <a href="{{ route('chat.index') }}">
                                <i class="fas fa-comments"></i>
                                <p>Chat Support</p>
                                <span class="badge" style="background-color: #ffffff !important; color: #1a2035 !important; font-size: 0.65rem; font-weight: 700; padding: 2px 7px; border-radius: 6px;">Live</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#exportNav" class="collapsed" aria-expanded="false">
                                <i class="fas fa-cloud-download-alt"></i>
                                <p>Export Data</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="exportNav">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('products.export') }}">
                                            <span class="sub-item">Document Data (Excel)</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('injection_details.export') }}">
                                            <span class="sub-item">Vials Data (Excel)</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li class="nav-item {{ request()->routeIs('products.trash') ? 'active' : '' }}">
                            <a href="{{ route('products.trash') }}">
                                <i class="fas fa-trash-alt"></i>
                                <p>Recycle Bin</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="{{ route('products.index') }}" class="logo">
                            <span class="navbar-brand text-white fw-bold">
                                Doc Tracker
                            </span>
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <!-- Useful Topbar Elements (Desktop) -->
                        <div class="d-none d-lg-flex align-items-center gap-3 ms-3">
                            @php
                                $pendingCount = \App\Models\Product::whereIn('user_id', auth()->user()->getVisibleUserIds())->where('status', 'pending')->count();
                                $submittedCount = \App\Models\Product::whereIn('user_id', auth()->user()->getVisibleUserIds())->where('status', 'submitted')->count();
                            @endphp
                            <div class="d-flex align-items-center px-3 py-1 rounded bg-warning text-white">
                                <i class="fas fa-hourglass-half me-2"></i> 
                                <span class="fw-bold" style="font-size: 0.85rem;">{{ $pendingCount }} Pending</span>
                            </div>
                            <div class="d-flex align-items-center px-3 py-1 rounded bg-success text-white">
                                <i class="fas fa-check-double me-2"></i> 
                                <span class="fw-bold" style="font-size: 0.85rem;">{{ $submittedCount }} Submitted</span>
                            </div>
                        </div>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <div class="avatar-sm">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Profile" class="avatar-img rounded-circle border border-2 border-white" width="40" height="40">
                                        @elseif(Auth::user()->email === 'admin@hamzaka.me')
                                            <img src="{{ asset('Dashboard/assets/img/profile_img/admin.png') }}" alt="Profile" class="avatar-img rounded-circle" width="40" height="40">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="Profile" class="avatar-img rounded-circle" width="40" height="40">
                                        @endif
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Hi,</span>
                                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    @if(Auth::user()->avatar)
                                                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Profile" class="avatar-img rounded" style="width: 50px; height: 50px; object-fit: cover;" width="50" height="50">
                                                    @elseif(Auth::user()->email === 'admin@hamzaka.me')
                                                        <img src="{{ asset('Dashboard/assets/img/profile_img/admin.png') }}" alt="Profile" class="avatar-img rounded" style="width: 50px; height: 50px; object-fit: cover;" width="50" height="50">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="Profile" class="avatar-img rounded" style="width: 50px; height: 50px; object-fit: cover;" width="50" height="50">
                                                    @endif
                                                </div>
                                                <div class="u-text">
                                                    <h4>{{ Auth::user()->name }}</h4>
                                                    <p class="text-muted">{{ Auth::user()->email }}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                                <i class="fas fa-cog me-2"></i>Profile Settings
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                                </button>
                                            </form>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validation Errors:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            @yield('modals')

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright">
                        {{ date('Y') }} © Doc Tracker - Document Tracking System
                    </div>
                    <div>
                        Powered by <a href="https://dezignwise.online" target="_blank" class="text-decoration-none">Dezignwise</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('Dashboard/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('Dashboard/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('Dashboard/assets/js/kaiadmin.min.js') }}"></script>

    <!-- jQuery Datatables -->
    <script src="{{ asset('Dashboard/assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Prevent sidebar from auto-expanding on hover when collapsed
            $('.sidebar').off('mouseenter mouseleave');

            // Force correct toggle icon on initial load if collapsed
            if ($('.wrapper').hasClass('sidebar_minimize')) {
                $('.toggle-sidebar').html('<i class="gg-menu-right"></i>');
                $('.toggle-sidebar').addClass('toggled');
            }

            // Add tooltips to sidebar items
            $('.sidebar .nav-item a').each(function() {
                var title = $(this).find('p').text().trim();
                if (title) {
                    $(this).attr('title', title);
                    new bootstrap.Tooltip(this, {
                        placement: 'right',
                        trigger: 'hover'
                    });
                }
            });

            // Only show tooltips if sidebar is collapsed
            $('.sidebar .nav-item a').on('show.bs.tooltip', function (e) {
                if (!$('.wrapper').hasClass('sidebar_minimize')) {
                    e.preventDefault();
                }
            });

            // Keep the same toggle icon for collapse/expand and save state
            $('.toggle-sidebar').on('click', function() {
                setTimeout(function() {
                    $('.toggle-sidebar').html('<i class="gg-menu-right"></i>');
                    
                    if ($('.wrapper').hasClass('sidebar_minimize')) {
                        localStorage.setItem('sidebar_collapsed', 'true');
                        $('.toggle-sidebar').addClass('toggled');
                    } else {
                        localStorage.setItem('sidebar_collapsed', 'false');
                        $('.toggle-sidebar').removeClass('toggled');
                    }
                }, 10);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
