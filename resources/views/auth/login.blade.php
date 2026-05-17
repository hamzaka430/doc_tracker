<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Doc Tracker</title>
    
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
    
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Public Sans', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Abstract shapes for a sleek look */
        body::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(26,32,53,0.05), rgba(76,104,183,0.1));
            z-index: 0;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -200px;
            left: -100px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(76,104,183,0.05), rgba(30,144,255,0.08));
            z-index: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08), 0 5px 15px rgba(0,0,0,0.03);
            padding: 3rem 2.5rem;
            transition: transform 0.3s ease;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-logo {
            width: 60px;
            height: 60px;
            background: #1a2035;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: #fff;
            font-size: 28px;
            box-shadow: 0 8px 15px rgba(26, 32, 53, 0.2);
            transform: rotate(-5deg);
        }

        .login-logo i {
            transform: rotate(5deg);
        }
        
        .login-title {
            color: #1a2035;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .login-subtitle {
            color: #686f7a;
            font-size: 0.9rem;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            border: 2px solid #ebedf2;
            background-color: #f8f9fa;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(26, 32, 53, 0.1);
            border-color: #1a2035;
            background-color: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #3f4254;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        
        .btn-login {
            background: #1a2035;
            color: #fff;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
            width: 100%;
            border: none;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 8px 15px rgba(26, 32, 53, 0.2);
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            background: #252d47;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(26, 32, 53, 0.3);
            color: #fff;
        }
        
        .form-check-input:checked {
            background-color: #1a2035;
            border-color: #1a2035;
        }

        .link-primary {
            color: #1a2035 !important;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .link-primary:hover {
            color: #48abf7 !important;
        }
        
        .footer-text {
            color: #686f7a;
            font-size: 0.85rem;
        }

        .footer-text a {
            color: #1a2035;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Doc Tracker</h1>
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
                &copy; {{ date('Y') }} Doc Tracker - Powered by <a href="https://dezignwise.online" target="_blank" class="text-white">Dezignwise</a>
            </p>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('Dashboard/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('Dashboard/assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
