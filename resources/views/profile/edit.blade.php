@extends('layouts.dashboard')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Profile</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Your Profile Settings</a>
            </li>
        </ul>
    </div>

    <div class="row">
            <!-- Left Sidebar: DP -->
            <div class="col-xl-4 col-lg-5 mb-4">
                @include('profile.partials.update-avatar-form')
            </div>

            <!-- Right Side: Forms -->
            <div class="col-xl-8 col-lg-7">
            
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0">Profile Information</h4>
                    <p class="text-muted mb-0 small">Update your account's profile information and email address.</p>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0">Update Password</h4>
                    <p class="text-muted mb-0 small">Ensure your account is using a long, random password to stay secure.</p>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header border-bottom text-white">
                    <h4 class="card-title mb-0 text-danger">Delete Account</h4>
                    <p class="text-muted mb-0 small">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Refined styling overrides to integrate Breeze forms into Bootstrap gracefully */
.dark\:bg-gray-800 { background: transparent !important; }
.dark\:text-gray-100, .dark\:text-gray-200 { color: inherit !important; }
.text-gray-900, .text-gray-800, .text-gray-600, .text-gray-400 { color: inherit !important; }
.dark\:focus\:ring-offset-gray-800 { outline: none; }
h2.text-lg { display: none; } /* Hide duplicate titles from native breeze components */
p.text-sm.text-gray-600 { display: none; } /* Hide duplicate subtitles */
.mt-1.block.w-full { 
    display: block; width: 100%; border-radius: .25rem; 
    border: 1px solid #ced4da; padding: .375rem .75rem; 
}
.mt-1.block.w-full:focus { 
    color: #495057; background-color: #fff; border-color: #80bdff; outline: 0; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
}
x-input-label { margin-bottom: 5px; font-weight: 500; font-size: 14px; }
.flex.items-center.gap-4 { display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; }
.space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 1.5rem; }

/* Action Buttons Customization */
button.inline-flex.items-center.px-4.py-2, a.inline-flex.items-center.px-4.py-2 {
    display: inline-block;
    font-weight: 500; text-align: center; white-space: nowrap; vertical-align: middle;
    user-select: none; border: 1px solid transparent; padding: .4rem 1rem;
    font-size: .875rem; line-height: 1.5; border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

/* Primary Button (Save) */
button.bg-gray-800 {
    color: #fff !important;
    background-color: #1a2035 !important;
    border-color: #1a2035 !important;
}
button.bg-gray-800:hover {
    background-color: #111524 !important;
}

/* Danger Button (Delete) */
button.bg-red-600 {
    color: #fff !important;
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}
button.bg-red-600:hover {
    background-color: #c82333 !important;
}

/* Secondary Button (Cancel) */
button.bg-white {
    color: #3f4254 !important;
    background-color: #fff !important;
    border-color: #ebedf2 !important;
}
button.bg-white:hover {
    background-color: #f8f9fa !important;
}
</style>
@endsection
