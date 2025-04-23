<!-- resources/views/store/track-result.blade.php -->
@extends('layouts.store')

@section('title', 'Order Status')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary-custom text-white">
                        <h4 class="mb-0">Order Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5>Invoice: {{ $transaction->invoice_number }}</h5>
                            <p class="text-muted">Order Date: {{ $transaction->created_at->format('d M Y H:i') }}</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Product Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Game:</strong> {{ $transaction->game->name }}</li>
                                    <li><strong>Service:</strong> {{ $transaction->service->name }}</li>
                                    <li><strong>Option:</strong> {{ $transaction->option->name }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Payment Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Amount:</strong> Rp {{ number_format($transaction->amount, 0, ',', '.') }}</li>
                                    <li>
                                        <strong>Payment Status:</strong> 
                                        @if($transaction->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($transaction->payment_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @if($transaction->payment_link)
                                                <a href="{{ $transaction->payment_link }}" class="btn btn-sm btn-warning ms-2" target="_blank">Pay Now</a>
                                            @endif
                                        @elseif($transaction->payment_status === 'expired')
                                            <span class="badge bg-danger">Expired</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </li>
                                    <li>
                                        <strong>Order Status:</strong>
                                        @if($transaction->process_status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($transaction->process_status === 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($transaction->process_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Order Progress -->
                        <div class="mb-4">
                            <h6>Order Progress</h6>
                            <div class="progress-track">
                                <ul id="progressbar">
                                    <li class="step0 active" id="step1">Order Created</li>
                                    <li class="step0 {{ in_array($transaction->payment_status, ['paid']) ? 'active' : '' }}" id="step2">Payment Complete</li>
                                    <li class="step0 {{ in_array($transaction->process_status, ['processing', 'completed']) ? 'active' : '' }}" id="step3">Processing</li>
                                    <li class="step0 {{ $transaction->process_status === 'completed' ? 'active' : '' }}" id="step4">Completed</li>
                                </ul>
                            </div>
                        </div>
                        
                        @if($transaction->payment_status === 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Your payment is pending. Please complete your payment to process your order.
                                @if($transaction->payment_link)
                                    <div class="mt-3">
                                        <a href="{{ $transaction->payment_link }}" class="btn btn-warning" target="_blank">
                                            <i class="fas fa-credit-card me-2"></i> Complete Payment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @elseif($transaction->payment_status === 'paid' && $transaction->process_status === 'completed')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Your order has been completed successfully.
                            </div>
                        @elseif($transaction->payment_status === 'paid')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Your payment has been received and your order is being processed.
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <a href="{{ route('store.track', $reseller->subdomain ?? $reseller->custom_domain) }}" class="btn btn-secondary">
                                <i class="fas fa-search me-2"></i> Track Another Order
                            </a>
                            <a href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}" class="btn btn-primary-custom">
                                <i class="fas fa-home me-2"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .progress-track {
        margin-top: 20px;
    }
    
    #progressbar {
        margin-bottom: 30px;
        overflow: hidden;
        color: #455a64;
        padding-left: 0;
        margin-top: 30px;
    }
    
    #progressbar li {
        list-style-type: none;
        font-size: 13px;
        width: 25%;
        float: left;
        position: relative;
        font-weight: 400;
        text-align: center;
    }
    
    #progressbar li:before {
        width: 40px;
        height: 40px;
        line-height: 38px;
        display: block;
        font-size: 18px;
        background: #c5cae9;
        border-radius: 50%;
        margin: 0 auto 10px auto;
        padding: 0;
        color: white;
    }
    
    #progressbar li:nth-child(1):before {
        content: "1";
    }
    
    #progressbar li:nth-child(2):before {
        content: "2";
    }
    
    #progressbar li:nth-child(3):before {
        content: "3";
    }
    
    #progressbar li:nth-child(4):before {
        content: "4";
    }
    
    #progressbar li:after {
        content: '';
        width: 100%;
        height: 2px;
        background: #c5cae9;
        position: absolute;
        left: -50%;
        top: 20px;
        z-index: -1;
    }
    
    #progressbar li:first-child:after {
        content: none;
    }
    
    #progressbar li.active {
        color: var(--primary-color);
    }
    
    #progressbar li.active:before {
        background: var(--primary-color);
    }
    
    #progressbar li.active:after {
        background: var(--primary-color);
    }
</style>
@endpush