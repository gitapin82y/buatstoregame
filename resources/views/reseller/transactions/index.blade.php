<!-- resources/views/reseller/transactions/index.blade.php -->
@extends('layouts.reseller')

@section('title', 'Transactions')

@section('page-title', 'Transactions')

@section('content')
<div class="row mt-4">
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Orders</h6>
                                <h2 class="mb-0">{{ $stats['total'] }}</h2>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Completed Orders</h6>
                                <h2 class="mb-0">{{ $stats['completed'] }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Pending Orders</h6>
                                <h2 class="mb-0">{{ $stats['pending'] }}</h2>
                            </div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Sales</h6>
                                <h2 class="mb-0">Rp {{ number_format($stats['total_sales'] / 1000, 0, ',', '.') }}K</h2>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
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
                                <option value="">All Process Status</option>
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
                                <button id="exportBtn" class="btn btn-outline-primary">
                                    <i class="fas fa-file-export me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Game/Service</th>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reseller.transactions.index') }}",
                data: function(d) {
                    d.payment_status = $('#filterPaymentStatus').val();
                    d.process_status = $('#filterProcessStatus').val();
                    d.search = $('#searchInput').val();
                }
            },
            columns: [
                { data: 'invoice', name: 'invoice' },
                { data: 'customer', name: 'customer' },
                { data: 'service', name: 'service' },
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
        
        // Export to CSV
        $('#exportBtn').click(function() {
            window.location.href = "{{ route('reseller.transactions.export') }}?" + 
                "payment_status=" + $('#filterPaymentStatus').val() + 
                "&process_status=" + $('#filterProcessStatus').val() + 
                "&search=" + $('#searchInput').val();
        });
    });
</script>
@endpush