@extends('layouts.dashboard')

@section('title', 'Daily Documents')

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
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Daily Documents</h3>
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
            <a href="#">Daily</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">Remaining Documents ({{ $products->count() }})</h4>
                    <div class="d-flex ms-auto gap-2 flex-wrap">
                        <form id="bulkSubmitForm" action="{{ route('products.bulkSubmit') }}" method="POST" class="d-none">
                            @csrf
                            <button type="button" class="btn btn-success btn-round btn-sm" id="bulkSubmitBtn">
                                <i class="fa fa-check-double me-1"></i> Bulk Submit (<span id="bulkCount">0</span>)
                            </button>
                        </form>
                        @if($products->count() > 0)
                            <button type="button" class="btn btn-danger btn-round btn-sm" data-bs-toggle="modal" data-bs-target="#pdfLayoutModal">
                                <i class="fa fa-file-pdf me-1"></i>
                                Download PDF
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <!-- Search and Filter -->
                <form action="{{ route('products.daily') }}" method="GET" class="mb-4">
                    <div class="row g-2 g-md-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search...">
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <select class="form-select form-control" name="type" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="Injection" {{ request('type') == 'Injection' ? 'selected' : '' }}>Injection</option>
                                <option value="Suspension" {{ request('type') == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                                <option value="Tablet" {{ request('type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="Capsule" {{ request('type') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" title="From Date">
                        </div>
                        <div class="col-6 col-md-6 col-lg-2">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" title="To Date">
                        </div>
                        <div class="col-6 col-md-6 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 px-2">
                                <i class="fa fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('products.daily') }}" class="btn btn-secondary flex-grow-1 px-2">Clear</a>
                        </div>
                    </div>
                </form>

        @if($products->count() > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px">
                                <div class="form-check p-0 m-0">
                                    <input type="checkbox" class="form-check-input" id="selectAllDesktop">
                                </div>
                            </th>
                            <th>Document Name</th>
                            <th>Batch No</th>
                            <th>Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="product-row">
                                <td>
                                    <div class="form-check p-0 m-0">
                                        <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
                                </td>
                                <td>
                                    <span class="stage-badge">{{ $product->stage }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-lg-none" id="mobileDailyList">
                <div class="d-flex align-items-center mb-2 px-1">
                    <div class="form-check p-0 m-0 me-2">
                        <input type="checkbox" class="form-check-input" id="selectAllMobile">
                    </div>
                    <label for="selectAllMobile" class="text-muted small mb-0 fw-semibold">Select All For Bulk Submit</label>
                </div>
                @foreach($products as $product)
                    <div class="card shadow-sm mb-2 product-card mobile-doc-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <div class="form-check p-0 m-0 mt-1 me-1">
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $product->name }}</h6>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>{{ $product->created_at->format('h:i A') }}
                                    </small>
                                </div>
                                <span class="stage-badge py-1 px-2">{{ $product->stage }}</span>
                            </div>
                            <div class="p-2 bg-light rounded d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 0.72rem;">BATCH NUMBER</small>
                                <code class="bg-white px-2 py-1 rounded">{{ $product->batch_no }}</code>
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
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">No Remaining Documents</h5>
                <p class="text-muted mb-3">All documents have been processed and submitted.</p>
            </div>
        @endif
            </div>
        </div>
    </div>
</div>

<!-- PDF Layout Selection Modal -->
<div class="modal fade" id="pdfLayoutModal" tabindex="-1" aria-labelledby="pdfLayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-luxury-navy text-white">
                <h5 class="modal-title" id="pdfLayoutModalLabel">
                    <i class="fas fa-file-pdf me-2"></i>
                    PDF Layout Selection
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-4">Choose how you want to download the PDF:</p>
                
                <div class="row g-3">
                    <!-- Option 1: Single Column -->
                    <div class="col-12">
                        <a href="{{ route('products.daily.pdf', ['layout' => 'single']) }}" class="text-decoration-none" onclick="setTimeout(() => document.querySelector('#pdfLayoutModal .btn-close').click(), 100)">
                            <div class="card border-2 hover-shadow" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-file-alt fa-3x text-primary"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-2">Single Page Layout</h5>
                                            <p class="card-text text-muted mb-0">
                                                Normal portrait mode with one list per page. Standard format for viewing and printing.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Option 2: Two Column -->
                    <div class="col-12">
                        <a href="{{ route('products.daily.pdf', ['layout' => 'double']) }}" class="text-decoration-none" onclick="setTimeout(() => document.querySelector('#pdfLayoutModal .btn-close').click(), 100)">
                            <div class="card border-2 hover-shadow" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-columns fa-3x text-success"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-2">Two Column Layout (Split Page)</h5>
                                            <p class="card-text text-muted mb-0">
                                                Two identical lists side-by-side. Perfect for splitting one printed page into two halves.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk Submit Functionality
    const selectAllDesktop = document.getElementById('selectAllDesktop');
    const selectAllMobile = document.getElementById('selectAllMobile');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const bulkSubmitForm = document.getElementById('bulkSubmitForm');
    const bulkCountSpan = document.getElementById('bulkCount');
    const bulkSubmitBtn = document.getElementById('bulkSubmitBtn');

    // Total unique products available on this page
    const totalUniqueProductIds = new Set(Array.from(checkboxes).map(cb => cb.value)).size;

    function updateBulkSubmitVisibility() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const uniqueIds = new Set();
        checkedBoxes.forEach(cb => uniqueIds.add(cb.value));
        const checkedCount = uniqueIds.size;
        
        if (bulkCountSpan) bulkCountSpan.textContent = checkedCount;
        
        if (checkedCount > 0) {
            if (bulkSubmitForm) bulkSubmitForm.classList.remove('d-none');
        } else {
            if (bulkSubmitForm) bulkSubmitForm.classList.add('d-none');
        }

        // Keep Select All checkboxes in sync with current checked count
        const allSelected = totalUniqueProductIds > 0 && checkedCount === totalUniqueProductIds;
        if (selectAllDesktop) selectAllDesktop.checked = allSelected;
        if (selectAllMobile) selectAllMobile.checked = allSelected;
    }

    function toggleAll(isChecked) {
        checkboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        if (selectAllDesktop) selectAllDesktop.checked = isChecked;
        if (selectAllMobile) selectAllMobile.checked = isChecked;
        updateBulkSubmitVisibility();
    }

    if (selectAllDesktop) {
        selectAllDesktop.addEventListener('change', function() {
            toggleAll(this.checked);
        });
    }

    if (selectAllMobile) {
        selectAllMobile.addEventListener('change', function() {
            toggleAll(this.checked);
        });
    }

    // Synchronize individual checkboxes between Desktop and Mobile views
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const isChecked = this.checked;
            const val = this.value;
            // Mirror state to any other checkbox with the same product ID
            document.querySelectorAll(`.product-checkbox[value="${val}"]`).forEach(box => {
                box.checked = isChecked;
            });
            updateBulkSubmitVisibility();
        });
    });

    if (bulkSubmitBtn) {
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

@endsection
