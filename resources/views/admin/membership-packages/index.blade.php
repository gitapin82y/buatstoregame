<!-- resources/views/admin/membership-packages/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Membership Packages')

@section('page-title', 'Membership Packages')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Membership Packages</h1>
            <a href="{{ route('admin.membership-packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Add New Package
            </a>
        </div>
    </div>
    
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">All Packages</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="packagesTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Level</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Sales</th>
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
                <p>Are you sure you want to delete this membership package?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i> This action can only be performed if the package has not been used in any transactions.
                </div>
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
        var table = $('#packagesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.membership-packages.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'level_badge', name: 'level' },
                { data: 'price_formatted', name: 'price' },
                { data: 'duration_days', name: 'duration_days', 
                  render: function(data) {
                      return data + ' days';
                  }
                },
                { data: 'status_badge', name: 'status' },
                { data: 'transactions_count', name: 'transactions_count' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']]
        });
        
        // Delete Package
        var deleteId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').click(function() {
            $.ajax({
                url: "{{ url('admin/membership-packages') }}/" + deleteId,
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
                        text: error.responseJSON?.message || 'An error occurred while deleting the package.'
                    });
                }
            });
        });
    });
</script>
@endpush