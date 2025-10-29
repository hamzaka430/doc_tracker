@extends('app')

@section('title', 'Submitted Documents')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-clipboard-check text-luxury-gold me-3"></i>
                Submitted Documents
            </h1>
            <p class="page-subtitle">
                Total submitted: <strong>{{ $submittedProducts->count() }}</strong> documents
            </p>
        </div>
        @if($submittedProducts->count() > 0)
            <a href="{{ route('products.export') }}" class="btn btn-luxury btn-export">
                <i class="fas fa-download me-2"></i>Export
            </a>
        @endif
    </div>
</div>

<!-- Search Bar -->
<div class="mobile-card mb-4">
    <div class="mobile-card-body">
        <form method="GET" action="{{ route('products.submitted') }}">
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control search-input" name="search" 
                                   placeholder="Search documents..." 
                                   value="{{ request('search') }}">
                        </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-fill" type="submit">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('products.submitted') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- Submitted Documents Table -->
    <div class="col-12">
        <div class="card fade-in-up">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-table text-luxury-gold me-2"></i>
                    Submitted Documents
                    @if(request('search'))
                        <small class="text-muted ms-2">
                            (filtered by "{{ request('search') }}")
                        </small>
                    @endif
                </h4>
            </div>
            <div class="card-body p-0">
                @if($submittedProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>Batch No</th>
                                    <th>Stage</th>
                                    <th>Submission Date</th>
                                    <th>Submission Time</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submittedProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
                                        </td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
                                        </td>
                                        <td>
                                            <span class="stage-badge">{{ $product->stage }}</span>
                                        </td>
                                        <td>
                                            <div class="text-success fw-semibold">
                                                {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-success">
                                                {{ $product->submission_time ? $product->submission_time->format('H:i:s') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($product->remarks)
                                                <span class="text-muted" 
                                                      data-bs-toggle="tooltip" 
                                                      data-bs-title="{{ $product->remarks }}">
                                                                            <i class="fas fa-comment-dots"></i>
                                                    {{ Str::limit($product->remarks, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        @if(request('search'))
                            <p class="text-muted lead">No documents found matching "{{ request('search') }}"</p>
                            <a href="{{ route('products.submitted') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Show All Documents
                            </a>
                        @else
                            <p class="text-muted lead">No submitted documents yet.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add First Document
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection