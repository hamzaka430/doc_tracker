@extends('layouts.dashboard')

@section('title', 'Recycle Bin')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Recycle Bin</h3>
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
            <a href="#">Trash</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Deleted Documents</h4>
                </div>
            </div>
            <div class="card-body">

                <!-- Search and Filter -->
                <form action="{{ route('products.trash') }}" method="GET" class="row mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="searchInput" 
                                   placeholder="Search by name, batch no, or stage...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-control" name="type" id="typeFilter" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Injection" {{ request('type') == 'Injection' ? 'selected' : '' }}>Injection</option>
                            <option value="Suspension" {{ request('type') == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                            <option value="Tablet" {{ request('type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Capsule" {{ request('type') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 px-1">Search</button>
                        <a href="{{ route('products.trash') }}" class="btn btn-secondary flex-grow-1 px-1" id="clearFilters">Clear</a>
                    </div>
                </form>

                <!-- Documents Table -->
        @if($products->count() > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover mb-0" id="productsTable">
                    <thead>
                        <tr>
                            <th style="width: 40px">
                                <div class="form-check p-0 m-0">
                                    <input type="checkbox" class="form-check-input" id="selectAllDesktop">
                                </div>
                            </th>
                            <th>Document Name</th>
                            <th>Batch No</th>
                            <th>Type</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="product-row" 
                                data-name="{{ strtolower($product->name) }}"
                                data-batch="{{ strtolower($product->batch_no) }}"
                                data-stage="{{ strtolower($product->stage) }}"
                                data-type="{{ $product->type }}">
                                <td>
                                    <div class="form-check p-0 m-0">
                                        <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white">
                                        {{ $product->type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="stage-badge">{{ $product->stage }}</span>
                                </td>
                                <td>
                                    <span class="badge status-{{ strtolower($product->status) }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $completed = 0;
                                        if($product->line_clearance) $completed++;
                                        if($product->review) $completed++;
                                        if($product->confirmation) $completed++;
                                        $percentage = ($completed / 3) * 100;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-dark text-white fw-semibold" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%"
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $completed }}/3
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('products.restore', $product) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-success btn-sm" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Restore Document">
                                                <i class="fa fa-undo me-1"></i> Restore
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-lg-none" id="mobileProductsList">
                @foreach($products as $product)
                    <div class="card shadow-sm mb-3 product-card" 
                         data-name="{{ strtolower($product->name) }}"
                         data-batch="{{ strtolower($product->batch_no) }}"
                         data-stage="{{ strtolower($product->stage) }}"
                         data-type="{{ $product->type }}">
                        <div class="card-body p-3">
                            <!-- Header Section -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-start flex-grow-1">
                                    <div class="form-check p-0 m-0 me-3 mt-1">
                                        <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark">{{ $product->name }}</h6>
                                        <small class="text-muted">
                                            <i class="far fa-calendar me-1"></i>{{ $product->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                                <span class="badge status-{{ strtolower($product->status) }} ms-2">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </div>
                            
                            <!-- Info Grid -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-barcode me-1"></i>Batch
                                        </small>
                                        <code class="bg-white px-2 py-1 rounded d-inline-block">{{ $product->batch_no }}</code>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-tag me-1"></i>Type
                                        </small>
                                        <span class="badge bg-dark text-white">
                                            {{ $product->type ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 bg-light rounded">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-layer-group me-1"></i>Stage
                                        </small>
                                        <span class="stage-badge">{{ $product->stage }}</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $completed = 0;
                                if($product->line_clearance) $completed++;
                                if($product->review) $completed++;
                                if($product->confirmation) $completed++;
                                $percentage = ($completed / 3) * 100;
                            @endphp
                            
                            <!-- Progress Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-tasks me-1"></i>Progress
                                    </small>
                                    <small class="text-muted fw-semibold">{{ $completed }}/3 Tasks</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-dark" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 border-top pt-3">
                                <form action="{{ route('products.restore', $product) }}" method="POST" class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fa fa-undo me-2"></i>Restore Document
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5" id="emptyState">
                <i class="fas fa-trash-restore fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">Recycle Bin is Empty</h5>
                <p class="text-muted mb-3">No deleted documents found.</p>
            </div>
        @endif
        
                <!-- No Results Message (Hidden by default) -->
                <div class="text-center py-5 d-none" id="noResultsMessage">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted mb-2">No Documents Found</h5>
                    <p class="text-muted mb-3">Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Ensure proper spacing on mobile for footer */
    @media (max-width: 767.98px) {
        .page-inner {
            padding-bottom: 2rem;
        }
        
        .card-body {
            padding: 1rem 0.75rem;
        }
        
        .row.mb-4 {
            margin-bottom: 1rem !important;
        }
        
        .input-group {
            margin-bottom: 0.75rem;
        }
        
        .col-md-5, .col-md-4, .col-md-3 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllDesktop = document.getElementById('selectAllDesktop');
    const checkboxes = document.querySelectorAll('.product-checkbox');

    if (selectAllDesktop) {
        selectAllDesktop.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(cb => {
                const row = cb.closest('.product-row') || cb.closest('.product-card');
                if (row.style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
        });
    }
});
</script>
@endpush
