<!-- resources/views/admin/withdrawals/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Withdrawal Details')

@section('page-title', 'Withdrawal Details')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.withdrawals.index') }}">Withdrawals</a></li>
                <li class="breadcrumb-item active" aria-current="page">Withdrawal #{{ $withdrawal->id }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Withdrawal Information</h5>
                <span class="badge bg-{{ $withdrawal->status === 'approved' ? 'success' : ($withdrawal->status === 'pending' ? 'warning' : 'danger') }}">
                    {{ ucfirst($withdrawal->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Withdrawal Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Withdrawal ID:</strong></td>
                                <td>#{{ $withdrawal->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount:</strong></td>
                                <td>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($withdrawal->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                        @if($withdrawal->approved_at)
                                            <small class="text-muted ms-1">{{ $withdrawal->approved_at->format('d M Y H:i') }}</small>
                                        @endif
                                    @elseif($withdrawal->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                        @if($withdrawal->rejected_at)
                                            <small class="text-muted ms-1">{{ $withdrawal->rejected_at->format('d M Y H:i') }}</small>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Request Date:</strong></td>
                                <td>{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @if($withdrawal->status === 'rejected')
                            <tr>
                                <td><strong>Rejection Reason:</strong></td>
                                <td>{{ $withdrawal->rejection_reason }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Bank Account Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Bank Name:</strong></td>
                                <td>{{ $withdrawal->bank_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Account Name:</strong></td>
                                <td>{{ $withdrawal->account_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Account Number:</strong></td>
                                <td>{{ $withdrawal->account_number }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Reseller Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Reseller:</strong></td>
                                <td>
                                    <a href="{{ route('admin.resellers.show', $withdrawal->reseller->user->id) }}">
                                        {{ $withdrawal->reseller->store_name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Owner:</strong></td>
                                <td>{{ $withdrawal->reseller->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $withdrawal->reseller->user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $withdrawal->reseller->user->phone_number ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Balance:</strong></td>
                                <td>Rp {{ number_format($withdrawal->reseller->balance, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    @if($withdrawal->status === 'approved' && $withdrawal->proof_image)
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Payment Proof</h6>
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $withdrawal->proof_image) }}" class="img-fluid rounded border" style="max-height: 300px;">
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $withdrawal->proof_image) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-eye me-1"></i> View Full Size
                                </a>
                                <a href="{{ asset('storage/' . $withdrawal->proof_image) }}" class="btn btn-sm btn-outline-secondary" download>
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                @if($withdrawal->status === 'pending')
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-success btn-lg w-100 btn-approve" data-id="{{ $withdrawal->id }}">
                                <i class="fas fa-check-circle me-1"></i> Approve Withdrawal
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-danger btn-lg w-100 btn-reject" data-id="{{ $withdrawal->id }}">
                                <i class="fas fa-times-circle me-1"></i> Reject Withdrawal
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Withdrawals
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
                <h5 class="card-title mb-0">Withdrawal Timeline</h5>
            </div>
            <div class="card-body p-3">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Request Submitted</h6>
                            <small class="text-muted">{{ $withdrawal->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $withdrawal->status === 'approved' ? 'bg-success' : ($withdrawal->status === 'pending' ? 'bg-warning' : 'bg-danger') }}"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">
                                @if($withdrawal->status === 'approved')
                                    Withdrawal Approved
                                @elseif($withdrawal->status === 'pending')
                                    Awaiting Approval
                                @else
                                    Withdrawal Rejected
                                @endif
                            </h6>
                            @if($withdrawal->status === 'approved' && $withdrawal->approved_at)
                                <small class="text-muted">{{ $withdrawal->approved_at->format('d M Y H:i') }}</small>
                            @elseif($withdrawal->status === 'rejected' && $withdrawal->rejected_at)
                                <small class="text-muted">{{ $withdrawal->rejected_at->format('d M Y H:i') }}</small>
                            @elseif($withdrawal->status === 'pending')
                                <small class="text-muted">Pending review</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.resellers.show', $withdrawal->reseller->user->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-user me-1"></i> View Reseller
                    </a>
                    
                    <a href="mailto:{{ $withdrawal->reseller->user->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i> Contact Reseller
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Withdrawal History</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($previousWithdrawals as $prev)
                        <div class="list-group-item bg-transparent px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Rp {{ number_format($prev->amount, 0, ',', '.') }}</h6>
                                    <small class="text-muted">{{ $prev->created_at->format('d M Y') }}</small>
                                </div>
                                <span class="badge bg-{{ $prev->status === 'approved' ? 'success' : ($prev->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($prev->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No previous withdrawals found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Withdrawal Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p>You are about to approve the withdrawal request for <strong>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="proof_image" class="form-label">Proof of Payment</label>
                        <input type="file" class="form-control" id="proof_image" name="proof_image" required accept="image/*">
                        <small class="text-muted">Upload a screenshot or image of the bank transfer confirmation.</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> By approving this withdrawal, you confirm that you have transferred the funds to the reseller's bank account.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Approve Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Withdrawal Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>You are about to reject the withdrawal request for <strong>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</strong>. Please provide a reason:</p>
                    
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Rejecting this withdrawal will return the funds to the reseller's balance.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject Withdrawal
                    </button>
                </div>
            </form>
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
    
    @media print {
        .btn, .card-footer, #sidebar-wrapper, .nav, .breadcrumb {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Approve Withdrawal
        $('.btn-approve').click(function() {
            $('#approveModal').modal('show');
        });
        
        // Reject Withdrawal
        $('.btn-reject').click(function() {
            $('#rejectModal').modal('show');
        });
    });
</script>
@endpush