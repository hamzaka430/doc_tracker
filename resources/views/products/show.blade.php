@extends('layouts.dashboard')

@section('title', 'Document Details - ' . $product->name)

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">{{ $product->name }}</h3>
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
            <a href="#">Document Details</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Document Information Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Document Information</h4>
                    <div class="ms-auto">
                        @if($product->isReadyForSubmission() && !$product->isSubmitted())
                            <span class="badge badge-success">Ready for Submit</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <div>
                            <label class="form-label text-muted small mb-1">Document ID</label>
                            <div class="fw-bold">#{{ $product->id }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div>
                            <label class="form-label text-muted small mb-1">Batch Number</label>
                            <div><span class="badge badge-info">{{ $product->batch_no }}</span></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div>
                            <label class="form-label text-muted small mb-1">Stage</label>
                            <div><span class="badge badge-primary">{{ $product->stage }}</span></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div>
                            <label class="form-label text-muted small mb-1">Type</label>
                            <div><span class="badge badge-secondary">{{ $product->type ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div>
                            <label class="form-label text-muted small mb-1">Status</label>
                            <div>
                                @if($product->status === 'submitted')
                                    <span class="badge badge-success">Submitted</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div>
                            <label class="form-label text-muted small mb-1">Created At</label>
                            <div class="fw-bold small">{{ $product->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    @if($product->isSubmitted())
                        <div class="col-6 col-md-4">
                            <div>
                                <label class="form-label text-muted small mb-1">Submission Date</label>
                                <div class="fw-bold small">
                                    {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div>
                                <label class="form-label text-muted small mb-1">Submission Time</label>
                                <div class="text-muted small">
                                    {{ $product->submission_time ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Update Details Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Update Details</h4>
            </div>
            <div class="card-body">
                @if(!$product->isEditable())
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle me-2"></i>
                        This product's edit window has expired and it can no longer be modified.
                    </div>
                @else
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Hidden inputs for checkboxes -->
                        <input type="hidden" name="line_clearance" value="0">
                        <input type="hidden" name="review" value="0">
                        <input type="hidden" name="confirmation" value="0">

                        <!-- Checkboxes -->
                        <div class="row g-2 mb-4">
                            <div class="col-12">
                                <label class="form-check p-3 border rounded w-100 d-flex align-items-center mb-0 cursor-pointer {{ $product->line_clearance ? 'bg-light border-success' : '' }}" for="line_clearance" style="cursor: pointer; min-height: 52px;">
                                    <input class="form-check-input mt-0 me-3" type="checkbox" id="line_clearance" 
                                           name="line_clearance" value="1" {{ $product->line_clearance ? 'checked' : '' }} style="width: 20px; height: 20px;">
                                    <span class="form-check-label flex-grow-1">
                                        <i class="fas fa-clipboard-check text-primary me-2"></i>
                                        <strong class="d-inline-block">Line Clearance</strong>
                                        <small class="text-muted d-block mt-1">Pre, In-Process & Post line clearance completed</small>
                                    </span>
                                </label>
                            </div>

                            <div class="col-12">
                                <label class="form-check p-3 border rounded w-100 d-flex align-items-center mb-0 cursor-pointer {{ $product->review ? 'bg-light border-success' : '' }}" for="review" style="cursor: pointer; min-height: 52px;">
                                    <input class="form-check-input mt-0 me-3" type="checkbox" id="review" 
                                           name="review" value="1" {{ $product->review ? 'checked' : '' }} style="width: 20px; height: 20px;">
                                    <span class="form-check-label flex-grow-1">
                                        <i class="fas fa-eye text-primary me-2"></i>
                                        <strong class="d-inline-block">Review</strong>
                                        <small class="text-muted d-block mt-1">Document review completed</small>
                                    </span>
                                </label>
                            </div>

                            <div class="col-12">
                                <label class="form-check p-3 border rounded w-100 d-flex align-items-center mb-0 cursor-pointer {{ $product->confirmation ? 'bg-light border-success' : '' }}" for="confirmation" style="cursor: pointer; min-height: 52px;">
                                    <input class="form-check-input mt-0 me-3" type="checkbox" id="confirmation" 
                                           name="confirmation" value="1" {{ $product->confirmation ? 'checked' : '' }} style="width: 20px; height: 20px;">
                                    <span class="form-check-label flex-grow-1">
                                        <i class="fas fa-check-double text-primary me-2"></i>
                                        <strong class="d-inline-block">Confirmation</strong>
                                        <small class="text-muted d-block mt-1">Final confirmation completed</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <label class="form-label">Completion Progress</label>
                            @php
                                $completed = 0;
                                if($product->line_clearance) $completed++;
                                if($product->review) $completed++;
                                if($product->confirmation) $completed++;
                                $percentage = ($completed / 3) * 100;
                            @endphp
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $completed }}/3 Tasks ({{ number_format($percentage, 0) }}%)
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="form-group mb-4 px-0">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="4" placeholder="Enter remarks...">{{ old('remarks', $product->remarks) }}</textarea>
                            @error('remarks')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-action d-flex flex-wrap gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-success flex-grow-1 flex-sm-grow-0">
                                <i class="fa fa-check me-2"></i>Save Updates
                            </button>
                            
                            @if($product->isReadyForSubmission() && !$product->isSubmitted())
                                <button type="button" class="btn btn-primary flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal" data-bs-target="#submitModal">
                                    <i class="fa fa-paper-plane me-2"></i>Submit Product
                                </button>
                            @endif
                            
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning flex-grow-1 flex-sm-grow-0">
                                <i class="fa fa-edit me-2"></i>Edit Product
                            </a>
                            
                            @if($product->isEditable())
                                <button type="button" class="btn btn-danger flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fa fa-trash me-2"></i>Delete
                                </button>
                            @endif
                            
                            <a href="{{ route('products.index') }}" class="btn btn-secondary flex-grow-1 flex-sm-grow-0">
                                <i class="fa fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </form>
                @endif

                @if($product->remarks && trim($product->remarks) !== '')
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="text-muted">Current Remarks:</h6>
                        <div class="alert alert-light">
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
                <h5 class="modal-title">Confirm Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to submit this product?</p>
                <div class="alert alert-info">
                    <strong>{{ $product->name }}</strong><br>
                    <small>Batch: {{ $product->batch_no }} | Stage: {{ $product->stage }}</small>
                </div>
                <p class="text-muted mb-0">
                    <i class="fa fa-info-circle me-1"></i>
                    Once submitted, this product cannot be modified.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.submit', $product) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane me-2"></i>Yes, Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
@if($product->isEditable())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to delete this product?</p>
                <div class="alert alert-light">
                    <strong>{{ $product->name }}</strong><br>
                    <small>Batch: {{ $product->batch_no }} | Stage: {{ $product->stage }}</small>
                </div>
                <p class="text-danger mb-0">
                    <i class="fa fa-exclamation-triangle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash me-2"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .progress-lg {
        height: 30px;
    }
    .card-action {
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
</style>
@endpush
