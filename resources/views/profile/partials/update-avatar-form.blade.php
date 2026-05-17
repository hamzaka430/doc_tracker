<div class="card card-profile">
    <div class="card-header border-bottom">
        <h4 class="card-title mb-0">Profile Picture</h4>
        <p class="text-muted mb-0 small">Update your account's display picture. (Max: 20KB)</p>
    </div>
    <div class="card-body text-center p-4">
        <div class="avatar avatar-xxl position-relative mb-4">
            @if(Auth::user()->avatar)
                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" class="avatar-img rounded-circle border border-3 border-white shadow">
            @elseif(Auth::user()->email === 'admin@hamzaka.me')
                <img src="{{ asset('Dashboard/assets/img/profile_img/admin.png') }}" alt="Avatar" class="avatar-img rounded-circle border border-3 border-white shadow">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="Avatar" class="avatar-img rounded-circle border border-3 border-white shadow">
            @endif
        </div>

        <form method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="d-flex flex-column align-items-center mb-3">
            @csrf
            
            <div class="form-group p-0 w-100 mb-3">
                <input type="file" id="avatar" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/*" required>
                @error('avatar')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary btn-sm px-4 w-100"><i class="fas fa-upload me-2"></i> Upload New Picture</button>
        </form>

        @if(Auth::user()->avatar)
            <form method="post" action="{{ route('profile.avatar.destroy') }}">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-outline-danger btn-sm px-4 w-100" onclick="return confirm('Are you sure you want to remove your profile picture?')"><i class="fas fa-trash-alt me-2"></i> Remove Picture</button>
            </form>
        @endif

        @if (session('status') === 'avatar-updated')
            <div class="alert alert-success py-2 px-3 mb-0 mt-3 small"><i class="fas fa-check-circle me-1"></i> Avatar Uploaded!</div>
        @elseif (session('status') === 'avatar-deleted')
            <div class="alert alert-secondary py-2 px-3 mb-0 mt-3 small"><i class="fas fa-info-circle me-1"></i> Avatar Removed!</div>
        @endif
    </div>
</div>
