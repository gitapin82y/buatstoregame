<!-- resources/views/admin/transactions/membership-show.blade.php -->
@extends('layouts.admin')

@section('title', 'Membership Transaction Details')

@section('page-title', 'Membership Transaction Details')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.transactions.membership.index') }}">Membership Transactions</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $transaction->invoice_number }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Transaction Information</h5>
                <span class="badge bg-{{ $transaction->payment_status === 'paid' ? 'success' : ($transaction->payment_status === 'pending' ? 'warning' : 'danger') }}">
                    {{ ucfirst($transaction->payment_status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Invoice Details</h6>
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
                                <td><strong>Amount:</strong></td>
                                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method:</strong></td>
                                <td>{{ $transaction->payment_method ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
                                    @if($transaction->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                        @if($transaction->paid_at)
                                            <small class="text-muted ms-1">{{ $transaction->paid_at->format('d M Y H:i') }}</small>
                                        @endif
                                    @elseif($transaction->payment_status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @if($transaction->expired_at && $transaction->expired_at->isFuture())
                                            <small class="text-muted ms-1">Expires: {{ $transaction->expired_at->format('d M Y H:i') }}</small>
                                        @endif
                                    @elseif($transaction->payment_status === 'expired')
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Xendit Invoice ID:</strong></td>
                                <td>{{ $transaction->xendit_invoice_id ?: 'Not available' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Reseller Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Reseller:</strong></td>
                                <td>
                                    <a href="{{ route('admin.resellers.show', $transaction->reseller->user->id) }}">
                                        {{ $transaction->reseller->store_name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Owner:</strong></td>
                                <td>{{ $transaction->reseller->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $transaction->reseller->user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $transaction->reseller->user->phone_number ?: 'Not provided' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Package Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Package Name:</strong></td>
                                <td>{{ $transaction->package->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Level:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $transaction->package->level === 'gold' ? 'warning' : 'primary' }}">
                                        {{ ucfirst($transaction->package->level) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>{{ $transaction->package->duration_days }} days</td>
                            </tr>
                            <tr>
                                <td><strong>Package Price:</strong></td>
                                <td>Rp {{ number_format($transaction->package->price, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Membership Status</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Current Level:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $transaction->reseller->membership_level === 'gold' ? 'warning' : 'primary' }}">
                                        {{ ucfirst($transaction->reseller->membership_level) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($transaction->reseller->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($transaction->reseller->isGracePeriod())
                                        <span class="badge bg-warning">Grace Period</span>
                                    @else
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Expires At:</strong></td>
                                <td>{{ $transaction->reseller->membership_expires_at->format('d M Y H:i') }}</td>
                            </tr>
                            @if($transaction->payment_status === 'paid')
                                <tr>
                                    <td><strong>Activation:</strong></td>
                                    <td>
                                        @if($transaction->paid_at)
                                            <span class="text-success">Activated on {{ $transaction->paid_at->format('d M Y H:i') }}</span>
                                        @else
                                            <span class="text-success">Activated</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.transactions.membership.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Membership Transactions
                    </a>
                    <div>
                        <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaction Timeline</h5>
            </div>
            <div class="card-body p-3">
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
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Membership Activated</h6>
                                <small class="text-muted">
                                    @if($transaction->paid_at)
                                        {{ $transaction->paid_at->format('d M Y H:i') }}
                                    @else
                                        Upon payment completion
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($transaction->payment_status === 'pending')
                        <a href="{{ $transaction->payment_link }}" target="_blank" class="btn btn-warning">
                            <i class="fas fa-external-link-alt me-1"></i> View Payment Page
                        </a>
                        <button type="button" class="btn btn-outline-primary" id="checkPaymentBtn" data-id="{{ $transaction->id }}">
                            <i class="fas fa-sync-alt me-1"></i> Check Payment Status
                        </button>
                    @endif
                    
                    <a href="{{ route('admin.resellers.show', $transaction->reseller->user->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-user me-1"></i> View Reseller
                    </a>
                    
                    <a href="mailto:{{ $transaction->reseller->user->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i> Contact Reseller
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Package Features</h5>
            </div>
            <div class="card-body">
                @if(is_array($transaction->package->features) && count($transaction->package->features) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($transaction->package->features as $feature)
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-check-circle text-success me-2"></i> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No features specified for this package.</p>
                @endif
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Check payment status
        $('#checkPaymentBtn').click(function() {
            const transactionId = $(this).data('id');
            
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Checking...');
            
            $.ajax({
                url: "{{ url('admin/transactions/membership') }}/" + transactionId + "/check-payment",
                type: 'POST',
                success: function(data) {
                    // Show message
                    Swal.fire({
                        icon: data.success ? 'success' : 'info',
                        title: data.success ? 'Payment Updated!' : 'Payment Status',
                        text: data.message
                    }).then(() => {
                        // Reload page if payment status changed
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                },
                error: function(error) {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.responseJSON?.message || 'An error occurred while checking payment status.'
                    });
                },
                complete: function() {
                    // Reset button
                    $('#checkPaymentBtn').prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Check Payment Status');
                }
            });
        });
    });
</script>
@endpush