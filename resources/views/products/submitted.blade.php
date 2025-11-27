@extends('layouts.dashboard')

@section('title', 'Submitted Documents')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Submitted Documents</h3>
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
            <a href="#">Submitted</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Submitted Documents ({{ $submittedProducts->count() }})</h4>
                    @if($submittedProducts->count() > 0)
                        <a href="{{ route('products.export') }}" class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-download me-1"></i>
                            Export
                        </a>
                    @endif
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

                <!-- Submitted Documents Table -->
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
                                                {{ $product->type ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stage-badge">{{ $product->stage }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="text-dark fw-semibold">
                                                    {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                                                </div>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="openDateModal({{ $product->id }}, '{{ $product->submission_date ? $product->submission_date->format('Y-m-d') : '' }}', '{{ $product->submission_time ? $product->submission_time->format('H:i') : '' }}')"
                                                        title="Edit Date">
                                                    <i class="fas fa-edit"></i>
                                                </button>
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
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('products.show', $product) }}" 
                                                   class="btn btn-link btn-primary btn-lg p-1"
                                                   data-bs-toggle="tooltip"
                                                   title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
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

@section('modals')
<!-- Edit Submission Date Modal -->
<div class="modal fade" id="editDateModal" tabindex="-1" aria-labelledby="editDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDateModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Edit Submission Date
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="submission_date" class="form-label">Submission Date</label>
                        <input type="date" class="form-control" id="submission_date" name="submission_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="submission_time" class="form-label">Submission Time (Optional)</label>
                        <input type="time" class="form-control" id="submission_time" name="submission_time">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDateModal(productId, currentDate, currentTime) {
    const modal = new bootstrap.Modal(document.getElementById('editDateModal'));
    const form = document.getElementById('editDateForm');
    const dateInput = document.getElementById('submission_date');
    const timeInput = document.getElementById('submission_time');
    
    // Set form action URL
    form.action = `/products/${productId}/submission-date`;
    
    // Set current values
    dateInput.value = currentDate;
    timeInput.value = currentTime;
    
    // Show modal
    modal.show();
}

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
@endpush