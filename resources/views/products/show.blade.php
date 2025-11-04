@extends('app')

@section('title', 'Document Details - ' . $product->name)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-items-center justify-content-center gap-2 gap-md-3">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="min-width: 40px; height: 40px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-grow-1 text-center">
            <h1 class="page-title mb-1">{{ $product->name }}</h1>
            <div class="page-subtitle mb-0 d-flex flex-wrap align-items-center justify-content-center gap-2">
                <span class="text-nowrap">
                    Document ID: <strong class="text-luxury-gold">#{{ $product->id }}</strong>
                </span>
                @if($product->isReadyForSubmission() && !$product->isSubmitted())
                    <span class="badge bg-dark text-white">Ready for Submit</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Product Information -->
<div class="mobile-card mb-4">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-info-circle text-luxury-gold me-2"></i>
            Document Information
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
                        <i class="fas fa-diagram-project fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Stage</small>
                        <span class="stage-badge">{{ $product->stage }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-dark">
                        <i class="fas fa-pills fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Type</small>
                        <span class="badge bg-dark text-white">
                            {{ $product->type ?? 'N/A' }}
                        </span>
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
                        <i class="fas fa-calendar-alt fa-lg"></i>
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
                            <div class="text-dark">
                            <i class="fas fa-circle-check fa-lg"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Submission Date</small>
                            <div class="text-dark fw-semibold">
                                {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                            <div class="text-dark">
                            <i class="fas fa-stopwatch fa-lg"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Submission Time</small>
                            <div class="text-dark fw-semibold">
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
            <i class="fas fa-pen-to-square text-luxury-gold me-2"></i>
            Update Details
        </h3>
    </div>
    <div class="mobile-card-body">
                @if($product->isSubmitted())
                    <div class="alert alert-success">
                        <i class="fas fa-circle-check me-2"></i>
                        This product has been submitted and cannot be modified.
                    </div>
                @else
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hidden inputs for checkboxes (ensures false values are sent) -->
                        <input type="hidden" name="line_clearance" value="0">
                        <input type="hidden" name="review" value="0">
                        <input type="hidden" name="confirmation" value="0">

                        <!-- Checkboxes -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: {{ $product->line_clearance ? 'rgba(52, 58, 64, 0.1)' : 'rgba(248, 249, 250, 1)' }};">
                                        <input class="form-check-input" type="checkbox" id="line_clearance" 
                                               name="line_clearance" value="1" {{ $product->line_clearance ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="line_clearance">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clipboard-check me-3 text-dark fa-lg"></i>
                                                <div>
                                                    <div class="fw-semibold">Line Clearance</div>
                                                    <small class="text-muted">Pre, In-Process & Post line clearance completed</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: {{ $product->review ? 'rgba(52, 58, 64, 0.1)' : 'rgba(248, 249, 250, 1)' }};">
                                        <input class="form-check-input" type="checkbox" id="review" 
                                               name="review" value="1" {{ $product->review ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="review">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-eye me-3 text-dark fa-lg"></i>
                                                <div>
                                                    <div class="fw-semibold">Review</div>
                                                    <small class="text-muted">Document review completed</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: {{ $product->confirmation ? 'rgba(52, 58, 64, 0.1)' : 'rgba(248, 249, 250, 1)' }};">
                                        <input class="form-check-input" type="checkbox" id="confirmation" 
                                               name="confirmation" value="1" {{ $product->confirmation ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="confirmation">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-double me-3 text-dark fa-lg"></i>
                                                <div>
                                                    <div class="fw-semibold">Confirmation</div>
                                                    <small class="text-muted">Final confirmation completed</small>
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
                                if($product->line_clearance) $completed++;
                                if($product->review) $completed++;
                                if($product->confirmation) $completed++;
                                $percentage = ($completed / 3) * 100;
                            @endphp
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-dark text-white fw-semibold fs-6" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $completed }}/3 Tasks Completed ({{ number_format($percentage, 0) }}%)
                                </div>
                            </div>
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
                            <button type="submit" class="btn btn-dark flex-fill">
                                <i class="fas fa-floppy-disk me-2"></i>Save Updates
                            </button>
                            
                            @if($product->isReadyForSubmission())
                                <button type="button" class="btn btn-dark flex-fill" data-bs-toggle="modal" data-bs-target="#submitModal">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Product
                                </button>
                            @endif
                        </div>
                        
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-dark flex-fill border">
                                <i class="fas fa-pen-to-square me-2"></i>Edit Product
                            </a>
                            @if(!$product->isSubmitted())
                                <button type="button" class="btn btn-outline-danger flex-fill border" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash-can me-2"></i>Delete
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
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-paper-plane me-2"></i>Yes, Submit
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
                    <i class="fas fa-circle-exclamation me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-can me-2"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection