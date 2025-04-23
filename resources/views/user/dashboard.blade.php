
<!-- resources/views/user/dashboard.blade.php -->
@extends('layouts.user')

@section('title', 'User Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <h4>Welcome, {{ Auth::user()->name }}</h4>
        <p>Manage your account and monitor your transaction history.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Total Transactions</h6>
                        <h2 class="fs-1">{{ $stats['total_transactions'] }}</h2>
                    </div>
                    <i class="fas fa-shopping-cart fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="{{ route('user.transactions.index') }}" class="text-white text-decoration-none">View History</a>
                <span class="ms-auto">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Completed Orders</h6>
                        <h2 class="fs-1">{{ $stats['completed_transactions'] }}</h2>
                    </div>
                    <i class="fas fa-check-circle fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                View Details
                <span class="ms-auto">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Pending Orders</h6>
                        <h2 class="fs-1">{{ $stats['pending_transactions'] }}</h2>
                    </div>
                    <i class="fas fa-clock fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                View Details
                <span class="ms-auto">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Total Spent</h6>
                        <h2 class="fs-1">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</h2>
                    </div>
                    <i class="fas fa-wallet fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                View Details
                <span class="ms-auto">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Transactions</h5>
                <a href="{{ route('user.transactions.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Game/Service</th>
                                <th>Store</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $transaction)
                                <tr>
                                    <td><a href="{{ route('user.transactions.show', $transaction->id) }}">{{ $transaction->invoice_number }}</a></td>
                                    <td>{{ $transaction->game->name }} - {{ $transaction->service->name }}</td>
                                    <td>{{ $transaction->reseller->store_name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($transaction->process_status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($transaction->process_status === 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($transaction->process_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($transaction->process_status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('user.transactions.track.form') }}" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i> Track Order
                    </a>
                    <a href="{{ route('user.profile.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user-edit me-2"></i> Update Profile
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-success">
                        <i class="fas fa-shopping-cart me-2"></i> Shop Now
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Need Help?</h5>
            </div>
            <div class="card-body">
                <p>If you need assistance with your orders or have questions about our services, please contact support.</p>
                <a href="#" class="btn btn-primary w-100">
                    <i class="fas fa-headset me-2"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection