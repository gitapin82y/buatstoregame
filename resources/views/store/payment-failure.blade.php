<!-- resources/views/store/payment-failure.blade.php -->
@extends('layouts.store')

@section('title', 'Payment Failed')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <div class="bg-danger text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-times-circle fa-4x"></i>
                            </div>
                            <h1 class="mt-4">Payment Failed</h1>
                            <p class="lead">We couldn't process your payment.</p>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Order Details</h5>
                                <div class="row mb-3 mt-4">
                                    <div class="col-md-6 text-md-start">
                                        <p class="mb-1"><strong>Invoice Number:</strong></p>
                                        <p class="mb-1"><strong>Date:</strong></p>
                                        <p class="mb-1"><strong>Game:</strong></p>
                                        <p class="mb-1"><strong>Service:</strong></p>
                                        <p class="mb-1"><strong>Amount:</strong></p>
                                        <p class="mb-1"><strong>Status:</strong></p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <p class="mb-1">{{ $transaction->invoice_number }}</p>
                                        <p class="mb-1">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                                        <p class="mb-1">{{ $transaction->game->name }}</p>
                                        <p class="mb-1">{{ $transaction->service->name }} ({{ $transaction->option->name }})</p>
                                        <p class="mb-1">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                        <p class="mb-1">
                                            <span class="badge bg-danger">Failed</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i> There was an issue with your payment. This might be due to insufficient funds, incorrect payment details, or a connection problem during the transaction process.
                        </div>
                        
                        <div class="d-grid gap-3">
                            @if($transaction->payment_link && $transaction->expired_at && $transaction->expired_at->isFuture())
                                <a href="{{ $transaction->payment_link }}" class="btn btn-primary-custom">
                                    <i class="fas fa-redo me-2"></i> Try Payment Again
                                </a>
                            @endif
                            <a href="{{ route('store.service', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $transaction->game->slug, 'serviceSlug' => $transaction->service->slug]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i> Place New Order
                            </a>
                            <a href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i> Back to Store
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection