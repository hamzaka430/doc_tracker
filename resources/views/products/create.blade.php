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
                    
                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="name">Document Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Enter document name" value="{{ old('name') }}" required>
                                @error('name')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Batch Number -->
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="batch_no">Batch Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="batch_no" name="batch_no" 
                                       placeholder="Enter batch number" value="{{ old('batch_no') }}" required>
                                @error('batch_no')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Stage -->
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="stage">Stage <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="stage" 
                                       name="stage" 
                                       placeholder="Enter stage or select from list" 
                                       value="{{ old('stage') }}" 
                                       list="stage_options"
                                       required>
                                <datalist id="stage_options">
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage }}">{{ $stage }}</option>
                                    @endforeach
                                </datalist>
                                @error('stage')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                                <small class="form-text text-muted">Type to enter custom stage or select from list</small>
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select class="form-select form-control" id="type" name="type" required>
                                    <option value="" disabled selected>Select Type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-action">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check me-2"></i>Create Document
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
