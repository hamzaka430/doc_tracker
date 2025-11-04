@extends('app')

@section('title', 'Document List')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-lines text-luxury-gold me-3"></i>
                Document List
            </h1>
            <p class="page-subtitle">Manage and track all pending documents</p>
        </div>
        <div class="text-end">
            <span class="badge badge-luxury fs-6" id="documentCount">{{ $products->count() }} Documents</span>
        </div>
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

<!-- Product List -->
<div class="mobile-card">
            <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-file-text text-luxury-gold me-2"></i>
            Pending Documents
        </h3>
    </div>
    <div class="mobile-card-body p-0">
        @if($products->count() > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover mb-0" id="productsTable">
                    <thead>
                        <tr>
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
                                    <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white">
                                        <i class=""></i>{{ $product->type ?? 'N/A' }}
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
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="btn btn-outline-dark btn-sm border" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-outline-dark btn-sm border" 
                                           title="Edit Product">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                        @if(!$product->isSubmitted())
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm border" 
                                                        title="Delete Product"
                                                        onclick="return confirm('Are you sure you want to delete this document?')">
                                                    <i class="fas fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
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
                    <div class="border-bottom p-3 product-card" 
                         data-name="{{ strtolower($product->name) }}"
                         data-batch="{{ strtolower($product->batch_no) }}"
                         data-stage="{{ strtolower($product->stage) }}"
                         data-type="{{ $product->type }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 text-deep-navy">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->created_at->format('M d, Y') }}</small>
                            </div>
                            <span class="badge status-{{ strtolower($product->status) }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Batch No</small>
                                <code class="bg-light px-1 rounded">{{ $product->batch_no }}</code>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Type</small>
                                <span class="badge bg-dark text-white">
                                    <i class="fas fa-pills me-1"></i>{{ $product->type ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Stage</small>
                                <span class="stage-badge">{{ $product->stage }}</span>
                            </div>
                        </div>

                        @php
                            $completed = 0;
                            if($product->line_clearance) $completed++;
                            if($product->review) $completed++;
                            if($product->confirmation) $completed++;
                            $percentage = ($completed / 3) * 100;
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Progress</small>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-dark text-white fw-semibold" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $completed }}/3 Tasks
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('products.show', $product) }}" 
                               class="btn btn-outline-dark btn-sm flex-fill border" 
                               title="View Details">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('products.edit', $product) }}" 
                               class="btn btn-outline-dark btn-sm border" 
                               title="Edit Product">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            @if(!$product->isSubmitted())
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger btn-sm border" 
                                            title="Delete Product"
                                            onclick="return confirm('Delete this document?')">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5" id="emptyState">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">No Documents Found</h5>
                <p class="text-muted mb-3">Start by adding your first document to the tracking system.</p>
                <a href="{{ route('products.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus me-2"></i>Add First Document
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const documentCount = document.getElementById('documentCount');
    const noResultsMessage = document.getElementById('noResultsMessage');
    
    // Get all product rows (desktop and mobile)
    const productRows = document.querySelectorAll('.product-row');
    const productCards = document.querySelectorAll('.product-card');
    const totalProducts = productRows.length || productCards.length;

    // Filter function
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedType = typeFilter.value;
        
        let visibleCount = 0;

        // Filter desktop table rows
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

        // Filter mobile cards
        productCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const batch = card.getAttribute('data-batch');
            const stage = card.getAttribute('data-stage');
            const type = card.getAttribute('data-type');
            
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                batch.includes(searchTerm) || 
                stage.includes(searchTerm);
            
            const matchesType = !selectedType || type === selectedType;
            
            if (matchesSearch && matchesType) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update document count
        documentCount.textContent = visibleCount + ' Document' + (visibleCount !== 1 ? 's' : '');
        
        // Show/hide no results message
        if (visibleCount === 0 && totalProducts > 0) {
            noResultsMessage.classList.remove('d-none');
        } else {
            noResultsMessage.classList.add('d-none');
        }
    }

    // Event listeners
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
});
</script>
@endsection