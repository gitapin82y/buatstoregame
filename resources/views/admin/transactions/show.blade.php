<!-- resources/views/admin/transactions/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Transaction Details')

@section('page-title', 'Transaction Details')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transactions</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $transaction->invoice_number }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Transaction Information</h5>
                <div>
                    <span class="badge bg-{{ $transaction->payment_status === 'paid' ? 'success' : ($transaction->payment_status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($transaction->payment_status) }}
                    </span>
                    <span class="badge bg-{{ $transaction->process_status === 'completed' ? 'success' : ($transaction->process_status === 'processing' ? 'info' : ($transaction->process_status === 'pending' ? 'warning' : 'danger')) }}">
                        {{ ucfirst($transaction->process_status) }}
                    </span>
                </div>
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
                                <td><strong>Process Status:</strong></td>
                                <td>
                                    @if($transaction->process_status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                        @if($transaction->completed_at)
                                            <small class="text-muted ms-1">{{ $transaction->completed_at->format('d M Y H:i') }}</small>
                                        @endif
                                    @elseif($transaction->process_status === 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @elseif($transaction->process_status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
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
                        <h6 class="fw-bold mb-3">Customer Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Name:</strong></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $transaction->user->id) }}">
                                        {{ $transaction->user->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $transaction->user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $transaction->user->phone_number ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Reseller:</strong></td>
                                <td>
                                    <a href="{{ route('admin.resellers.show', $transaction->reseller->user->id) }}">
                                        {{ $transaction->reseller->store_name }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Product Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Game:</strong></td>
                                <td>
                                    <a href="{{ route('admin.games.show', $transaction->game->id) }}">
                                        {{ $transaction->game->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Service:</strong></td>
                                <td>{{ $transaction->service->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Service Type:</strong></td>
                                <td>{{ ucfirst($transaction->service->type) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Option:</strong></td>
                                <td>{{ $transaction->option->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Base Price:</strong></td>
                                <td>Rp {{ number_format($transaction->option->base_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Markup:</strong></td>
                                <td>
                                    @php
                                        $markup = $transaction->amount - $transaction->option->base_price;
                                        $markupPercent = $transaction->option->base_price > 0 ? round(($markup / $transaction->option->base_price) * 100, 2) : 0;
                                    @endphp
                                    Rp {{ number_format($markup, 0, ',', '.') }} ({{ $markupPercent }}%)
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Game Account Details</h6>
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
                            @if($transaction->service->type === 'joki' && isset($transaction->notes))
                            @php
                                $notes = json_decode($transaction->notes, true);
                            @endphp
                            <tr>
                                <td><strong>Password:</strong></td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <span class="text-muted password-hidden">•••••••••••</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 btn-toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <span class="password-text" style="display: none;">{{ isset($notes['password']) ? \Crypt::decrypt($notes['password']) : 'Not provided' }}</span>
                                    </span>
                                </td>
                            </tr>
                            @endif
                            @if(isset($transaction->notes) && (($transaction->service->type === 'joki' && isset(json_decode($transaction->notes, true)['notes'])) || ($transaction->service->type !== 'joki')))
                            <tr>
                                <td><strong>Notes:</strong></td>
                                <td>
                                    @if($transaction->service->type === 'joki')
                                        {{ json_decode($transaction->notes, true)['notes'] ?: 'No notes provided' }}
                                    @else
                                        {{ $transaction->notes ?: 'No notes provided' }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($transaction->payment_status === 'paid' && $transaction->process_status !== 'completed')
                <div class="mt-4">
                    <form action="{{ route('admin.transactions.update-status', $transaction->id) }}" method="POST" class="row">
                        @csrf
                        @method('PUT')
                        
                        <div class="col-md-6 mb-3">
                            <label for="process_status" class="form-label">Update Process Status</label>
                            <select class="form-select" id="process_status" name="process_status" required>
                                <option value="pending" {{ $transaction->process_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $transaction->process_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $transaction->process_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ $transaction->process_status === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Transactions
                    </a>
                    <div>
                        <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                        @if($transaction->payment_status === 'paid' && $transaction->process_status !== 'completed')
                            <button type="button" class="btn btn-success btn-process" data-id="{{ $transaction->id }}">
                                <i class="fas fa-sync-alt me-1"></i> Process Order
                            </button>
                        @endif
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
                    
                    @if($transaction->payment_status === 'paid' && $transaction->process_status !== 'completed')
                        <button type="button" class="btn btn-success btn-process" data-id="{{ $transaction->id }}">
                            <i class="fas fa-cogs me-1"></i> Process Order
                        </button>
                    @endif
                    
                    <a href="mailto:{{ $transaction->user->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i> Contact Customer
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">API Details</h5>
            </div>
            <div class="card-body">
                @if($transaction->service->type === 'topup' && $transaction->payment_status === 'paid')
                    <div class="mb-3">
                        <label class="form-label">API Response</label>
                        <div class="border rounded p-2 bg-light">
                            <code>{{ $transaction->api_response ?? 'No API response data available' }}</code>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label">API Parameters</label>
                        <div class="border rounded p-2 bg-light">
                            <code>
                                {<br>
                                &nbsp;&nbsp;"user_id": "{{ $transaction->user_identifier }}",<br>
                                @if($transaction->server_identifier)
                                &nbsp;&nbsp;"server_id": "{{ $transaction->server_identifier }}",<br>
                                @endif
                                &nbsp;&nbsp;"product_code": "{{ $transaction->option->api_code ?? 'N/A' }}",<br>
                                &nbsp;&nbsp;"ref_id": "{{ $transaction->invoice_number }}"<br>
                                }
                            </code>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        @if($transaction->payment_status !== 'paid')
                            API details will be available after payment is completed.
                        @elseif($transaction->service->type !== 'topup')
                            This service type ({{ ucfirst($transaction->service->type) }}) requires manual processing.
                        @else
                            No API details available for this transaction.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Process Transaction Modal -->
<div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processModalLabel">Process Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to manually process this transaction?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> This will attempt to deliver the product or service to the customer via the appropriate API or mark it for manual processing.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmProcess">Process Now</button>
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
        // Toggle password visibility
        $('.btn-toggle-password').click(function() {
            const passwordHidden = $(this).siblings('.password-hidden');
            const passwordText = $(this).siblings('.password-text');
            const icon = $(this).find('i');
            
            if (passwordHidden.is(':visible')) {
                passwordHidden.hide();
                passwordText.show();
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordHidden.show();
                passwordText.hide();
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // Process transaction
        $('.btn-process').click(function() {
            $('#processModal').modal('show');
        });
        
        $('#confirmProcess').click(function() {
            const transactionId = $('.btn-process').data('id');
            
            $.ajax({
                url: "{{ url('admin/transactions') }}/" + transactionId + "/process",
                type: 'POST',
                success: function(data) {
                    $('#processModal').modal('hide');
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    }).then(() => {
                        // Reload page to show updated status
                        window.location.reload();
                    });
                },
                error: function(error) {
                    $('#processModal').modal('hide');
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.responseJSON?.message || 'An error occurred while processing the transaction.'
                    });
                }
            });
        });
        
        // Check payment status
        $('#checkPaymentBtn').click(function() {
            const transactionId = $(this).data('id');
            
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Checking...');
            
            $.ajax({
                url: "{{ url('admin/transactions') }}/" + transactionId + "/check-payment",
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