@extends('layouts.dashboard')

@section('title', 'All Documents - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">All Documents</h3>
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
            <a href="#">All Documents</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Document List</h4>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus me-1"></i>
                        Add Document
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, batch no, or stage..." />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-control" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="Injection">Injection</option>
                            <option value="Suspension">Suspension</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Capsule">Capsule</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-secondary w-100" id="clearFilters">
                            Clear Filters
                        </button>
                    </div>
                    <div class="col-md-3">
                        <form id="bulkSubmitForm" action="{{ route('products.bulkSubmit') }}" method="POST" class="w-100 d-none">
                            @csrf
                            <button type="button" class="btn btn-success w-100 fw-bold" id="bulkSubmitBtn">
                                <i class="fa fa-check-double me-1"></i> Bulk Submit (<span id="bulkCount">0</span>)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="productsTable" class="display table table-striped table-hover">
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
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="searchable-row product-row" 
                                    data-name="{{ strtolower($product->name) }}"
                                    data-batch="{{ strtolower($product->batch_no) }}"
                                    data-stage="{{ strtolower($product->stage) }}"
                                    data-type="{{ $product->type }}">
                                    <td>
                                        @if(!$product->isSubmitted())
                                            <div class="form-check p-0 m-0">
                                                <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                            </div>
                                        @endif
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
                                        @if($product->status === 'submitted')
                                            <span class="badge badge-success">Submitted</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
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
                                            <div class="progress-bar bg-success" 
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
                                            <a href="{{ route('products.create', ['name' => $product->name, 'batch_no' => $product->batch_no, 'stage' => $product->stage, 'type' => $product->type]) }}" 
                                               class="btn btn-link btn-warning btn-lg p-1" 
                                               data-bs-toggle="tooltip" 
                                               title="Duplicate / Copy">
                                                <i class="fa fa-copy"></i>
                                            </a>
                                            @if(!$product->isSubmitted())
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
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                        <h5 class="text-muted">No Documents Found</h5>
                                        <p class="text-muted">Start by adding your first document to the tracking system.</p>
                                        <a href="{{ route('products.create') }}" class="btn btn-primary mt-3">
                                            <i class="fa fa-plus me-2"></i>Add First Document
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#productsTable').DataTable({
        "pageLength": 10,
        "ordering": false, // Disable sorting arrows
        "info": true,
        "searching": false, // We use custom search
        "language": {
            "emptyTable": "No documents available"
        }
    });

    // Custom search functionality
    function filterTable() {
        var searchTerm = $('#searchInput').val().toLowerCase();
        var selectedType = $('#typeFilter').val();

        table.rows().every(function() {
            var row = this.node();
            var $row = $(row);
            
            var name = $row.data('name') || '';
            var batch = $row.data('batch') || '';
            var stage = $row.data('stage') || '';
            var type = $row.data('type') || '';
            
            var matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                batch.includes(searchTerm) || 
                stage.includes(searchTerm);
            
            var matchesType = !selectedType || type === selectedType;
            
            if (matchesSearch && matchesType) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }

    // Event listeners
    $('#searchInput').on('keyup', filterTable);
    $('#typeFilter').on('change', filterTable);
    
    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#typeFilter').val('');
        filterTable();
        table.search('').draw();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

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
        
        if(bulkCountSpan) bulkCountSpan.textContent = checkedCount;
        
        if (checkedCount > 0) {
            if(bulkSubmitForm) bulkSubmitForm.classList.remove('d-none');
        } else {
            if(bulkSubmitForm) bulkSubmitForm.classList.add('d-none');
            if (selectAllDesktop) selectAllDesktop.checked = false;
        }
    }

    if (selectAllDesktop) {
        selectAllDesktop.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(cb => {
                const row = cb.closest('.searchable-row');
                if (row && row.style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateBulkSubmitVisibility();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkSubmitVisibility);
    });

    if(bulkSubmitBtn) {
        bulkSubmitBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            const uniqueIds = new Set();
            checkedBoxes.forEach(cb => uniqueIds.add(cb.value));
            
            if (uniqueIds.size === 0) return;

            if (confirm(`Are you sure you want to bulk submit ${uniqueIds.size} documents?`)) {
                bulkSubmitForm.querySelectorAll('input[name="product_ids[]"]').forEach(input => input.remove());
                
                uniqueIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'product_ids[]';
                    input.value = id;
                    bulkSubmitForm.appendChild(input);
                });
                
                bulkSubmitForm.submit();
            }
        });
    }
});
</script>
@endpush
