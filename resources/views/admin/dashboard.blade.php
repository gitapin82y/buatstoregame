<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row my-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Total Users</h6>
                        <h2 class="fs-1">{{ $stats['total_users'] }}</h2>
                    </div>
                    <i class="fas fa-users fs-1"></i>
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
                        <h6 class="fs-5 fw-bold">Active Resellers</h6>
                        <h2 class="fs-1">{{ $stats['active_resellers'] }}</h2>
                    </div>
                    <i class="fas fa-store fs-1"></i>
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
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Today's Sales</h6>
                        <h2 class="fs-1">{{ number_format($stats['sales_today']) }}</h2>
                    </div>
                    <i class="fas fa-dollar-sign fs-1"></i>
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
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Pending Withdrawals</h6>
                        <h2 class="fs-1">{{ $stats['pending_withdrawals'] }}</h2>
                    </div>
                    <i class="fas fa-money-bill-wave fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="{{ route('admin.withdrawals.index') }}" class="text-white text-decoration-none">View Details</a>
                <span class="ms-auto">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Transaction Overview (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="transactionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">New Resellers (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="resellerChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Latest Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>User</th>
                                <th>Game</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestTransactions as $transaction)
                            <tr>
                                <td><a href="{{ route('admin.transactions.show', $transaction->id) }}">{{ $transaction->invoice_number }}</a></td>
                                <td>{{ $transaction->user->name }}</td>
                                <td>{{ $transaction->game->name }}</td>
                                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($transaction->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($transaction->payment_status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($transaction->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-primary btn-sm">View All Transactions</a>
            </div>
        </div>
    </div>
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Expiring Memberships</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($expiringResellers as $profile)
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $profile->store_name }}</h6>
                            <small>{{ $profile->membership_expires_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $profile->user->name }} ({{ $profile->user->email }})</p>
                        <small>{{ ucfirst($profile->membership_level) }} membership expires on {{ $profile->membership_expires_at->format('d M Y') }}</small>
                    </div>
                    @endforeach

                    @if($expiringResellers->isEmpty())
                    <div class="list-group-item">
                        <p class="mb-0">No expiring memberships in the next 7 days.</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.resellers.index') }}" class="btn btn-primary btn-sm">View All Resellers</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Transaction Chart
    var transactionCtx = document.getElementById('transactionChart').getContext('2d');
    var transactionData = {
        labels: [
            @foreach($transactionChart as $data)
                '{{ \Carbon\Carbon::parse($data->date)->format('d M') }}',
            @endforeach
        ],
        datasets: [
            {
                label: 'Transactions',
                data: [
                    @foreach($transactionChart as $data)
                        {{ $data->count }},
                    @endforeach
                ],
                borderColor: '#3490dc',
                backgroundColor: 'rgba(52, 144, 220, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y',
            },
            {
                label: 'Sales (Rp)',
                data: [
                    @foreach($transactionChart as $data)
                        {{ $data->amount }},
                    @endforeach
                ],
                borderColor: '#38c172',
                backgroundColor: 'rgba(56, 193, 114, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y1',
            }
        ]
    };
    
    var transactionChart = new Chart(transactionCtx, {
        type: 'line',
        data: transactionData,
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Transactions'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Sales (Rp)'
                    }
                }
            }
        }
    });

    // Reseller Chart
    var resellerCtx = document.getElementById('resellerChart').getContext('2d');
    var resellerData = {
        labels: [
            @foreach($resellerChart as $data)
                '{{ \Carbon\Carbon::createFromDate($data->year, $data->month, 1)->format('M Y') }}',
            @endforeach
        ],
        datasets: [
            {
                label: 'New Resellers',
                data: [
                    @foreach($resellerChart as $data)
                        {{ $data->count }},
                    @endforeach
                ],
                backgroundColor: '#8e44ad',
                borderWidth: 0,
                borderRadius: 4,
            }
        ]
    };
    
    var resellerChart = new Chart(resellerCtx, {
        type: 'bar',
        data: resellerData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush

