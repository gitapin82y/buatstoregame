<!-- resources/views/user/transactions/index.blade.php -->
@extends('layouts.user')

@section('title', 'Transaction History')

@section('page-title', 'Transaction History')

@section('content')
<div class="row mt-4">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">My Transactions</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row g-2">
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
                                <option value="">All Order Status</option>
                                <option value="completed">Completed</option>
                                <option value="processing">Processing</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('user.transactions.track.form') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-1"></i> Track Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Game/Service</th>
                                <th>Store</th>
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
    
    <div class="col-md-12">
        <div class="alert alert-info">
            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Need to find a specific order?</h5>
            <p>If you're looking for a specific transaction or need to check the status of an order from a store where you weren't logged in, you can use our <a href="{{ route('user.transactions.track.form') }}" class="alert-link">Order Tracking Tool</a>.</p>
            <p class="mb-0">Simply enter your invoice number to get detailed information about your order.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('user.transactions.index') }}",
                data: function(d) {
                    d.payment_status = $('#filterPaymentStatus').val();
                    d.process_status = $('#filterProcessStatus').val();
                    d.search = $('#searchInput').val();
                }
            },
            columns: [
                { data: 'invoice', name: 'invoice' },
                { data: 'product', name: 'product' },
                { data: 'store', name: 'store' },
                { data: 'amount_formatted', name: 'amount' },
                { data: 'payment_status_badge', name: 'payment_status' },
                { data: 'process_status_badge', name: 'process_status' },
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[6, 'desc']]
        });
        
        // Apply filters
        $('#filterPaymentStatus, #filterProcessStatus').change(function() {
            table.draw();
        });
        
        // Search functionality
        $('#searchInput').keyup(function() {
            table.draw();
        });
    });
</script>
@endpush