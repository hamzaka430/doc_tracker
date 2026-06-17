@extends('layouts.dashboard')

@section('title', 'SAP Errors - Doc Tracker')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">SAP Errors</h3>
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
            <a href="#">SAP Errors</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Errors List</h4>
                    <a href="{{ route('sap-errors.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus me-1"></i>
                        Add Error
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="errorsTable" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>SAP T-Code</th>
                                <th>Date Added</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sapErrors as $error)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-deep-navy">{{ $error->title }}</div>
                                    </td>
                                    <td>
                                        @if($error->sap_tcode)
                                            <code class="bg-light px-2 py-1 rounded">{{ $error->sap_tcode }}</code>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $error->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('sap-errors.show', $error) }}" 
                                               class="btn btn-link btn-primary btn-lg p-1" 
                                               data-bs-toggle="tooltip" 
                                               title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sap-errors.edit', $error) }}" 
                                               class="btn btn-link btn-info btn-lg p-1" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('sap-errors.destroy', $error) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-link btn-danger p-1" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this error?')">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3 d-block"></i>
                                        <h5 class="text-muted">No Errors Recorded</h5>
                                        <p class="text-muted">Start by adding your first SAP error.</p>
                                        <a href="{{ route('sap-errors.create') }}" class="btn btn-primary mt-3">
                                            <i class="fa fa-plus me-2"></i>Add First Error
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
    $('#errorsTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "info": true,
        "language": {
            "emptyTable": "No errors available"
        }
    });

    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
