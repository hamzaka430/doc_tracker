@extends('layouts.dashboard')

@section('title', 'User Management - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">User Management</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('products.index') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Users</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Users</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-success">Admin</span>
                                        @elseif($user->linked_to)
                                            <span class="badge bg-primary">Linked User</span>
                                        @else
                                            <span class="badge bg-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="text-success"><i class="fa fa-check-circle"></i> Full Access</span>
                                        @else
                                            @php $perms = $user->permissions ?? []; @endphp
                                            @if(empty($perms))
                                                <span class="text-muted">Default (Own Data Only)</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($perms as $perm)
                                                        <span class="badge bg-info">{{ str_replace('_', ' ', str_replace('can_', '', $perm)) }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $user->id }}">
                                            <i class="fa fa-key"></i> Permissions
                                        </button>
                                    </td>
                                </tr>

                                <!-- Permissions Modal -->
                                <div class="modal fade" id="permissionsModal{{ $user->id }}" tabindex="-1" aria-labelledby="permissionsModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('users.updatePermissions', $user->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="permissionsModalLabel{{ $user->id }}">Manage Access for {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="isAdmin{{ $user->id }}" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold text-danger" for="isAdmin{{ $user->id }}">Make Admin (Grants access to everything)</label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="isLinked{{ $user->id }}" name="linked_to" value="1" {{ $user->linked_to ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold text-primary" for="isLinked{{ $user->id }}">Link to Organization (User will see organization data)</label>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success">Save Permissions</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
