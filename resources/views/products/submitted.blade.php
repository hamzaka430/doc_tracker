@extends('app')

@section('title', 'Submitted Documents')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-clipboard-check text-luxury-gold me-3"></i>
                Submitted Documents
            </h1>
            <p class="page-subtitle">
                Total submitted: <strong>{{ $submittedProducts->count() }}</strong> documents
            </p>
        </div>
        @if($submittedProducts->count() > 0)
            <a href="{{ route('products.export') }}" class="btn btn-luxury btn-export">
                <i class="fas fa-download me-2"></i>Export
            </a>
        @endif
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="mobile-card mb-4">
    <div class="mobile-card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control search-input" id="searchInput" 
                           placeholder="Search by name, batch no, or stage...">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <!-- <label class="form-label">
                    <i class="fas fa-filter me-2"></i>Filter by Type
                </label> -->
                <div class="custom-select-wrapper">
                    <i class="fas fa-pills icon-left"></i>
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="Injection">Injection</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                    </select>
                    <i class="fas fa-chevron-down icon-right"></i>
                </div>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-secondary w-100 h-100" id="clearFilters">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Submitted Documents Table -->
    <div class="col-12">
        <div class="card fade-in-up">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-table text-luxury-gold me-2"></i>
                    Submitted Documents
                    @if(request('search'))
                        <small class="text-muted ms-2">
                            (filtered by "{{ request('search') }}")
                        </small>
                    @endif
                </h4>
            </div>
            <div class="card-body p-0">
                @if($submittedProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="submittedTable">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>Batch No</th>
                                    <th>Type</th>
                                    <th>Stage</th>
                                    <th>Submission Date</th>
                                    <th>Submission Time</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submittedProducts as $product)
                                    <tr class="product-row"
                                        data-name="{{ strtolower($product->name) }}"
                                        data-batch="{{ strtolower($product->batch_no) }}"
                                        data-stage="{{ strtolower($product->stage) }}"
                                        data-type="{{ $product->type }}">
                                        <td>
                                            <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
                                        </td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark text-white">
                                                <i class="fas fa-pills me-1"></i>{{ $product->type ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stage-badge">{{ $product->stage }}</span>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold">
                                                {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-dark">
                                                {{ $product->submission_time ? $product->submission_time->format('H:i:s') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($product->remarks)
                                                <span class="text-muted" 
                                                      data-bs-toggle="tooltip" 
                                                      data-bs-title="{{ $product->remarks }}">
                                                                            <i class="fas fa-comment-dots"></i>
                                                    {{ Str::limit($product->remarks, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5" id="emptyState">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted lead">No submitted documents yet.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-dark">
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Search and Filter functionality
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const emptyState = document.getElementById('emptyState');
    
    // Get all product rows
    const productRows = document.querySelectorAll('.product-row');
    const totalProducts = productRows.length;

    // Filter function
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedType = typeFilter.value;
        
        let visibleCount = 0;

        productRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const batch = row.getAttribute('data-batch');
            const stage = row.getAttribute('data-stage');
            const type = row.getAttribute('data-type');
            
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                batch.includes(searchTerm) || 
                stage.includes(searchTerm);
            
            const matchesType = !selectedType || type === selectedType;
            
            if (matchesSearch && matchesType) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0 && totalProducts > 0) {
            noResultsMessage.classList.remove('d-none');
            if (emptyState) emptyState.classList.add('d-none');
        } else {
            noResultsMessage.classList.add('d-none');
            if (emptyState && totalProducts === 0) emptyState.classList.remove('d-none');
        }
    }

    // Event listeners
    if (searchInput && typeFilter) {
        searchInput.addEventListener('input', filterProducts);
        typeFilter.addEventListener('change', filterProducts);
        
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            typeFilter.value = '';
            filterProducts();
        });

        // Auto-focus on search input if there are products
        if (totalProducts > 0) {
            searchInput.focus();
        }
    }
});
</script>
@endsection