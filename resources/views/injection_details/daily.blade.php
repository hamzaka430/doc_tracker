@extends('layouts.dashboard')

@section('title', 'Daily Vials - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Daily Vials Data</h3>
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
            <a href="#">Daily Vials</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <h4 class="card-title mb-0">Pending Injection Data</h4>
                    <div class="ms-auto d-flex gap-2 flex-wrap">
                        <a href="{{ route('injection_details.export') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-file-excel me-1"></i> Export to Excel
                        </a>
                        <a href="{{ route('injection_details.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i> Add Custom Entry
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <form action="{{ route('injection_details.daily') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by product name or batch no..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                        @if(request('search'))
                            <a href="{{ route('injection_details.daily') }}" class="btn btn-secondary">Clear</a>
                        @endif
                    </div>
                </form>

                @if($vialsData->count() > 0)
                <div class="table-responsive">
                    <table class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Batch No</th>
                                <th>Batch Size</th>
                                <th>Optical Qty</th>
                                <th>Pkg Qty</th>
                                <th>Rejection</th>
                                <th>Error</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vialsData as $data)
                            @php
                                $isFilled = $data->batch_size || $data->optical_qty || $data->pkg_qty || $data->rejection;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $data->product_name }}</strong>
                                </td>
                                <td><span class="badge badge-info">{{ $data->batch_no }}</span></td>
                                <td>{{ $data->batch_size ?? '-' }}</td>
                                <td>{{ $data->optical_qty ?? '-' }}</td>
                                <td>{{ $data->pkg_qty ?? '-' }}</td>
                                <td>{{ $data->rejection ?? '-' }}</td>
                                <td>{{ $data->error ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('injection_details.edit', $data) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('injection_details.destroy', $data) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Showing {{ $vialsData->firstItem() }}–{{ $vialsData->lastItem() }} of {{ $vialsData->total() }} entries</small>
                    {{ $vialsData->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

                @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">No Pending Vials Data Found</h5>
                    <p class="text-muted">All daily entries are completed. Great job!</p>
                    <a href="{{ route('injection_details.create') }}" class="btn btn-primary mt-2">
                        <i class="fa fa-plus me-1"></i> Add Custom Entry
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    td { vertical-align: middle; }
    .badge { font-size: 0.8rem; }
</style>
@endpush
