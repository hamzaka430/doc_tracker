<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Confirm Password - Doc Tracker</title>
    
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
        
        .confirm-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .confirm-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
        }
        
        .confirm-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .confirm-header h1 {
            color: #1a2332;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .confirm-header p {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .form-label {
            font-weight: 600;
            color: #1a2332;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #1572e8;
            box-shadow: 0 0 0 0.2rem rgba(21, 114, 232, 0.15);
        }
        
        .btn-confirm {
            background: #1572e8;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-confirm:hover {
            background: #0d5cbf;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(21, 114, 232, 0.3);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem;
        }
        
        @media (max-width: 576px) {
            .confirm-card {
                padding: 2rem 1.5rem;
            }
            
            .confirm-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="confirm-container">
        <div class="confirm-card">
            <div class="confirm-header">
                <h1><i class="fas fa-shield-alt text-primary"></i> Confirm Password</h1>
                <p>This is a secure area. Please confirm your password before continuing.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Error!</strong> Please check your password.
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i>Password
                    </label>
                    <input id="password" type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Enter your password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-confirm">
                    <i class="fas fa-check me-2"></i>Confirm
                </button>
            </form>
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
