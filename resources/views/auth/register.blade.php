<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Doc Tracker</title>
    
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Public Sans', sans-serif;
            position: relative;
            overflow: hidden;
            padding: 20px 0;
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
        
        .register-container {
            width: 100%;
            max-width: 450px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08), 0 5px 15px rgba(0,0,0,0.03);
            padding: 3rem 2.5rem;
            transition: transform 0.3s ease;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-logo {
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

        .register-logo i {
            transform: rotate(5deg);
        }
        
        .register-title {
            color: #1a2035;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .register-subtitle {
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
        
        .btn-register {
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
        
        .btn-register:hover {
            background: #252d47;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(26, 32, 53, 0.3);
            color: #fff;
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
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>Doc Tracker</h1>
                <p>Create your account to get started</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Error!</strong> Please check the form below.
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-user me-1"></i>Full Name
                    </label>
                    <input id="name" type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           placeholder="Enter your full name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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
                           autocomplete="new-password"
                           placeholder="Create a password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock me-1"></i>Confirm Password
                    </label>
                    <input id="password_confirmation" type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Confirm your password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>Register
                </button>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="link-primary">Login here</a>
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
