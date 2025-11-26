@extends('app')

@section('title', 'Daily Documents')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-calendar-day text-luxury-gold me-3"></i>
                Remaining Documents
            </h1>
            <p class="page-subtitle">All pending documents to be processed</p>
        </div>
        <div class="text-end d-flex align-items-center gap-3">
            <span class="badge badge-luxury fs-6">{{ $products->count() }} Remaining</span>
            @if($products->count() > 0)
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#pdfLayoutModal">
                    <i class="fas fa-file-pdf me-2"></i>
                    Download PDF
                </button>
            @endif
        </div>
    </div>
</div>

<div class="mobile-card">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-file-lines text-luxury-gold me-2"></i>
            All Remaining Documents
        </h3>
    </div>
    <div class="mobile-card-body p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Batch No</th>
                            <th>Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
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
        @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">No Remaining Documents</h5>
                <p class="text-muted mb-3">All documents have been processed and submitted.</p>
            </div>
        @endif
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
                        <a href="{{ route('products.daily.pdf', ['layout' => 'single']) }}" class="text-decoration-none">
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
                        <a href="{{ route('products.daily.pdf', ['layout' => 'double']) }}" class="text-decoration-none">
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

@endsection
