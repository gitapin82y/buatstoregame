<!-- resources/views/admin/withdrawals/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manage Withdrawals')

@section('page-title', 'Manage Withdrawals')

@section('content')
<div class="row mt-4">
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Requests</h6>
                                <h2 class="mb-0">{{ $stats['total'] }}</h2>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Pending</h6>
                                <h2 class="mb-0">{{ $stats['pending'] }}</h2>
                            </div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Approved</h6>
                                <h2 class="mb-0">{{ $stats['approved'] }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Pending Amount</h6>
                                <h2 class="mb-0">Rp {{ number_format($stats['pending_amount'] / 1000, 0, ',', '.') }}K</h2>
                            </div>
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Withdrawals</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select id="filterStatus" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterReseller" class="form-select">
                            <option value="">All Resellers</option>
                            <!-- Resellers will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="dateFrom" class="form-control" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="dateTo" class="form-control" placeholder="To Date">
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by reseller, bank, etc...">
                    </div>
                    <div class="col-md-2">
                        <button id="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">All Withdrawal Requests</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="withdrawalsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reseller</th>
                                <th>Bank Account</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
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

<!-- Approve Withdrawal Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p>You are about to approve the withdrawal request. Please upload proof of payment:</p>
                    
                    <div class="mb-3">
                        <label for="proof_image" class="form-label">Proof of Payment</label>
                        <input type="file" class="form-control" id="proof_image" name="proof_image" required accept="image/*">
                        <small class="text-muted">Upload a screenshot or image of the bank transfer confirmation.</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> By approving this withdrawal, you confirm that you have transferred the funds to the reseller's bank account.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Approve Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Withdrawal Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>You are about to reject the withdrawal request. Please provide a reason:</p>
                    
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Rejecting this withdrawal will return the funds to the reseller's balance.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load resellers for filter dropdown
        $.ajax({
            url: "{{ route('admin.get-resellers') }}",
            type: "GET",
            success: function(data) {
                if (data.success) {
                    var options = '';
                    $.each(data.resellers, function(key, value) {
                        options += '<option value="' + value.id + '">' + value.store_name + '</option>';
                    });
                    $('#filterReseller').append(options);
                }
            }
        });
        
        // Initialize DataTable
        var table = $('#withdrawalsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.withdrawals.index') }}",
                data: function(d) {
                    d.status = $('#filterStatus').val();
                    d.reseller_id = $('#filterReseller').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.search = $('#searchInput').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'reseller', name: 'reseller' },
                { data: 'bank_account', name: 'bank_account' },
                { data: 'amount_formatted', name: 'amount' },
                { data: 'status_badge', name: 'status' },
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[5, 'desc']]
        });
        
        // Apply filters
        $('#filterStatus, #filterReseller, #dateFrom, #dateTo').change(function() {
            table.draw();
        });
        
        // Search functionality
        $('#searchInput').keyup(function() {
            table.draw();
        });
        
        // Reset filters
        $('#resetFilters').click(function() {
            $('#filterStatus, #filterReseller').val('');
            $('#dateFrom, #dateTo').val('');
            $('#searchInput').val('');
            table.draw();
        });
        
        // Approve Withdrawal
        $(document).on('click', '.btn-approve', function() {
            const id = $(this).data('id');
            $('#approveForm').attr('action', "{{ url('admin/withdrawals') }}/" + id + "/approve");
            $('#approveModal').modal('show');
        });
        
        // Reject Withdrawal
        $(document).on('click', '.btn-reject', function() {
            const id = $(this).data('id');
            $('#rejectForm').attr('action', "{{ url('admin/withdrawals') }}/" + id + "/reject");
            $('#rejectModal').modal('show');
        });
    });
</script>
@endpush