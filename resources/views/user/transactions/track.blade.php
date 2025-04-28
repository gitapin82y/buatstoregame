<!-- resources/views/user/transactions/track.blade.php -->
@extends('layouts.user')

@section('title', 'Order Status')

@section('page-title', 'Order Status')

@section('content')
<div class="row mt-4">
    <div class="col-md-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Order Status for {{ $transaction->invoice_number }}</h5>
                <span class="badge bg-{{ $transaction->payment_status === 'paid' ? 'success' : ($transaction->payment_status === 'pending' ? 'warning' : 'danger') }}">
                    {{ ucfirst($transaction->payment_status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Order Information</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Invoice:</span>
                                    <span>{{ $transaction->invoice_number }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Order Date:</span>
                                    <span>{{ $transaction->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Store:</span>
                                    <span>{{ $transaction->reseller->store_name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Amount:</span>
                                    <span>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Product Information</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Game:</span>
                                    <span>{{ $transaction->game->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Service:</span>
                                    <span>{{ $transaction->service->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Option:</span>
                                    <span>{{ $transaction->option->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>User ID:</span>
                                    <span>{{ $transaction->user_identifier }}</span>
                                </div>
                                @if($transaction->server_identifier)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Server ID:</span>
                                    <span>{{ $transaction->server_identifier }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Order Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="order-progress">
                            <div class="progress-track">
                                <ul id="progressbar">
                                    <li class="step0 active" id="step1">
                                        <div class="icon-container">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <span class="step-title">Order Placed</span>
                                        <span class="step-date">{{ $transaction->created_at->format('d M Y') }}</span>
                                    </li>
                                    <li class="step0 {{ in_array($transaction->payment_status, ['paid']) ? 'active' : '' }}" id="step2">
                                        <div class="icon-container">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <span class="step-title">Payment Complete</span>
                                        <span class="step-date">
                                            @if($transaction->paid_at)
                                                {{ $transaction->paid_at->format('d M Y') }}
                                            @elseif($transaction->payment_status === 'pending')
                                                Waiting for payment
                                            @else
                                                Payment failed
                                            @endif
                                        </span>
                                    </li>
                                    <li class="step0 {{ in_array($transaction->process_status, ['processing', 'completed']) ? 'active' : '' }}" id="step3">
                                        <div class="icon-container">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                        <span class="step-title">Processing</span>
                                        <span class="step-date">
                                            @if(in_array($transaction->process_status, ['processing', 'completed']))
                                                In progress
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </li>
                                    <li class="step0 {{ $transaction->process_status === 'completed' ? 'active' : '' }}" id="step4">
                                        <div class="icon-container">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <span class="step-title">Completed</span>
                                        <span class="step-date">
                                            @if($transaction->completed_at)
                                                {{ $transaction->completed_at->format('d M Y') }}
                                            @elseif($transaction->process_status === 'completed')
                                                Delivered
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($transaction->payment_status === 'pending')
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading">Payment Pending</h5>
                                <p class="mb-0">Your payment is still pending. Please complete your payment to process your order.</p>
                                @if($transaction->payment_link && $transaction->expired_at && $transaction->expired_at->isFuture())
                                    <div class="mt-3">
                                        <a href="{{ $transaction->payment_link }}" class="btn btn-warning" target="_blank">
                                            <i class="fas fa-credit-card me-2"></i> Complete Payment
                                        </a>
                                        <small class="d-block mt-2">Payment link expires: {{ $transaction->expired_at->format('d M Y H:i') }}</small>
                                    </div>
                                @elseif($transaction->expired_at && $transaction->expired_at->isPast())
                                    <small class="d-block mt-2">Payment link has expired. Please contact the store for assistance.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($transaction->payment_status === 'paid' && $transaction->process_status === 'completed')
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading">Order Completed</h5>
                                <p class="mb-0">Your order has been successfully completed and delivered. Thank you for your purchase!</p>
                            </div>
                        </div>
                    </div>
                @elseif($transaction->payment_status === 'paid')
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading">Order in Progress</h5>
                                <p class="mb-0">Your payment has been received and your order is being processed. Please allow some time for delivery.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('user.transactions.track.form') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-search me-1"></i> Track Another Order
                    </a>
                    <a href="{{ route('user.transactions.index') }}" class="btn btn-primary">
                        <i class="fas fa-history me-1"></i> View All Transactions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .order-progress {
        margin-top: 20px;
    }
    
    .progress-track {
        margin: 0 auto;
        position: relative;
        padding: 0 10px;
    }
    
    #progressbar {
        margin-bottom: 30px;
        overflow: hidden;
        color: #455a64;
        padding-left: 0;
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
    }
    
    #progressbar li {
        list-style-type: none;
        font-size: 13px;
        width: 25%;
        position: relative;
        text-align: center;
    }
    
    #progressbar li:before {
        content: "";
        width: 100%;
        height: 4px;
        background: #c5cae9;
        position: absolute;
        left: -50%;
        top: 20px;
        z-index: 1;
    }
    
    #progressbar li:first-child:before {
        content: none;
    }
    
    #progressbar li .icon-container {
        width: 40px;
        height: 40px;
        line-height: 40px;
        display: block;
        font-size: 16px;
        background: #c5cae9;
        border-radius: 50%;
        margin: 0 auto 10px auto;
        padding: 0;
        color: white;
        position: relative;
        z-index: 2;
    }
    
    #progressbar li .step-title {
        display: block;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    #progressbar li .step-date {
        display: block;
        font-size: 12px;
        color: #757575;
    }
    
    #progressbar li.active .icon-container {
        background: #007bff;
    }
    
    #progressbar li.active {
        color: #007bff;
    }
    
    #progressbar li.active:before {
        background: #007bff;
    }
</style>
@endpush