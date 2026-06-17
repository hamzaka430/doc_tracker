@extends('layouts.dashboard')

@section('title', 'SAP Error Details - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">SAP Error Details</h3>
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
            <a href="{{ route('sap-errors.index') }}">SAP Errors</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Error Details</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">{{ $sapError->title }}</h4>
                    <div class="ms-auto">
                        <a href="{{ route('sap-errors.edit', $sapError) }}" class="btn btn-info btn-round btn-sm me-2">
                            <i class="fa fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('sap-errors.destroy', $sapError) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-round btn-sm" onclick="return confirm('Delete this error?')">
                                <i class="fa fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-uppercase fw-bold text-muted mb-1">SAP T-Code</h6>
                    @if($sapError->sap_tcode)
                        <code class="fs-5 bg-light px-2 py-1 rounded">{{ $sapError->sap_tcode }}</code>
                    @else
                        <span class="text-muted">Not provided</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h6 class="text-uppercase fw-bold text-muted mb-2">Description</h6>
                    @if($sapError->description)
                        <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $sapError->description }}</div>
                    @else
                        <span class="text-muted">No description provided</span>
                    @endif
                </div>

                <div class="mb-2">
                    <h6 class="text-uppercase fw-bold text-muted mb-1">Date Added</h6>
                    <p>{{ $sapError->created_at->format('l, F j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Error Screenshot</h4>
            </div>
            <div class="card-body text-center">
                @if($sapError->image_path)
                    <a href="{{ Storage::disk('s3')->url($sapError->image_path) }}" target="_blank">
                        <img src="{{ Storage::disk('s3')->url($sapError->image_path) }}" 
                             alt="Error Screenshot" 
                             class="img-fluid rounded border shadow-sm"
                             style="max-height: 400px; width: auto;">
                    </a>
                    <div class="mt-2 text-muted small">
                        <i class="fas fa-search-plus me-1"></i>Click image to view full size
                    </div>
                @else
                    <div class="py-5 text-muted">
                        <i class="fas fa-image fa-3x mb-3 text-light"></i>
                        <p class="mb-0">No screenshot uploaded</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
