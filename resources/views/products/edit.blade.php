@extends('app')

@section('title', 'Edit Product - ' . $product->name)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-edit text-luxury-gold me-3"></i>
                Edit Product
            </h1>
            <p class="page-subtitle">Update product information</p>
        </div>
        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Details
        </a>
    </div>
</div>

<!-- Edit Form -->
<div class="mobile-card">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-info-circle text-luxury-gold me-2"></i>
            Product Information
        </h3>
    </div>
    <div class="mobile-card-body">
        <form action="{{ route('products.updateBasic', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-12">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               placeholder="Product Name" 
                               value="{{ old('name', $product->name) }}" 
                               required>
                        <label for="name">
                            <i class="fas fa-box me-2"></i>Product Name
                        </label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control @error('batch_no') is-invalid @enderror" 
                               id="batch_no" 
                               name="batch_no" 
                               placeholder="Batch Number" 
                               value="{{ old('batch_no', $product->batch_no) }}" 
                               required>
                        <label for="batch_no">
                            <i class="fas fa-barcode me-2"></i>Batch Number
                        </label>
                        @error('batch_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control @error('stage') is-invalid @enderror" 
                               id="stage" 
                               name="stage" 
                               placeholder="Enter stage or select from list" 
                               value="{{ old('stage', $product->stage) }}" 
                               list="stage_options"
                               required>
                        <label for="stage">
                            <i class="fas fa-tasks me-2"></i>Stage
                        </label>
                        <datalist id="stage_options">
                            @foreach($stages as $stage)
                                <option value="{{ $stage }}">{{ $stage }}</option>
                            @endforeach
                        </datalist>
                        @error('stage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-muted mb-2">Current Status Information</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge status-{{ strtolower($product->status) }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Created</small>
                                <span class="text-dark">{{ $product->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($product->isSubmitted())
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Submitted</small>
                                    <span class="text-success">
                                        {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Danger Zone -->
@if(!$product->isSubmitted())
<div class="mobile-card mt-4 border-danger">
    <div class="mobile-card-header bg-danger text-white">
        <h3 class="mobile-card-title mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Danger Zone
        </h3>
    </div>
    <div class="mobile-card-body">
        <p class="text-muted mb-3">
            Once you delete this product, there is no going back. Please be certain.
        </p>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash me-2"></i>Delete Product
        </button>
    </div>
</div>

<!-- Delete Confirmation Modal -->
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Stage type toggle functionality
        const dropdownType = document.getElementById('dropdown_type');
        const customType = document.getElementById('custom_type');
        const stageDropdown = document.getElementById('stage_dropdown');
        const stageCustom = document.getElementById('stage_custom');
        const stageSelect = document.getElementById('stage_select');
        const stageInput = document.getElementById('stage_input');
        const stageHidden = document.getElementById('stage');

        // Check if current stage is in predefined list
        const currentStage = stageHidden.value;
        const predefinedStages = Array.from(stageSelect.options).map(option => option.value);
        
        if (currentStage && !predefinedStages.includes(currentStage)) {
            customType.checked = true;
            stageInput.value = currentStage;
        }

        // Toggle between dropdown and custom input
        function toggleStageInput() {
            if (dropdownType.checked) {
                stageDropdown.classList.remove('d-none');
                stageCustom.classList.add('d-none');
                stageSelect.required = true;
                stageInput.required = false;
                updateHiddenStage();
            } else {
                stageDropdown.classList.add('d-none');
                stageCustom.classList.remove('d-none');
                stageSelect.required = false;
                stageInput.required = true;
                stageInput.focus();
                updateHiddenStage();
            }
        }

        // Update hidden stage field
        function updateHiddenStage() {
            if (dropdownType.checked) {
                stageHidden.value = stageSelect.value;
            } else {
                stageHidden.value = stageInput.value;
            }
        }

        // Event listeners
        dropdownType.addEventListener('change', toggleStageInput);
        customType.addEventListener('change', toggleStageInput);
        stageSelect.addEventListener('change', updateHiddenStage);
        stageInput.addEventListener('input', updateHiddenStage);

        // Initialize
        toggleStageInput();
    });

    // Form validation feedback
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const stageValue = document.getElementById('stage').value;
        if (!stageValue || stageValue.trim() === '') {
            e.preventDefault();
            alert('Please select or enter a stage for the product.');
            return false;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Changes...';
        submitBtn.disabled = true;
    });
</script>
@endsection