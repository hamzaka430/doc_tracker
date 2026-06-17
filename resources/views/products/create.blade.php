@extends('layouts.dashboard')

@section('title', 'Add New Document - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Add New Document</h3>
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
            <a href="#">Add Document</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Document Information</div>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    
                    <div id="documents-container">
                        <!-- First Document Row -->
                        <div class="document-row border rounded p-3 mb-3 position-relative bg-light">
                            <div class="row">
                                <!-- Product Name -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Document Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name[]" 
                                               placeholder="Enter document name" required>
                                    </div>
                                </div>

                                <!-- Batch Number -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Batch Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="batch_no[]" 
                                               placeholder="Enter batch number" required>
                                    </div>
                                </div>

                                <!-- Stage -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Stage <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="stage[]" 
                                               placeholder="Enter stage or select" 
                                               list="stage_options"
                                               required>
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="form-group mb-0">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" name="type[]" required>
                                            <option value="" disabled selected>Select Type</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($errors->any())
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <datalist id="stage_options">
                        @foreach($stages as $stage)
                            <option value="{{ $stage }}">{{ $stage }}</option>
                        @endforeach
                    </datalist>

                    <div class="mb-4 mt-2">
                        <button type="button" id="add-document-btn" class="btn btn-primary btn-border btn-round btn-sm">
                            <i class="fa fa-plus me-2"></i>Add Another Document
                        </button>
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check me-2"></i>Save Documents
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-danger">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .card-action {
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('documents-container');
    const addBtn = document.getElementById('add-document-btn');
    
    addBtn.addEventListener('click', function() {
        const firstRow = container.querySelector('.document-row');
        const newRow = firstRow.cloneNode(true);
        
        // Clear inputs in cloned row
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        
        const selects = newRow.querySelectorAll('select');
        selects.forEach(select => select.selectedIndex = 0);
        
        // Add a delete button to the new row
        if (!newRow.querySelector('.remove-row-btn')) {
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-icon btn-danger btn-sm remove-row-btn position-absolute';
            removeBtn.style.top = '-15px';
            removeBtn.style.right = '-10px';
            removeBtn.style.zIndex = '10';
            removeBtn.innerHTML = '<i class="fa fa-times"></i>';
            removeBtn.onclick = function() {
                newRow.remove();
            };
            newRow.appendChild(removeBtn);
        }
        
        container.appendChild(newRow);
    });
});
</script>
@endpush
