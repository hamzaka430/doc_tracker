@extends('layouts.dashboard')

@section('title', 'Add SAP Error - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Add SAP Error</h3>
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
            <a href="#">Add Error</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Error Information</div>
            </div>
            <div class="card-body">
                <form action="{{ route('sap-errors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="title">Error Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="Enter short description/title" value="{{ old('title') }}" required>
                                @error('title')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="sap_tcode">SAP T-Code</label>
                                <input type="text" class="form-control" id="sap_tcode" name="sap_tcode" 
                                       placeholder="e.g. MIGO, ME21N" value="{{ old('sap_tcode') }}">
                                @error('sap_tcode')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Detailed Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" 
                                          placeholder="Describe the error in detail...">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="image">Error Screenshot (Max: 5MB)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                @error('image')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-action mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check me-2"></i>Save Error
                        </button>
                        <a href="{{ route('sap-errors.index') }}" class="btn btn-danger">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
