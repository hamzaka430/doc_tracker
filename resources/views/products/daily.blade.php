@extends('app')

@section('title', 'Daily Documents')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="page-title">
                <i class="fas fa-calendar-day text-luxury-gold me-3"></i>
                Today's Documents
            </h1>
            <p class="page-subtitle">Documents added today</p>
        </div>
        <div class="text-end">
            <span class="badge badge-luxury fs-6">{{ $products->count() }} Today</span>
        </div>
    </div>
</div>

<div class="mobile-card">
    <div class="mobile-card-header">
        <h3 class="mobile-card-title">
            <i class="fas fa-file-lines text-luxury-gold me-2"></i>
            Today's Documents
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
                <h5 class="text-muted mb-2">No Documents Today</h5>
                <p class="text-muted mb-3">No documents were added today.</p>
            </div>
        @endif
    </div>
</div>

@endsection
