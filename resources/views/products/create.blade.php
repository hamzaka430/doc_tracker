@extends('app')

@section('title', 'Add New Document')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus text-luxury-gold me-3"></i>
        Add New Document
    </h1>
    <p class="page-subtitle">Create a new document entry in the tracking system</p>
</div>

<!-- Add Product Form -->
<div class="mobile-card">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-file-lines text-luxury-gold me-2"></i>
            Document Information
        </h3>
    </div>
    <div class="mobile-card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <!-- Product Name -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Document Name" value="{{ old('name') }}" required>
                        <label for="name">
                            <i class="fas fa-file-lines me-2"></i>Document Name
                        </label>
                        @error('name')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Batch Number -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="batch_no" name="batch_no" 
                               placeholder="Batch Number" value="{{ old('batch_no') }}" required>
                        <label for="batch_no">
                            <i class="fas fa-barcode me-2"></i>Batch Number
                        </label>
                        @error('batch_no')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Stage -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               id="stage" 
                               name="stage" 
                               placeholder="Enter stage or select from list" 
                               value="{{ old('stage') }}" 
                               list="stage_options"
                               required>
                        <label for="stage">
                            <i class="fas fa-diagram-project me-2"></i>Stage
                        </label>
                        <datalist id="stage_options">
                            @foreach($stages as $stage)
                                <option value="{{ $stage }}">{{ $stage }}</option>
                            @endforeach
                        </datalist>
                        @error('stage')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <style>
                    .custom-select-wrapper select {
                        padding: 15.5px 45px
                    }
                </style>
                <!-- Type -->
                <div class="col-md-6">
                    <div class="custom-select-wrapper">
                        <i class="fas fa-pills icon-left"></i>
                        <select class="form-select" id="type" name="type" required>
                            <option value="" disabled selected>Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down icon-right"></i>
                    </div>
                    @error('type')
                        <div class="text-danger small mt-2">
                            <i class="fas fa-circle-exclamation me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-dark flex-fill">
                    <i class="fas fa-plus me-2"></i>
                    Add Document
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mt-3">
    <div class="col-md-4">
        <div class="mobile-card text-center">
            <div class="mobile-card-body">
                <i class="fas fa-stopwatch fa-2x text-luxury-gold mb-2"></i>
                <h5 class="mb-1">Quick Entry</h5>
                <p class="text-muted small mb-0">Add documents efficiently</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mobile-card text-center">
            <div class="mobile-card-body">
                <i class="fas fa-circle-check fa-2x text-dark mb-2"></i>
                <h5 class="mb-1">Auto Status</h5>
                <p class="text-muted small mb-0">Pending status by default</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mobile-card text-center">
            <div class="mobile-card-body">
                <i class="fas fa-diagram-project fa-2x text-dark mb-2"></i>
                <h5 class="mb-1">Stage Tracking</h5>
                <p class="text-muted small mb-0">Monitor progress easily</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus on document name field
        document.getElementById('name').focus();
    });

    // Form validation feedback
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const stageValue = document.getElementById('stage').value;
        if (!stageValue || stageValue.trim() === '') {
            e.preventDefault();
            alert('Please enter a stage for the document.');
            return false;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding Document...';
        submitBtn.disabled = true;
    });
</script>
@endsection