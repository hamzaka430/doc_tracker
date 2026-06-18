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
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-dark text-white shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-white mb-3"><i class="fas fa-chart-line me-2"></i>Submission Analytics (Last 7 Days)</h5>
                        <div style="height: 250px;">
                            <canvas id="submissionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">All Documents</h4>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus me-1"></i>
                        Add Document
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <form action="{{ route('products.index') }}" method="GET" class="mb-4">
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
                            <a href="{{ route('products.index') }}" class="btn btn-secondary flex-grow-1 px-1">Clear</a>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="display table table-striped table-hover">
                        <thead>
                            <tr>
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
                                    <td colspan="7" class="text-center py-5">
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
                
                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Initialize Chart
    const chartData = @json(json_decode($chartDataJson));
    const ctx = document.getElementById('submissionChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Documents Submitted',
                data: chartData.data,
                borderColor: '#00f2fe',
                backgroundColor: 'rgba(0, 242, 254, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#4facfe',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#4facfe',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
