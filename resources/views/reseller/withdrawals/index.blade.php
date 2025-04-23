<!-- resources/views/reseller/withdrawals/index.blade.php -->
@extends('layouts.reseller')

@section('title', 'Withdrawals')

@section('page-title', 'Withdrawals')

@section('content')
<div class="row mt-4">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Balance</h5>
            </div>
            <div class="card-body text-center">
                <h2 class="display-4 mb-3">Rp {{ number_format($balance, 0, ',', '.') }}</h2>
                <p class="text-muted">Available for withdrawal</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="fas fa-money-bill-wave me-2"></i> Withdraw Funds
                </button>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h5>{{ $stats['total_withdrawals'] }}</h5>
                        <small class="text-muted">Total Withdrawals</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h5>{{ $stats['approved_withdrawals'] }}</h5>
                        <small class="text-muted">Approved</small>
                    </div>
                    <div class="col-6">
                        <h5>{{ $stats['pending_withdrawals'] }}</h5>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-6">
                        <h5>Rp {{ number_format($stats['total_withdrawn'], 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Withdrawn</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Withdrawal History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="withdrawalsTable">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Bank Account</th>
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

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel">Withdraw Funds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reseller.withdrawals.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="amount" name="amount" min="50000" max="{{ $balance }}" required>
                        </div>
                        <small class="text-muted">Minimum withdrawal: Rp 50,000</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <select class="form-select" id="bank_name" name="bank_name" required>
                            <option value="">Select Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="CIMB Niaga">CIMB Niaga</option>
                            <option value="Permata">Permata</option>
                            <option value="Danamon">Danamon</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_number" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Holder Name</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Withdrawal requests are processed within 1-2 business days.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Proof Modal -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofModalLabel">Payment Proof</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="proofDetails" class="mb-3">
                    <h5 id="proofAmount"></h5>
                    <p id="proofDate" class="text-muted"></p>
                </div>
                <img id="proofImage" src="" alt="Payment Proof" class="img-fluid border rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reasonModalLabel">Rejection Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="rejectionDetails" class="mb-3">
                    <h5 id="rejectionAmount"></h5>
                    <p id="rejectionDate" class="text-muted"></p>
                </div>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="rejectionReason"></span>
                </div>
                <p>The withdrawal amount has been refunded to your balance.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#withdrawalsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reseller.withdrawals.index') }}",
            columns: [
                { data: 'amount_formatted', name: 'amount' },
                { data: 'bank_account', name: 'bank_name' },
                { data: 'status_badge', name: 'status' },
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[3, 'desc']]
        });
        
        // Validate withdrawal amount
        $('#amount').on('input', function() {
            const amount = $(this).val();
            const balance = {{ $balance }};
            const minAmount = 50000;
            
            if (amount > balance) {
                $(this).val(balance);
            } else if (amount < minAmount && amount !== '') {
                $(this).val(minAmount);
            }
        });
        
        // View Proof
        $(document).on('click', '.btn-proof', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: "{{ url('reseller/withdrawals') }}/" + id + "/proof",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#proofAmount').text(response.amount);
                        $('#proofDate').text('Approved on ' + response.date);
                        $('#proofImage').attr('src', response.proof_url);
                        $('#proofModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching the proof image.'
                    });
                }
            });
        });
        
        // View Rejection Reason
        $(document).on('click', '.btn-reason', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: "{{ url('reseller/withdrawals') }}/" + id + "/reason",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#rejectionAmount').text(response.amount);
                        $('#rejectionDate').text('Rejected on ' + response.date);
                        $('#rejectionReason').text(response.reason);
                        $('#reasonModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching the rejection reason.'
                    });
                }
            });
        });
    });
</script>
@endpush