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
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Search by name, batch no, or stage...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-control" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="Injection">Injection</option>
                            <option value="Suspension">Suspension</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Capsule">Capsule</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary w-100" id="clearFilters">
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Documents Table -->
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
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold text-dark">{{ $product->name }}</h6>
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>{{ $product->created_at->format('M d, Y') }}
                                    </small>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
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
@endpush
