@extends('app')

@section('title', 'Product List')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-list text-luxury-gold me-3"></i>
                Product List
            </h1>
            <p class="page-subtitle">Manage and track all pending products</p>
        </div>
        <div class="text-end">
            <span class="badge badge-luxury fs-6">{{ $products->count() }} Products</span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('products.create') }}" class="mobile-card text-decoration-none h-100">
            <div class="mobile-card-body text-center">
                <i class="fas fa-plus-circle fa-2x text-luxury-gold mb-2"></i>
                <h6 class="mb-1">Add Product</h6>
                <small class="text-muted">Create new entry</small>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('products.submitted') }}" class="mobile-card text-decoration-none h-100">
            <div class="mobile-card-body text-center">
                <i class="fas fa-check-double fa-2x text-success mb-2"></i>
                <h6 class="mb-1">Submitted</h6>
                <small class="text-muted">View completed</small>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="mobile-card h-100">
            <div class="mobile-card-body text-center">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h6 class="mb-1">Pending</h6>
                <small class="text-muted">{{ $products->count() }} items</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('products.export') }}" class="mobile-card text-decoration-none h-100">
            <div class="mobile-card-body text-center">
                <i class="fas fa-download fa-2x text-info mb-2"></i>
                <h6 class="mb-1">Export</h6>
                <small class="text-muted">Download CSV</small>
            </div>
        </a>
    </div>
</div>

<!-- Product List -->
<div class="mobile-card">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-boxes text-luxury-gold me-2"></i>
            Pending Products
        </h3>
    </div>
    <div class="mobile-card-body p-0">
        @if($products->count() > 0)
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Batch No</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-deep-navy">{{ $product->name }}</div>
                                    <small class="text-muted">{{ $product->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $product->batch_no }}</code>
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
                                        if($product->pre_line_clearance) $completed++;
                                        if($product->in_process) $completed++;
                                        if($product->post_line_clearance) $completed++;
                                        $percentage = ($completed / 3) * 100;
                                    @endphp
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $completed }}/3</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$product->isSubmitted())
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="fas fa-trash"></i>
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
            <div class="d-lg-none">
                @foreach($products as $product)
                    <div class="border-bottom p-3">
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
                                <small class="text-muted d-block">Stage</small>
                                <span class="stage-badge">{{ $product->stage }}</span>
                            </div>
                        </div>

                        @php
                            $completed = 0;
                            if($product->pre_line_clearance) $completed++;
                            if($product->in_process) $completed++;
                            if($product->post_line_clearance) $completed++;
                            $percentage = ($completed / 3) * 100;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="text-muted">{{ $completed }}/3 completed</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('products.show', $product) }}" 
                               class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('products.edit', $product) }}" 
                               class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$product->isSubmitted())
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                            onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">No Products Found</h5>
                <p class="text-muted mb-3">Start by adding your first product to the tracking system.</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Product
                </a>
            </div>
        @endif
    </div>
</div>
@endsection