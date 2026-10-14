@extends('layouts.dashboard')

@section('title', 'Edit Vials Data - ' . $injectionDetail->product_name)

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Edit Vials Data</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('products.index') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">
            <a href="{{ route('injection_details.index') }}">Vials Data</a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Edit Entry</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2 text-warning"></i>Edit Vials Data
                    </h4>
                    <a href="{{ route('injection_details.index') }}" class="btn btn-secondary btn-sm btn-round ms-auto">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('injection_details.update', $injectionDetail) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="vials-row border rounded p-3 mb-3 bg-light">
                        <div class="row g-2 g-md-3">
                            <!-- Product Name -->
                            <div class="col-12 col-lg-4">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="product_name">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                           id="product_name" name="product_name"
                                           placeholder="Abacus 40mg/5ml Injection"
                                           value="{{ old('product_name', $injectionDetail->product_name) }}" required>
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Batch No -->
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="batch_no">Batch No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('batch_no') is-invalid @enderror"
                                           id="batch_no" name="batch_no"
                                           placeholder="013P056" maxlength="7"
                                           value="{{ old('batch_no', $injectionDetail->batch_no) }}" required>
                                    @error('batch_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Batch Size -->
                            <div class="col-6 col-md-4 col-lg-2">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="batch_size">Batch Size</label>
                                    <input type="text" class="form-control @error('batch_size') is-invalid @enderror"
                                           id="batch_size" name="batch_size"
                                           placeholder="100000" maxlength="7"
                                           value="{{ old('batch_size', $injectionDetail->batch_size) }}">
                                </div>
                            </div>

                            <!-- Optical Qty -->
                            <div class="col-6 col-md-3 col-lg-1">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="optical_qty">Opt Qty</label>
                                    <input type="text" class="form-control px-1 text-center @error('optical_qty') is-invalid @enderror"
                                           id="optical_qty" name="optical_qty"
                                           placeholder="100000" maxlength="7"
                                           value="{{ old('optical_qty', $injectionDetail->optical_qty) }}">
                                </div>
                            </div>

                            <!-- Pkg Qty -->
                            <div class="col-6 col-md-3 col-lg-1">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="pkg_qty">Pkg Qty</label>
                                    <input type="text" class="form-control px-1 text-center @error('pkg_qty') is-invalid @enderror"
                                           id="pkg_qty" name="pkg_qty"
                                           placeholder="100000" maxlength="7"
                                           value="{{ old('pkg_qty', $injectionDetail->pkg_qty) }}">
                                </div>
                            </div>

                            <!-- Rejection -->
                            <div class="col-6 col-md-3 col-lg-1">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="rejection">Rejection</label>
                                    <input type="text" class="form-control px-1 text-center @error('rejection') is-invalid @enderror"
                                           id="rejection" name="rejection"
                                           placeholder="10" maxlength="4"
                                           value="{{ old('rejection', $injectionDetail->rejection) }}">
                                </div>
                            </div>

                            <!-- Error -->
                            <div class="col-6 col-md-3 col-lg-1">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1" for="error">Error</label>
                                    <input type="text" class="form-control px-1 text-center @error('error') is-invalid @enderror"
                                           id="error" name="error"
                                           placeholder="05" maxlength="4"
                                           value="{{ old('error', $injectionDetail->error) }}">
                                    @error('error')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Entry info -->
                    <div class="alert alert-light border mb-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Entry created on <strong>{{ $injectionDetail->created_at->format('M d, Y \a\t H:i') }}</strong>
                        </small>
                    </div>

                    <div class="card-action d-flex flex-wrap gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-success flex-grow-1 flex-sm-grow-0">
                            <i class="fas fa-save me-2"></i>Update Vials Data
                        </button>
                        <a href="{{ route('injection_details.index') }}" class="btn btn-secondary flex-grow-1 flex-sm-grow-0">
                            <i class="fas fa-times me-2"></i>Cancel
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
    .form-group { margin-bottom: 1rem; }
    .card-action { padding-top: 1rem; border-top: 1px solid #eee; }
</style>
@endpush
