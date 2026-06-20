<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sign up for Doc Tracker, your secure Document Tracking System for project management.">
    <title>Sign up - Doc Tracker</title>
    <link rel="icon" href="{{ asset('Dashboard/assets/img/favicon.svg') }}" type="image/svg+xml" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <main class="min-h-screen flex flex-col items-center justify-center">
        <div class="py-2 px-4 md:px-8 w-full">
            <div class="grid items-center gap-4 max-w-5xl w-full mx-auto lg:grid-cols-2">
                
                <div class="border border-slate-300 bg-white rounded-lg p-5 max-w-sm mx-auto shadow-sm md:p-6 lg:mx-0 w-full">

                    <!-- Branding -->
                    <div class="flex items-center gap-2 mb-8">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xl font-bold text-slate-900 tracking-tight">Doc Tracker</span>
                    </div>

                    <div class="mb-5">
                        <h1 class="text-slate-900 text-2xl font-bold mb-2">Create an account</h1>
                        <p class="text-slate-600 text-sm leading-relaxed">Sign up to access your dashboard and start managing your projects.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-3">
                        @csrf
                        
                        <div>
                            <label for="name" class="mb-1 text-slate-900 font-medium text-xs inline-block">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Hamza" required autofocus autocomplete="name"
                                class="px-2.5 py-1.5 text-sm text-slate-900 rounded-md bg-white w-full border outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 @error('name') border-red-500 @enderror" />
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1 text-slate-900 font-medium text-xs inline-block">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="hamza@example.com" required autocomplete="username"
                                class="px-2.5 py-1.5 text-sm text-slate-900 rounded-md bg-white w-full border outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 @error('email') border-red-500 @enderror" />
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="mb-1 text-slate-900 font-medium text-xs inline-block">Password</label>
                            <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="new-password"
                                class="px-2.5 py-1.5 text-sm text-slate-900 rounded-md bg-white w-full border outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 @error('password') border-red-500 @enderror" />
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1 text-slate-900 font-medium text-xs inline-block">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password"
                                class="px-2.5 py-1.5 text-sm text-slate-900 rounded-md bg-white w-full border outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600 @error('password_confirmation') border-red-500 @enderror" />
                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full py-1.5 px-3 mt-3 text-sm rounded-md font-semibold cursor-pointer tracking-wide text-white border border-blue-600 bg-blue-600 hover:bg-blue-700 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                            Sign up</button>

                        <div class="text-slate-900 text-xs text-center pt-1">Already have an account? 
                            <a href="{{ route('login') }}" class="text-blue-700 hover:underline ml-1 font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">
                                Sign in
                            </a>
                        </div>
                    </form>
                </div>

                <div class="aspect-[71/50] w-11/12 lg:w-5/6 xl:w-[85%] mx-auto">
                    <img src="https://readymadeui.com/images/integration-illus.webp" class="w-full object-cover" alt="login img" width="710" height="500" />
                </div>
            </div>
        </div>
    </main>
</body>
</html>
