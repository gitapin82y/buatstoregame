<!-- resources/views/reseller/dashboard.blade.php -->
@extends('layouts.reseller')

@section('title', 'Reseller Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div class="row mt-4">
    <div class="col-md-6">
        <h4>Welcome, {{ Auth::user()->name }}</h4>
        <p>Manage your game store and monitor your performance.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <div class="d-flex justify-content-md-end align-items-center">
            <span class="me-2 text-muted">Your Store:</span>
            <a href="{{ $reseller->getDomainUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i> {{ $reseller->subdomain }}.buattokogame.com
            </a>
        </div>
        <div class="mt-2">
            <span class="badge {{ $membershipInfo['is_active'] ? 'bg-success' : ($membershipInfo['is_grace_period'] ? 'bg-warning' : 'bg-danger') }}">
                {{ $membershipInfo['is_active'] ? 'Active' : ($membershipInfo['is_grace_period'] ? 'Grace Period' : 'Inactive') }}
            </span>
            <span class="text-muted ms-2">{{ ucfirst($membershipInfo['level']) }} Membership | Expires: {{ $membershipInfo['expires_at']->format('d M Y') }}</span>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Balance</h6>
                        <h2 class="fs-1">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</h2>
                    </div>
                    <i class="fas fa-wallet fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="{{ route('reseller.withdrawals.index') }}" class="text-white text-decoration-none">Withdraw</a>
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
                        <h6 class="fs-5 fw-bold">Total Sales</h6>
                        <h2 class="fs-1">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</h2>
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
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-5 fw-bold">Transactions</h6>
                        <h2 class="fs-1">{{ $stats['transactions_count'] }}</h2>
                    </div>
                    <i class="fas fa-shopping-cart fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="{{ route('reseller.transactions.index') }}" class="text-white text-decoration-none">View Details</a>
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
                        <h6 class="fs-5 fw-bold">Active Games</h6>
                        <h2 class="fs-1">{{ $stats['active_games'] }}</h2>
                    </div>
                    <i class="fas fa-gamepad fs-1"></i>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="{{ route('reseller.games.index') }}" class="text-white text-decoration-none">Manage Games</a>
                <span class="ms-auto">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <!-- Lanjutan resources/views/reseller/dashboard.blade.php -->
                <h5 class="card-title">Sales Performance (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Top Games</h5>
            </div>
            <div class="card-body">
                @if($gameStats->isEmpty())
                    <p class="text-center">No game sales data available yet.</p>
                @else
                    @foreach($gameStats as $stat)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                @if($stat->game->logo)
                                    <img src="{{ asset('storage/' . $stat->game->logo) }}" alt="{{ $stat->game->name }}" width="40" class="me-2">
                                @else
                                    <div class="bg-light rounded-circle p-2 me-2">
                                        <i class="fas fa-gamepad"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $stat->game->name }}</h6>
                                    <small class="text-muted">{{ $stat->count }} transactions</small>
                                </div>
                            </div>
                            <span class="badge bg-primary">Rp {{ number_format($stat->amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Transactions</h5>
                <a href="{{ route('reseller.transactions.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Game</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $transaction)
                                <tr>
                                    <td><a href="{{ route('reseller.transactions.show', $transaction->id) }}">{{ $transaction->invoice_number }}</a></td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->game->name }}</td>
                                    <td>{{ $transaction->service->name }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$membershipInfo['is_active'])
<div class="row mt-2">
    <div class="col-md-12">
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-circle me-2"></i> Your membership has expired!</h5>
            <p>Your store is currently {{ $membershipInfo['is_grace_period'] ? 'in grace period' : 'inactive' }}. Please renew your membership to continue using all features.</p>
            <a href="{{ route('reseller.membership.index') }}" class="btn btn-primary mt-2">Renew Membership</a>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Transaction Chart
    var salesCtx = document.getElementById('salesChart').getContext('2d');
    var salesData = {
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
    
    var salesChart = new Chart(salesCtx, {
        type: 'line',
        data: salesData,
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
</script>
@endpush