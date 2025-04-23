<!-- resources/views/admin/games/services/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Services for ' . $game->name)

@section('page-title', 'Services for ' . $game->name)

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Game Services</h5>
                <div>
                    <a href="{{ route('admin.games.show', $game->id) }}" class="btn btn-sm btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Game
                    </a>
                    <a href="{{ route('admin.games.services.create', $game->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Add New Service
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="servicesTable">
                        <thead>
                            <tr>
                                <th width="60">Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Options</th>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this service? This action cannot be undone and will also delete all related options.
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
        var table = $('#servicesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.games.services.index', $game->id) }}",
            columns: [
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'options_count', name: 'options_count' },
                { data: 'status_badge', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
        
        // Delete Service
        var deleteId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').click(function() {
            $.ajax({
                url: "{{ url('admin/games/' . $game->id . '/services') }}/" + deleteId,
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
                        text: 'An error occurred while deleting the service.'
                    });
                }
            });
        });
    });
</script>
@endpush