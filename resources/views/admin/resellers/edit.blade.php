<!-- resources/views/admin/resellers/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Reseller')

@section('page-title', 'Edit Reseller')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{ $reseller->name }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Reseller Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.resellers.update', $reseller->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $reseller->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $reseller->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $reseller->phone_number) }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Account Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $reseller->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $reseller->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $reseller->resellerProfile->store_name ?? '') }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="subdomain" class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" id="subdomain" name="subdomain" value="{{ old('subdomain', $reseller->resellerProfile->subdomain ?? '') }}" required>
                                <span class="input-group-text">.buattokogame.com</span>
                            </div>
                            @error('subdomain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="custom_domain" class="form-label">Custom Domain (Optional)</label>
                            <input type="text" class="form-control @error('custom_domain') is-invalid @enderror" id="custom_domain" name="custom_domain" value="{{ old('custom_domain', $reseller->resellerProfile->custom_domain ?? '') }}">
                            <small class="text-muted">Leave empty if not using a custom domain</small>
                            @error('custom_domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="membership_level" class="form-label">Membership Level</label>
                            <select class="form-select @error('membership_level') is-invalid @enderror" id="membership_level" name="membership_level" required>
                                <option value="silver" {{ old('membership_level', $reseller->resellerProfile->membership_level ?? '') == 'silver' ? 'selected' : '' }}>Silver</option>
                                <option value="gold" {{ old('membership_level', $reseller->resellerProfile->membership_level ?? '') == 'gold' ? 'selected' : '' }}>Gold</option>
                            </select>
                            @error('membership_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="membership_expires_at" class="form-label">Membership Expires At</label>
                            <input type="datetime-local" class="form-control @error('membership_expires_at') is-invalid @enderror" id="membership_expires_at" name="membership_expires_at" value="{{ old('membership_expires_at', $reseller->resellerProfile->membership_expires_at ? $reseller->resellerProfile->membership_expires_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                            @error('membership_expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="store_description" class="form-label">Store Description</label>
                            <textarea class="form-control @error('store_description') is-invalid @enderror" id="store_description" name="store_description" rows="3">{{ old('store_description', $reseller->resellerProfile->store_description ?? '') }}</textarea>
                            @error('store_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.resellers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Reseller
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Membership Extension</h5>
            </div>
            <div class="card-body">
                @if($reseller->resellerProfile)
                    <div class="mb-3">
                        <label class="form-label">Current Membership</label>
                        <p class="mb-1"><strong>Level:</strong> {{ ucfirst($reseller->resellerProfile->membership_level) }}</p>
                        <p class="mb-1"><strong>Expires:</strong> {{ $reseller->resellerProfile->membership_expires_at ? $reseller->resellerProfile->membership_expires_at->format('d M Y H:i') : 'N/A' }}</p>
                        <p class="mb-1">
                            <strong>Status:</strong>
                            @if($reseller->resellerProfile->isActive())
                                <span class="badge bg-success">Active</span>
                            @elseif($reseller->resellerProfile->isGracePeriod())
                                <span class="badge bg-warning">Grace Period</span>
                            @else
                                <span class="badge bg-danger">Expired</span>
                            @endif
                        </p>
                    </div>
                    
                    <form action="{{ route('admin.resellers.extend-membership', $reseller->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="package_id" class="form-label">Package</label>
                            <select class="form-select" id="package_id" name="package_id" required>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" data-duration="{{ $package->duration_days }}">
                                        {{ $package->name }} ({{ $package->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration_days" class="form-label">Duration (days)</label>
                            <input type="number" class="form-control" id="duration_days" name="duration_days" value="30" min="30" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus-circle me-1"></i> Extend Membership
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> No reseller profile found. Please update this user with store information first.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.resellers.show', $reseller->id) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> View Details
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="fas fa-key me-1"></i> Reset Password
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteResellerModal">
                        <i class="fas fa-trash me-1"></i> Delete Reseller
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset the password for this reseller?</p>
                <p>A password reset link will be sent to: <strong>{{ $reseller->email }}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.resellers.reset-password', $reseller->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Reseller Modal -->
<div class="modal fade" id="deleteResellerModal" tabindex="-1" aria-labelledby="deleteResellerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteResellerModalLabel">Delete Reseller</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this reseller? This action cannot be undone and will delete all associated data, including transactions, games, and store settings.</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i> Warning: This is a permanent action!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.resellers.destroy', $reseller->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update duration from package selection
        $('#package_id').on('change', function() {
            var duration = $(this).find(':selected').data('duration');
            $('#duration_days').val(duration);
        });
        
        // Initial setting based on default selection
        $('#package_id').trigger('change');
    });
</script>
@endpush