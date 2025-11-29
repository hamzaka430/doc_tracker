<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - Doc Tracker</title>
    
    <link rel="icon" href="{{ asset('Dashboard/assets/img/favicon.ico') }}" type="image/x-icon" />
    
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

    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('Dashboard/assets/css/kaiadmin.min.css') }}" />
    
    <style>
        body {
            background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Public Sans', sans-serif;
        }
        
        .verify-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        
        .verify-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .verify-header h1 {
            color: #1a2332;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .verify-header p {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .btn-verify {
            background: #1572e8;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-verify:hover {
            background: #0d5cbf;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(21, 114, 232, 0.3);
        }
        
        .btn-logout {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-logout:hover {
            color: #1a2332;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem;
        }
        
        @media (max-width: 576px) {
            .verify-card {
                padding: 2rem 1.5rem;
            }
            
            .verify-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-header">
                <h1><i class="fas fa-envelope-open-text text-primary"></i> Verify Email</h1>
                <p>Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn't receive the email, we'll gladly send you another.</p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    A new verification link has been sent to your email address!
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-verify">
                        <i class="fas fa-paper-plane me-2"></i>Resend Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link btn-logout">
                        <i class="fas fa-sign-out-alt me-1"></i>Log Out
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-white-50 mb-0">
                &copy; {{ date('Y') }} Doc Tracker - Powered by <a href="https://hamzaka.me" target="_blank" class="text-white">Hamza Zaka</a>
            </p>
        </div>
    </div>

    <script src="{{ asset('Dashboard/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
