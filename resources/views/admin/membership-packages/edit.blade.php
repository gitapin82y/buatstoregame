<!-- resources/views/admin/membership-packages/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Membership Package')

@section('page-title', 'Edit Membership Package')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.membership-packages.index') }}">Membership Packages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{ $package->name }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Membership Package</h5>
                <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'danger' }}">
                    {{ ucfirst($package->status) }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.membership-packages.update', $package->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Package Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $package->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label">Membership Level</label>
                            <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                <option value="silver" {{ old('level', $package->level) == 'silver' ? 'selected' : '' }}>Silver</option>
                                <option value="gold" {{ old('level', $package->level) == 'gold' ? 'selected' : '' }}>Gold</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $package->price) }}" min="0" step="1000" required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="duration_days" class="form-label">Duration (Days)</label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" min="1" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $package->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $package->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $package->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Package Features</h5>
                    <p class="text-muted mb-3">Define what features are included in this membership package:</p>
                    
                    <div id="featuresContainer">
                        @if(old('features'))
                            @foreach(old('features') as $index => $feature)
                                <div class="input-group mb-2 feature-input">
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Enter a feature">
                                    <button class="btn btn-outline-danger remove-feature" type="button">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @elseif($package->features)
                            @foreach($package->features as $feature)
                                <div class="input-group mb-2 feature-input">
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Enter a feature">
                                    <button class="btn btn-outline-danger remove-feature" type="button">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2 feature-input">
                                <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                                <button class="btn btn-outline-danger remove-feature" type="button">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="addFeature">
                            <i class="fas fa-plus me-1"></i> Add Another Feature
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.membership-packages.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Package
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Package Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Created:</strong></span>
                    <span>{{ $package->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Last Updated:</strong></span>
                    <span>{{ $package->updated_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><strong>Active Sales:</strong></span>
                    <span>{{ $package->transactions_count }}</span>
                </div>
                
                <hr>
                
                <div class="alert {{ $package->transactions_count > 0 ? 'alert-warning' : 'alert-info' }} mb-0">
                    <i class="fas {{ $package->transactions_count > 0 ? 'fa-exclamation-triangle' : 'fa-info-circle' }} me-2"></i> 
                    @if($package->transactions_count > 0)
                        This package has been purchased by {{ $package->transactions_count }} reseller(s). Updating will not affect existing subscriptions.
                    @else
                        This package has not been purchased by any resellers yet.
                    @endif
                </div>
            </div>
        </div>
        
        @if($package->transactions_count === 0)
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Danger Zone</h5>
            </div>
            <div class="card-body">
                <p>Since this package has not been used, you can delete it if needed.</p>
                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deletePackageModal">
                    <i class="fas fa-trash me-1"></i> Delete Package
                </button>
            </div>
        </div>
        
        <!-- Delete Package Modal -->
        <div class="modal fade" id="deletePackageModal" tabindex="-1" aria-labelledby="deletePackageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePackageModalLabel">Delete Package</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this package? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.membership-packages.destroy', $package->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add new feature field
        $('#addFeature').click(function() {
            const newFeature = `
                <div class="input-group mb-2 feature-input">
                    <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                    <button class="btn btn-outline-danger remove-feature" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            $('#featuresContainer').append(newFeature);
        });
        
        // Remove feature field
        $(document).on('click', '.remove-feature', function() {
            // Only remove if there's more than one feature input
            if ($('.feature-input').length > 1) {
                $(this).closest('.feature-input').remove();
            } else {
                // If this is the last one, just clear the value
                $(this).prev('input').val('');
            }
        });
    });
</script>
@endpush