<!-- resources/views/user/transactions/show.blade.php -->
@extends('layouts.user')

@section('title', 'Transaction Details')

@section('page-title', 'Transaction Details')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.transactions.index') }}">Transactions</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $transaction->invoice_number }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaction Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Invoice Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Invoice Number:</strong></td>
                                <td>{{ $transaction->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Store:</strong></td>
                                <td>{{ $transaction->reseller->store_name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Payment Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Amount:</strong></td>
                                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
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
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Order Status:</strong></td>
                                <td>
                                    @if($transaction->process_status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($transaction->process_status === 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @elseif($transaction->process_status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Product Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Game:</strong></td>
                                <td>{{ $transaction->game->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Service:</strong></td>
                                <td>{{ $transaction->service->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Option:</strong></td>
                                <td>{{ $transaction->option->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Account Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>User ID:</strong></td>
                                <td>{{ $transaction->user_identifier }}</td>
                            </tr>
                            @if($transaction->server_identifier)
                                <tr>
                                    <td><strong>Server ID:</strong></td>
                                    <td>{{ $transaction->server_identifier }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($transaction->payment_status === 'paid')
                    <div class="alert alert-success mt-4">
                        <i class="fas fa-check-circle me-2"></i>
                        @if($transaction->process_status === 'completed')
                            Your order has been completed successfully.
                        @elseif($transaction->process_status === 'processing')
                            Your order is being processed and will be completed shortly.
                        @else
                            Your payment has been received and your order will be processed soon.
                        @endif
                    </div>
                @elseif($transaction->payment_status === 'pending')
                    <div class="alert alert-warning mt-4">
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
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Order Created</h6>
                            <small class="text-muted">{{ $transaction->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $transaction->payment_status === 'paid' ? 'bg-success' : ($transaction->payment_status === 'pending' ? 'bg-warning' : 'bg-danger') }}"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Payment 
                                @if($transaction->payment_status === 'paid')
                                    Completed
                                @elseif($transaction->payment_status === 'pending')
                                    Pending
                                @elseif($transaction->payment_status === 'expired')
                                    Expired
                                @else
                                    Failed
                                @endif
                            </h6>
                            @if($transaction->paid_at)
                                <small class="text-muted">{{ $transaction->paid_at->format('d M Y H:i') }}</small>
                            @endif
                        </div>
                    </div>
                    
                    @if($transaction->payment_status === 'paid')
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $transaction->process_status === 'completed' || $transaction->process_status === 'processing' ? 'bg-success' : 'bg-warning' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Processing</h6>
                                <small class="text-muted">Order is being processed</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $transaction->process_status === 'completed' ? 'bg-success' : 'bg-light' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Completion</h6>
                                @if($transaction->completed_at)
                                    <small class="text-muted">{{ $transaction->completed_at->format('d M Y H:i') }}</small>
                                @elseif($transaction->process_status === 'completed')
                                    <small class="text-muted">Order completed</small>
                                @else
                                    <small class="text-muted">Waiting for completion</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Need Help?</h5>
            </div>
            <div class="card-body">
                <p>If you have any questions or issues with your order, please contact the store or our support team.</p>
                <div class="d-grid gap-2">
                    <a href="mailto:{{ $transaction->reseller->user->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i> Contact Store
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-headset me-2"></i> Support Center
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 1.5rem;
        bottom: 1.5rem;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-marker {
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        left: -0.45rem;
        top: 0.2rem;
    }
    
    .timeline-content {
        padding-left: 1rem;
    }
</style>
@endpush