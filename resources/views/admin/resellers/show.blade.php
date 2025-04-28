<!-- resources/views/admin/resellers/show.blade.php -->
@extends('layouts.admin')

@section('title', $reseller->name . ' Details')

@section('page-title', $reseller->name . ' Details')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $reseller->name }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">Reseller Details</h1>
            <div>
                <a href="{{ route('admin.resellers.edit', $reseller->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Reseller
                </a>
                @if($profile)
                <a href="{{ $profile->getDomainUrl() }}" target="_blank" class="btn btn-info">
                    <i class="fas fa-external-link-alt me-1"></i> Visit Store
                </a>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Reseller Information</h5>
                <span class="badge {{ $reseller->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($reseller->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($profile && $profile->store_logo)
                        <img src="{{ asset('storage/' . $profile->store_logo) }}" alt="{{ $profile->store_name }}" class="img-fluid rounded-circle mb-3" style="max-width: 120px;">
                    @else
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-4x text-secondary"></i>
                        </div>
                    @endif
                    <h4>{{ $reseller->name }}</h4>
                    <p class="text-muted mb-0">{{ $reseller->email }}</p>
                    @if($reseller->phone_number)
                        <p class="text-muted">{{ $reseller->phone_number }}</p>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>User ID:</strong></span>
                    <span>#{{ $reseller->id }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Role:</strong></span>
                    <span>{{ ucfirst($reseller->role) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Joined:</strong></span>
                    <span>{{ $reseller->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Last Update:</strong></span>
                    <span>{{ $reseller->updated_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
        
        @if($profile)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Store Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Store Name:</strong></span>
                    <span>{{ $profile->store_name }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Subdomain:</strong></span>
                    <span>{{ $profile->subdomain }}.buattokogame.com</span>
                </div>
                @if($profile->custom_domain)
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Custom Domain:</strong></span>
                    <span>{{ $profile->custom_domain }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Membership:</strong></span>
                    <span>{{ ucfirst($profile->membership_level) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Expires:</strong></span>
                    <span>
                        {{ $profile->membership_expires_at ? $profile->membership_expires_at->format('d M Y') : 'N/A' }}
                        @if($profile->isActive())
                            <span class="badge bg-success">Active</span>
                        @elseif($profile->isGracePeriod())
                            <span class="badge bg-warning">Grace</span>
                        @else
                            <span class="badge bg-danger">Expired</span>
                        @endif
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Balance:</strong></span>
                    <span>Rp {{ number_format($profile->balance, 0, ',', '.') }}</span>
                </div>
                
                @if($profile->store_description)
                <hr>
                <div class="mb-0">
                    <strong>Description:</strong>
                    <p class="mt-2 mb-0">{{ $profile->store_description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Social Media</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @if($profile->social_facebook)
                    <a href="https://facebook.com/{{ $profile->social_facebook }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent">
                        <span><i class="fab fa-facebook me-2 text-primary"></i> Facebook</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    
                    @if($profile->social_instagram)
                    <a href="https://instagram.com/{{ $profile->social_instagram }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent">
                        <span><i class="fab fa-instagram me-2 text-danger"></i> Instagram</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    
                    @if($profile->social_twitter)
                    <a href="https://twitter.com/{{ $profile->social_twitter }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent">
                        <span><i class="fab fa-twitter me-2 text-info"></i> Twitter</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    
                    @if($profile->social_tiktok)
                    <a href="https://tiktok.com/@{{ $profile->social_tiktok }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent">
                        <span><i class="fab fa-tiktok me-2"></i> TikTok</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    
                    @if(!$profile->social_facebook && !$profile->social_instagram && !$profile->social_twitter && !$profile->social_tiktok)
                        <div class="list-group-item text-muted">No social media accounts linked</div>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i> No reseller profile found. Please update this user with store information.
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Sales</h6>
                                <h3 class="mb-0">Rp {{ number_format($stats['total_sales'] / 1000, 0, ',', '.') }}K</h3>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Orders</h6>
                                <h3 class="mb-0">{{ $stats['total_transactions'] }}</h3>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Active Games</h6>
                                <h3 class="mb-0">{{ $stats['total_games'] }}</h3>
                            </div>
                            <i class="fas fa-gamepad fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Sales Performance</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="250"></canvas>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Latest Transactions</h5>
                <a href="{{ route('admin.transactions.index') }}?reseller={{ $reseller->id }}" class="btn btn-sm btn-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Game/Service</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $transaction)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.transactions.show', $transaction->id) }}">
                                            {{ $transaction->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $transaction->game->name }} - {{ $transaction->service->name }}</td>
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
                                    <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Membership History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Package</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($membershipHistory as $transaction)
                                        <tr>
                                            <td>{{ $transaction->package->name }}</td>
                                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                            <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No membership history found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Withdrawal Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdrawalHistory as $withdrawal)
                                        <tr>
                                            <td>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($withdrawal->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($withdrawal->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $withdrawal->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No withdrawal history found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Prepare data for chart
        var dates = [];
        var amounts = [];
        var counts = [];
        
        @foreach($transactionChartData as $data)
            dates.push('{{ $data->date }}');
            amounts.push({{ $data->amount }});
            counts.push({{ $data->count }});
        @endforeach
        
        // Create chart
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Sales Amount (Rp)',
                        data: amounts,
                        borderColor: 'rgba(0, 123, 255, 1)',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        yAxisID: 'y-axis-1',
                    },
                    {
                        label: 'Order Count',
                        data: counts,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        yAxisID: 'y-axis-2',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    'y-axis-1': {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sales Amount (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            }
                        }
                    },
                    'y-axis-2': {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Order Count'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    });
</script>
@endpush