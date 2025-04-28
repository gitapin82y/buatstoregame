<!-- resources/views/admin/games/services/options/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Service Options')

@section('page-title', 'Options for ' . $service->name)

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.index') }}">Games</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.show', $game->id) }}">{{ $game->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.index', $game->id) }}">Services</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.show', ['game' => $game->id, 'service' => $service->id]) }}">{{ $service->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Options</li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Service Options</h5>
                <div>
                    <a href="{{ route('admin.games.services.show', ['game' => $game->id, 'service' => $service->id]) }}" class="btn btn-sm btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Service
                    </a>
                    <a href="{{ route('admin.games.services.options.create', ['game' => $game->id, 'service' => $service->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Add New Option
                    </a>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                        <i class="fas fa-file-import me-1"></i> Bulk Import
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Service options are the specific variations of a service that customers can purchase, such as different diamond amounts for top-ups or different rank tiers for joki services.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="optionsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>API Code</th>
                                <th>Base Price</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Import Modal -->
<div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkImportModalLabel">Bulk Import Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.games.services.options.import', ['game' => $game->id, 'service' => $service->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="options_json" class="form-label">Enter options in JSON format</label>
                        <textarea class="form-control" id="options_json" name="options_json" rows="12" required>
[
    {
        "name": "Option 1",
        "description": "Description for option 1",
        "api_code": "ABC123",
        "base_price": 10000,
        "status": "active"
    },
    {
        "name": "Option 2",
        "description": "Description for option 2",
        "api_code": "DEF456",
        "base_price": 20000,
        "status": "active"
    }
]</textarea>
                        <small class="text-muted">Each option should have name, base_price (required), and optionally description, api_code, and status.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Options</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this option? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#optionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.games.services.options.index', ['game' => $game->id, 'service' => $service->id]) }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description', render: function(data) {
                    return data ? data.substring(0, 50) + (data.length > 50 ? '...' : '') : '-';
                }},
                { data: 'api_code', name: 'api_code', defaultContent: '-' },
                { data: 'base_price_formatted', name: 'base_price' },
                { data: 'status_badge', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
        
        // Delete Option
        var deleteId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').click(function() {
            $.ajax({
                url: "{{ url('admin/games/' . $game->id . '/services/' . $service->id . '/options') }}/" + deleteId,
                type: 'DELETE',
                success: function(data) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    });
                },
                error: function(error) {
                    $('#deleteModal').modal('hide');
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while deleting the option.'
                    });
                }
            });
        });
    });
</script>
@endpush