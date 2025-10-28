@extends('app')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-items-start gap-3">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle mb-0">
                Product ID: <strong class="text-luxury-gold">#{{ $product->id }}</strong>
                @if($product->isReadyForSubmission() && !$product->isSubmitted())
                    <span class="badge bg-success ms-2">Ready for Submit</span>
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Product Information -->
<div class="mobile-card mb-4">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-info-circle text-luxury-gold me-2"></i>
            Product Information
        </h3>
    </div>
    <div class="mobile-card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-luxury-gold">
                        <i class="fas fa-barcode fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Batch Number</small>
                        <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-luxury-gold">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Stage</small>
                        <span class="stage-badge">{{ $product->stage }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-luxury-gold">
                        <i class="fas fa-flag fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Status</small>
                        <span class="badge status-{{ strtolower($product->status) }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-luxury-gold">
                        <i class="fas fa-calendar fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Created At</small>
                        <div class="text-dark">{{ $product->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
            @if($product->isSubmitted())
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Submission Date</small>
                            <div class="text-success fw-semibold">
                                {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-success">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Submission Time</small>
                            <div class="text-success fw-semibold">
                                {{ $product->submission_time ? $product->submission_time->format('H:i:s') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Product Details -->
<div class="mobile-card">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-edit text-luxury-gold me-2"></i>
            Update Details
        </h3>
    </div>
    <div class="mobile-card-body">
                @if($product->isSubmitted())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        This product has been submitted and cannot be modified.
                    </div>
                @else
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hidden inputs for checkboxes (ensures false values are sent) -->
                        <input type="hidden" name="pre_line_clearance" value="0">
                        <input type="hidden" name="in_process" value="0">
                        <input type="hidden" name="post_line_clearance" value="0">

                        <!-- Checkboxes -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: {{ $product->pre_line_clearance ? 'rgba(40, 167, 69, 0.1)' : 'rgba(108, 117, 125, 0.1)' }};">
                                        <input class="form-check-input" type="checkbox" id="pre_line_clearance" 
                                               name="pre_line_clearance" value="1" {{ $product->pre_line_clearance ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="pre_line_clearance">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle me-3 text-success fa-lg"></i>
                                                <div>
                                                    <div class="fw-semibold">Pre-Line Clearance</div>
                                                    <small class="text-muted">Initial quality check completed</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: {{ $product->in_process ? 'rgba(40, 167, 69, 0.1)' : 'rgba(108, 117, 125, 0.1)' }};">
                                        <input class="form-check-input" type="checkbox" id="in_process" 
                                               name="in_process" value="1" {{ $product->in_process ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="in_process">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-cog me-3 text-warning fa-lg"></i>
                                                <div>
                                                    <div class="fw-semibold">Hourly In-Process</div>
                                                    <small class="text-muted">Production monitoring completed</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: {{ $product->post_line_clearance ? 'rgba(40, 167, 69, 0.1)' : 'rgba(108, 117, 125, 0.1)' }};">
                                        <input class="form-check-input" type="checkbox" id="post_line_clearance" 
                                               name="post_line_clearance" value="1" {{ $product->post_line_clearance ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="post_line_clearance">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clipboard-check me-3 text-info fa-lg"></i>
                                                <div>
                                                    <div class="fw-semibold">Post-Line Clearance</div>
                                                    <small class="text-muted">Final quality verification completed</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <label class="form-label text-muted small">Completion Progress</label>
                            @php
                                $completed = 0;
                                if($product->pre_line_clearance) $completed++;
                                if($product->in_process) $completed++;
                                if($product->post_line_clearance) $completed++;
                                $percentage = ($completed / 3) * 100;
                            @endphp
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                    {{ number_format($percentage, 0) }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $completed }}/3 tasks completed</small>
                        </div>

                        <!-- Remarks -->
                        <div class="form-floating mb-4">
                            <textarea class="form-control" id="remarks" name="remarks" 
                                      style="height: 100px" placeholder="Enter remarks...">{{ old('remarks', $product->remarks) }}</textarea>
                            <label for="remarks">Remarks</label>
                            @error('remarks')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-secondary flex-fill">
                                <i class="fas fa-save me-2"></i>Save Updates
                            </button>
                            
                            @if($product->isReadyForSubmission())
                                <button type="button" class="btn btn-success flex-fill" data-bs-toggle="modal" data-bs-target="#submitModal">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Product
                                </button>
                            @endif
                        </div>
                        
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary flex-fill">
                                <i class="fas fa-edit me-2"></i>Edit Product
                            </a>
                            @if(!$product->isSubmitted())
                                <button type="button" class="btn btn-outline-danger flex-fill" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            @endif
                        </div>
                    </form>
                @endif

                @if($product->remarks && trim($product->remarks) !== '')
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="text-muted">Current Remarks:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $product->remarks }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
@if($product->isReadyForSubmission() && !$product->isSubmitted())
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane text-luxury-gold me-2"></i>
                    Confirm Submission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to submit this product?</p>
                <div class="bg-light p-3 rounded">
                    <strong>{{ $product->name }}</strong><br>
                    <small class="text-muted">Batch: {{ $product->batch_no }} | Stage: {{ $product->stage }}</small>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Once submitted, this product cannot be modified.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.submit', $product) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Yes, Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
@if(!$product->isSubmitted())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to delete this product?</p>
                <div class="bg-light p-3 rounded">
                    <strong>{{ $product->name }}</strong><br>
                    <small class="text-muted">Batch: {{ $product->batch_no }} | Stage: {{ $product->stage }}</small>
                </div>
                <p class="text-danger mt-3 mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection