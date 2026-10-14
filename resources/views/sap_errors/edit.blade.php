@extends('layouts.dashboard')

@section('title', 'Edit SAP Error - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Edit SAP Error</h3>
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
            <a href="{{ route('sap-errors.show', $sapError) }}">Error Details</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Edit</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Edit Error Information</div>
            </div>
            <div class="card-body">
                <form action="{{ route('sap-errors.update', $sapError) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                 <label class="form-label" for="title">Error Title <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" id="title" name="title" 
                                        value="{{ old('title', $sapError->title) }}" required>
                                 @error('title')
                                     <small class="form-text text-danger">{{ $message }}</small>
                                 @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label" for="sap_tcode">SAP T-Code</label>
                                <input type="text" class="form-control" id="sap_tcode" name="sap_tcode" 
                                       value="{{ old('sap_tcode', $sapError->sap_tcode) }}">
                                 @error('sap_tcode')
                                     <small class="form-text text-danger">{{ $message }}</small>
                                 @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label class="form-label" for="description">Detailed Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $sapError->description) }}</textarea>
                                @error('description')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label class="form-label" for="image">Replace Error Screenshot (Max: 5MB)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current screenshot</small>
                                @error('image')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            @if($sapError->image_path)
                            <div class="form-group pt-2 mb-0">
                                <div class="form-check p-0">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="remove_image" id="remove_image" value="1">
                                    <label class="form-check-label text-danger" for="remove_image">
                                        Remove current screenshot
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <img src="{{ Storage::url($sapError->image_path) }}" alt="Current Screenshot" class="img-thumbnail" style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-action d-flex flex-wrap gap-2 pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-success flex-grow-1 flex-sm-grow-0">
                            <i class="fa fa-check me-2"></i>Update Error
                        </button>
                        <a href="{{ route('sap-errors.show', $sapError) }}" class="btn btn-secondary flex-grow-1 flex-sm-grow-0">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
