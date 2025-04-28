<!-- resources/views/admin/transactions/index.blade.php -->
@extends('layouts.admin')

@section('title', 'User Transactions')

@section('page-title', 'User Transactions')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Transactions</h1>
            <div>
                <button type="button" class="btn btn-outline-primary" id="exportBtn">
                    <i class="fas fa-file-export me-1"></i> Export Data
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Transactions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select id="filterPaymentStatus" class="form-select">
                            <option value="">All Payment Status</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="expired">Expired</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterProcessStatus" class="form-select">
                            <option value="">All Process Status</option>
                            <option value="completed">Completed</option>
                            <option value="processing">Processing</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterReseller" class="form-select">
                            <option value="">All Resellers</option>
                            <!-- Resellers will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterGame" class="form-select">
                            <option value="">All Games</option>
                            <!-- Games will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="dateFrom" class="form-control" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="dateTo" class="form-control" placeholder="To Date">
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by invoice, customer, etc...">
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
                <h5 class="card-title mb-0">All Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Reseller</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Payment</th>
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

<!-- Process Transaction Modal -->
<div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processModalLabel">Process Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to manually process this transaction?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> This will attempt to deliver the product or service to the customer via the appropriate API or mark it for manual processing.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmProcess">Process Now</button>
            </div>
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
        
        // Load games for filter dropdown
        $.ajax({
            url: "{{ route('admin.get-games') }}",
            type: "GET",
            success: function(data) {
                if (data.success) {
                    var options = '';
                    $.each(data.games, function(key, value) {
                        options += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $('#filterGame').append(options);
                }
            }
        });
        
        // Initialize DataTable
        var table = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.transactions.index') }}",
                data: function(d) {
                    d.payment_status = $('#filterPaymentStatus').val();
                    d.process_status = $('#filterProcessStatus').val();
                    d.reseller_id = $('#filterReseller').val();
                    d.game_id = $('#filterGame').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.search = $('#searchInput').val();
                }
            },
            columns: [
                { data: 'invoice', name: 'invoice' },
                { data: 'customer', name: 'customer' },
                { data: 'reseller', name: 'reseller' },
                { data: 'service', name: 'service' },
                { data: 'amount_formatted', name: 'amount' },
                { data: 'payment_status_badge', name: 'payment_status' },
                { data: 'process_status_badge', name: 'process_status' },
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[7, 'desc']]
        });
        
        // Apply filters
        $('#filterPaymentStatus, #filterProcessStatus, #filterReseller, #filterGame, #dateFrom, #dateTo').change(function() {
            table.draw();
        });
        
        // Search functionality
        $('#searchInput').keyup(function() {
            table.draw();
        });
        
        // Reset filters
        $('#resetFilters').click(function() {
            $('#filterPaymentStatus, #filterProcessStatus, #filterReseller, #filterGame').val('');
            $('#dateFrom, #dateTo').val('');
            $('#searchInput').val('');
            table.draw();
        });
        
        // Export to CSV
        $('#exportBtn').click(function() {
            window.location.href = "{{ route('admin.transactions.export') }}?" + 
                "payment_status=" + $('#filterPaymentStatus').val() + 
                "&process_status=" + $('#filterProcessStatus').val() + 
                "&reseller_id=" + $('#filterReseller').val() + 
                "&game_id=" + $('#filterGame').val() + 
                "&date_from=" + $('#dateFrom').val() + 
                "&date_to=" + $('#dateTo').val() + 
                "&search=" + $('#searchInput').val();
        });
        
        // Process Transaction
        var processId;
        
        $(document).on('click', '.btn-process', function() {
            processId = $(this).data('id');
            $('#processModal').modal('show');
        });
        
        $('#confirmProcess').click(function() {
            $.ajax({
                url: "{{ url('admin/transactions') }}/" + processId + "/process",
                type: 'POST',
                success: function(data) {
                    $('#processModal').modal('hide');
                    table.ajax.reload();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    });
                },
                error: function(error) {
                    $('#processModal').modal('hide');
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.responseJSON?.message || 'An error occurred while processing the transaction.'
                    });
                }
            });
        });
    });
</script>
@endpush