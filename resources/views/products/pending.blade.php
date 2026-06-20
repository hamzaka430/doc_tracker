@extends('layouts.dashboard')

@section('title', 'Pending Documents')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Pending Documents</h3>
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
            <a href="#">Pending</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Pending Documents</h4>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus me-1"></i>
                        Add Document
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Search and Filter -->
                <!-- Search and Filter -->
                <form action="{{ route('products.pending') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-control" name="type" id="typeFilter" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="Injection" {{ request('type') == 'Injection' ? 'selected' : '' }}>Injection</option>
                                <option value="Suspension" {{ request('type') == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                                <option value="Tablet" {{ request('type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="Capsule" {{ request('type') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="To Date">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 px-1">Filter</button>
                            <button type="button" class="btn btn-success flex-grow-1 fw-bold" id="bulkSubmitBtn" disabled>
                                <i class="fa fa-check-double"></i> Submit (<span id="bulkCount">0</span>)
                            </button>
                        </div>
                    </div>
                </form>

                <form id="bulkSubmitForm" action="{{ route('products.bulkSubmit') }}" method="POST" class="d-none">
                    @csrf
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
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="btn btn-link btn-primary btn-lg p-1" 
                                           data-bs-toggle="tooltip" 
                                           title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-link btn-info btn-lg p-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-link btn-danger p-1" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this document?')">
                                                <i class="fa fa-times"></i>
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
                                <a href="{{ route('products.show', $product) }}" 
                                   class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="fa fa-eye me-1"></i>View
                                </a>
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Delete this document?')">
                                        <i class="fa fa-trash"></i>
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
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-muted mb-2">No Pending Documents</h5>
                <p class="text-muted mb-3">All documents have been submitted!</p>
                <a href="{{ route('products.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Add New Document
                </a>
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

    // Bulk Submit Functionality
    const selectAllDesktop = document.getElementById('selectAllDesktop');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const bulkSubmitForm = document.getElementById('bulkSubmitForm');
    const bulkCountSpan = document.getElementById('bulkCount');
    const bulkSubmitBtn = document.getElementById('bulkSubmitBtn');

    function updateBulkSubmitVisibility() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const uniqueIds = new Set();
        checkedBoxes.forEach(cb => uniqueIds.add(cb.value));
        const checkedCount = uniqueIds.size;
        
        bulkCountSpan.textContent = checkedCount;
        
        if (checkedCount > 0) {
            if (bulkSubmitBtn) bulkSubmitBtn.disabled = false;
        } else {
            if (bulkSubmitBtn) bulkSubmitBtn.disabled = true;
            if (selectAllDesktop) selectAllDesktop.checked = false;
        }
    }

    if (selectAllDesktop) {
        selectAllDesktop.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateBulkSubmitVisibility();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const isChecked = this.checked;
            const value = this.value;
            document.querySelectorAll(`.product-checkbox[value="${value}"]`).forEach(box => {
                box.checked = isChecked;
            });
            updateBulkSubmitVisibility();
        });
    });

    bulkSubmitBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const uniqueIds = new Set();
        checkedBoxes.forEach(cb => uniqueIds.add(cb.value));
        
        if (uniqueIds.size === 0) return;

        if (confirm(`Are you sure you want to bulk submit ${uniqueIds.size} documents?`)) {
            // Remove old hidden inputs
            bulkSubmitForm.querySelectorAll('input[name="product_ids[]"]').forEach(input => input.remove());
            
            // Add new hidden inputs
            uniqueIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_ids[]';
                input.value = id;
                bulkSubmitForm.appendChild(input);
            });
            
            // Disable button to prevent double clicks
            bulkSubmitBtn.disabled = true;
            bulkSubmitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Submitting...';
            
            bulkSubmitForm.submit();
        }
    });
});
</script>
@endpush
