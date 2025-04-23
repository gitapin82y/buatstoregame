<!-- resources/views/reseller/membership/index.blade.php -->
@extends('layouts.reseller')

@section('title', 'Membership')

@section('page-title', 'Membership')

@section('content')
<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Membership Status</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="border border-2 border-{{ $membershipInfo['is_active'] ? 'success' : ($membershipInfo['is_grace_period'] ? 'warning' : 'danger') }} rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-{{ $membershipInfo['is_active'] ? 'check' : ($membershipInfo['is_grace_period'] ? 'exclamation' : 'times') }} fa-3x text-{{ $membershipInfo['is_active'] ? 'success' : ($membershipInfo['is_grace_period'] ? 'warning' : 'danger') }}"></i>
                    </div>
                    <h4>{{ ucfirst($membershipInfo['level']) }} Membership</h4>
                    <p class="mb-0">Status: 
                        <span class="badge bg-{{ $membershipInfo['is_active'] ? 'success' : ($membershipInfo['is_grace_period'] ? 'warning' : 'danger') }}">
                            {{ $membershipInfo['is_active'] ? 'Active' : ($membershipInfo['is_grace_period'] ? 'Grace Period' : 'Inactive') }}
                        </span>
                    </p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Membership Level:</strong></td>
                            <td>{{ $membershipInfo['level'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Expires At:</strong></td>
                            <td>{{ $membershipInfo['expires_at']->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Domain:</strong></td>
                            <td>
                                @if($membershipInfo['domain'])
                                    <a href="{{ $membershipInfo['domain'] }}" target="_blank">{{ str_replace(['https://', 'http://'], '', $membershipInfo['domain']) }}</a>
                                @else
                                    Not configured
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Days Remaining:</strong></td>
                            <td>
                                @if($membershipInfo['is_active'])
                                    {{ now()->diffInDays($membershipInfo['expires_at']) }} days
                                @else
                                    <span class="text-danger">Expired</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                @if(!$membershipInfo['is_active'])
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Your membership has expired. Please renew to continue using all features.
                        @if($membershipInfo['is_grace_period'])
                            <p class="mb-0 mt-2"><strong>Grace period:</strong> Your store will be accessible but inactive for transactions until {{ $membershipInfo['expires_at']->addDays(7)->format('d M Y') }}.</p>
                        @endif
                    </div>
                @elseif(now()->diffInDays($membershipInfo['expires_at']) <= 7)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Your membership will expire soon. Please renew to avoid service interruption.
                    </div>
                @endif
            </div>
            <div class="card-footer text-center">
                <a href="#packages" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i> Renew Membership
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Membership Features</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th class="text-center">Silver</th>
                                <th class="text-center">Gold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Subdomain</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Custom Domain</td>
                                <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Games & Services</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Transactions</td>
                                <td class="text-center">Unlimited</td>
                                <td class="text-center">Unlimited</td>
                            </tr>
                            <tr>
                                <td>AI Content Generator</td>
                                <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Social Media Planner</td>
                                <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Brand Image Generator</td>
                                <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Support</td>
                                <td class="text-center">Email</td>
                                <td class="text-center">Priority</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2" id="packages">
    <div class="col-12 mb-4">
        <h3>Available Packages</h3>
    </div>
    
    @foreach($packages as $package)
    <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-{{ $package->level === 'gold' ? 'warning' : 'primary' }} text-white text-center">
                <h4 class="mb-0">{{ $package->name }}</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="mb-1">Rp {{ number_format($package->price, 0, ',', '.') }}</h2>
                    <p class="text-muted">{{ $package->duration_days }} days</p>
                </div>
                
                <div class="mb-4">
                    <p>{{ $package->description }}</p>
                    
                    @if($package->features)
                        <ul class="list-group list-group-flush mb-4">
                            @foreach(json_decode($package->features) as $feature)
                                <li class="list-group-item border-0 ps-0">
                                    <i class="fas fa-check text-success me-2"></i> {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <div class="card-footer border-0 bg-white text-center">
                <a href="{{ route('reseller.membership.checkout', $package->id) }}" class="btn btn-{{ $package->level === 'gold' ? 'warning' : 'primary' }} w-100">
                    <i class="fas fa-shopping-cart me-2"></i> Subscribe Now
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row mt-4">
    <div class="col-12 mb-4">
        <h3>Transaction History</h3>
    </div>
    
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->invoice_number }}</td>
                                    <td>{{ $transaction->package->name }}</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($transaction->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($transaction->payment_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @if($transaction->payment_link)
                                                <a href="{{ $transaction->payment_link }}" class="btn btn-sm btn-warning ms-2" target="_blank">Pay</a>
                                            @endif
                                        @elseif($transaction->payment_status === 'expired')
                                            <span class="badge bg-danger">Expired</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No transaction history available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection