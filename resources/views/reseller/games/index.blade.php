<!-- resources/views/reseller/games/index.blade.php -->
@extends('layouts.reseller')

@section('title', 'Manage Games')

@section('page-title', 'Manage Games')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Available Games</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> 
                    Add games to your store by clicking "Add to Store" button. You can then manage pricing and services for each game.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="gamesTable">
                        <thead>
                            <tr>
                                <th width="60">Logo</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Profit Margin</th>
                                <th width="180">Actions</th>
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
                <h5 class="modal-title" id="deleteModalLabel">Confirm Remove</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this game from your store? All related services and configurations will be deleted.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Remove</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#gamesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reseller.games.index') }}",
            columns: [
                { data: 'logo', name: 'logo', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'status_badge', name: 'status' },
                { data: 'profit_margin', name: 'profit_margin' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
        
        // Delete Game
        var deleteId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').click(function() {
            $.ajax({
                url: "{{ url('reseller/games') }}/" + deleteId,
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
                        text: 'An error occurred while removing the game.'
                    });
                }
            });
        });
    });
</script>
@endpush