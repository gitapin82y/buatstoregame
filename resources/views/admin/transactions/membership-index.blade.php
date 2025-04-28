<!-- resources/views/admin/transactions/membership-index.blade.php -->
@extends('layouts.admin')

@section('title', 'Membership Transactions')

@section('page-title', 'Membership Transactions')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Membership Transactions</h1>
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
                        <select id="filterPackage" class="form-select">
                            <option value="">All Packages</option>
                            <!-- Packages will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterMembershipLevel" class="form-select">
                            <option value="">All Levels</option>
                            <option value="silver">Silver</option>
                            <option value="gold">Gold</option>
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by invoice, reseller, etc...">
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
                <h5 class="card-title mb-0">All Membership Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Reseller</th>
                                <th>Package</th>
                                <th>Level</th>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load packages for filter dropdown
        $.ajax({
            url: "{{ route('admin.get-membership-packages') }}",
            type: "GET",
            success: function(data) {
                if (data.success) {
                    var options = '';
                    $.each(data.packages, function(key, value) {
                        options += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $('#filterPackage').append(options);
                }
            }
        });
        
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
        var table = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.transactions.membership.index') }}",
                data: function(d) {
                    d.payment_status = $('#filterPaymentStatus').val();
                    d.package_id = $('#filterPackage').val();
                    d.membership_level = $('#filterMembershipLevel').val();
                    d.reseller_id = $('#filterReseller').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.search = $('#searchInput').val();
                }
            },
            columns: [
                { data: 'invoice', name: 'invoice' },
                { data: 'reseller', name: 'reseller' },
                { data: 'package', name: 'package' },
                { data: 'level_badge', name: 'level' },
                { data: 'amount_formatted', name: 'amount' },
                { data: 'payment_status_badge', name: 'payment_status' },
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[6, 'desc']]
        });
        
        // Apply filters
        $('#filterPaymentStatus, #filterPackage, #filterMembershipLevel, #filterReseller, #dateFrom, #dateTo').change(function() {
            table.draw();
        });
        
        // Search functionality
        $('#searchInput').keyup(function() {
            table.draw();
        });
        
        // Reset filters
        $('#resetFilters').click(function() {
            $('#filterPaymentStatus, #filterPackage, #filterMembershipLevel, #filterReseller').val('');
            $('#dateFrom, #dateTo').val('');
            $('#searchInput').val('');
            table.draw();
        });
        
        // Export to CSV
        $('#exportBtn').click(function() {
            window.location.href = "{{ route('admin.transactions.membership.export') }}?" + 
                "payment_status=" + $('#filterPaymentStatus').val() + 
                "&package_id=" + $('#filterPackage').val() + 
                "&membership_level=" + $('#filterMembershipLevel').val() + 
                "&reseller_id=" + $('#filterReseller').val() + 
                "&date_from=" + $('#dateFrom').val() + 
                "&date_to=" + $('#dateTo').val() + 
                "&search=" + $('#searchInput').val();
        });
    });
</script>
@endpush