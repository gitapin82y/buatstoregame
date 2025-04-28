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
                    <h6 class="text-muted mb-3">Invoice Details</h6>
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
                            <td><strong>Order Status:</strong></td>
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
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Customer Information</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Name:</strong></td>
                            <td>{{ $transaction->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $transaction->user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $transaction->user->phone_number ?: 'Not provided' }}</td>
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
                    <h6 class="text-muted mb-3">Game Account Details</h6>
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
            
            <hr>
            
            <div class="mb-4">
                <h6 class="text-muted mb-3">Transaction Timeline</h6>
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
            
            @if($transaction->payment_status === 'pending')
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i> Payment is still pending for this transaction.
                    @if($transaction->payment_link)
                        <div class="mt-2">
                            <a href="{{ $transaction->payment_link }}" target="_blank" class="btn btn-warning btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i> View Payment Page
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('reseller.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Transactions
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
            <h5 class="card-title mb-0">Actions</h5>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2">
                @if($transaction->payment_status === 'paid' && $transaction->process_status !== 'completed')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markCompletedModal">
                        <i class="fas fa-check-circle me-1"></i> Mark as Completed
                    </button>
                @endif
                
                <a href="mailto:{{ $transaction->user->email }}" class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-1"></i> Contact Customer
                </a>
                
                @if($transaction->payment_status === 'pending')
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#resendLinkModal">
                        <i class="fas fa-paper-plane me-1"></i> Resend Payment Link
                    </button>
                @endif
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Financial Details</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span>Product Price:</span>
                <span>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Base Cost:</span>
                <span>Rp {{ number_format($transaction->option->base_price, 0, ',', '.') }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-0 fw-bold">
                <span>Your Profit:</span>
                <span>Rp {{ number_format($transaction->amount - $transaction->option->base_price, 0, ',', '.') }}</span>
            </div>
            <div class="text-end">
                <small class="text-muted">
                    @php
                        $profit = $transaction->amount - $transaction->option->base_price;
                        $profitPercent = $transaction->option->base_price > 0 ? ($profit / $transaction->option->base_price * 100) : 0;
                    @endphp
                    ({{ number_format($profitPercent, 2) }}% margin)
                </small>
            </div>
        </div>
    </div>
</div>