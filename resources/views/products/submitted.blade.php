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
                <form action="{{ route('products.submitted') }}" method="GET" class="mb-4">
                    <div class="row g-2 g-md-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search submitted...">
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <select class="form-select form-control" name="type" id="typeFilter" onchange="this.form.submit()">
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
                            <a href="{{ route('products.submitted') }}" class="btn btn-secondary flex-grow-1 px-2">Clear</a>
                        </div>
                    </div>
                </form>

                <!-- Submitted Documents Desktop Table -->
                @if($submittedProducts->count() > 0)
                    <div class="table-responsive d-none d-lg-block">
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
                                                        onclick="openDateModal({{ $product->id }}, '{{ $product->submission_date ? $product->submission_date->format('Y-m-d') : '' }}', '{{ $product->submission_time ? substr($product->submission_time, 0, 5) : '' }}')"
                                                        title="Edit Date">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-dark">
                                                {{ $product->submission_time ?? 'N/A' }}
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
                                                @if($product->isEditable())
                                                    @if(auth()->user()->canModifyData($product->user_id))
                                                        <a href="{{ route('products.edit', $product) }}" 
                                                           class="btn btn-link btn-info btn-lg p-1" 
                                                           data-bs-toggle="tooltip" 
                                                           title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Submitted Documents Mobile Card View -->
                    <div class="d-lg-none" id="mobileSubmittedList">
                        @foreach($submittedProducts as $product)
                            <div class="card shadow-sm mb-3 product-card mobile-doc-card"
                                 data-name="{{ strtolower($product->name) }}"
                                 data-batch="{{ strtolower($product->batch_no) }}"
                                 data-stage="{{ strtolower($product->stage) }}"
                                 data-type="{{ $product->type }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">{{ $product->name }}</h6>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle me-1"></i> Submitted
                                            </span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary py-1 px-2" 
                                                onclick="openDateModal({{ $product->id }}, '{{ $product->submission_date ? $product->submission_date->format('Y-m-d') : '' }}', '{{ $product->submission_time ? substr($product->submission_time, 0, 5) : '' }}')"
                                                title="Edit Date & Time">
                                            <i class="fas fa-calendar-alt me-1"></i> Date
                                        </button>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">BATCH NO</small>
                                                <code class="bg-white px-2 py-1 rounded d-inline-block mt-1">{{ $product->batch_no }}</code>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">TYPE</small>
                                                <span class="badge bg-dark text-white mt-1">{{ $product->type ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">SUBMITTED DATE</small>
                                                <span class="fw-semibold text-dark mt-1 d-block" style="font-size: 0.8rem;">
                                                    {{ $product->submission_date ? $product->submission_date->format('M d, Y') : 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">SUBMITTED TIME</small>
                                                <span class="text-dark mt-1 d-block" style="font-size: 0.8rem;">
                                                    {{ $product->submission_time ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-2 bg-light rounded d-flex justify-content-between align-items-center">
                                                <small class="text-muted" style="font-size: 0.72rem;">STAGE</small>
                                                <span class="stage-badge py-1 px-2">{{ $product->stage }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($product->remarks)
                                        <div class="p-2 bg-light rounded mb-3">
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">REMARKS</small>
                                            <small class="text-dark">{{ $product->remarks }}</small>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                            <i class="fa fa-eye me-1"></i> View Details
                                        </a>
                                        @if($product->isEditable() && auth()->user()->canModifyData($product->user_id))
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-info btn-sm flex-grow-1">
                                                <i class="fa fa-edit me-1"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $submittedProducts->appends(request()->query())->links('pagination::bootstrap-5') }}
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
                @method('PATCH')
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
});
</script>
@endpush