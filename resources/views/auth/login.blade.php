<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Doc Tracker</title>
    
    <link rel="icon" href="{{ asset('Dashboard/assets/img/favicon.ico') }}" type="image/x-icon" />
    
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
    
    <style>
        body {
            background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Public Sans', sans-serif;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: #1a2332;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 0.95rem;
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
        
        .btn-login {
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
        
        .btn-login:hover {
            background: #0d5cbf;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(21, 114, 232, 0.3);
        }
        
        .link-primary {
            color: #1572e8;
            text-decoration: none;
            font-weight: 500;
        }
        
        .link-primary:hover {
            color: #0d5cbf;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem;
        }
        
        .form-check-input {
            cursor: pointer;
        }
        
        .form-check-label {
            cursor: pointer;
            color: #6c757d;
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-file-alt text-primary"></i> Doc Tracker</h1>
                <p>Welcome back! Please login to your account</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Error!</strong> Please check your credentials.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>Email Address
                    </label>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Enter your email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
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

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label" for="remember_me">
                            Remember me
                        </label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-primary">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>

                <!-- Register Link -->
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="link-primary">Register here</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="text-white-50 mb-0">
                &copy; {{ date('Y') }} Doc Tracker - Powered by <a href="https://hamzaka.me" target="_blank" class="text-white">Hamza Zaka</a>
            </p>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('Dashboard/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
