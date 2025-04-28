<!-- resources/views/reseller/membership/checkout.blade.php -->
@extends('layouts.reseller')

@section('title', 'Membership Checkout')

@section('page-title', 'Membership Checkout')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.membership.index') }}">Membership</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Membership Purchase</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <div class="bg-{{ $package->level === 'gold' ? 'warning' : 'primary' }} text-white rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-crown fa-3x"></i>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h3>{{ $package->name }}</h3>
                        <h5 class="text-muted">{{ ucfirst($package->level) }} Membership</h5>
                        <p>{{ $package->description }}</p>
                        
                        <div class="d-flex align-items-center mt-3">
                            <h4 class="mb-0 me-3">Rp {{ number_format($package->price, 0, ',', '.') }}</h4>
                            <span class="badge bg-secondary">{{ $package->duration_days }} days</span>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-4">
                    <h5>Package Features</h5>
                    <ul class="list-group list-group-flush">
                        @if($package->features)
                            @foreach(json_decode($package->features) as $feature)
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-check-circle text-success me-2"></i> {{ $feature }}
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-check-circle text-success me-2"></i> Access to all basic features
                            </li>
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-check-circle text-success me-2"></i> Unlimited transactions
                            </li>
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-check-circle text-success me-2"></i> 24/7 customer support
                            </li>
                            @if($package->level === 'gold')
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-check-circle text-success me-2"></i> Custom domain support
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-check-circle text-success me-2"></i> AI content generation tools
                                </li>
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-check-circle text-success me-2"></i> Priority support
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
                
                <form action="{{ route('reseller.membership.purchase', $package->id) }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Your membership will be activated immediately after successful payment. If you currently have an active membership, the duration will be added to your existing membership.
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i> Proceed to Payment
                        </button>
                        <a href="{{ route('reseller.membership.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Membership Plans
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Package:</span>
                    <span>{{ $package->name }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Duration:</span>
                    <span>{{ $package->duration_days }} days</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Level:</span>
                    <span>{{ ucfirst($package->level) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2 fw-bold">
                    <span>Total:</span>
                    <span>Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Membership</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Current Level:</span>
                    <span>{{ ucfirst($reseller->membership_level) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status:</span>
                    <span>
                        @if($reseller->isActive())
                            <span class="badge bg-success">Active</span>
                        @elseif($reseller->isGracePeriod())
                            <span class="badge bg-warning">Grace Period</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Expires At:</span>
                    <span>{{ $reseller->membership_expires_at->format('d M Y') }}</span>
                </div>
                <hr>
                <div class="mb-2">
                    <span>After purchase:</span>
                    <span class="d-block mt-1">
                        @if($reseller->isActive() || $reseller->isGracePeriod())
                            Your membership will be extended to {{ $reseller->membership_expires_at->addDays($package->duration_days)->format('d M Y') }}
                        @else
                            Your membership will be active until {{ now()->addDays($package->duration_days)->format('d M Y') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection