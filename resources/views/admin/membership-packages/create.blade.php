<!-- resources/views/admin/membership-packages/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Add New Membership Package')

@section('page-title', 'Add New Membership Package')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.membership-packages.index') }}">Membership Packages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Package</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">New Membership Package Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.membership-packages.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Package Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            <small class="text-muted">E.g. "Silver Monthly", "Gold Premium"</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label">Membership Level</label>
                            <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                <option value="silver" {{ old('level') == 'silver' ? 'selected' : '' }}>Silver</option>
                                <option value="gold" {{ old('level') == 'gold' ? 'selected' : '' }}>Gold</option>
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" min="0" step="1000" required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="duration_days" class="form-label">Duration (Days)</label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" min="1" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <i class="fas fa-save me-1"></i> Create Package
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
        
        // Automatically set default features based on level
        $('#level').change(function() {
            const level = $(this).val();
            
            // Clear existing features
            $('#featuresContainer').empty();
            
            if (level === 'silver') {
                const silverFeatures = [
                    'Unlimited transactions',
                    'Basic store customization',
                    'Subdomain access',
                    'Standard customer support',
                    'Up to 10 games'
                ];
                
                silverFeatures.forEach(feature => {
                    const featureInput = `
                        <div class="input-group mb-2 feature-input">
                            <input type="text" class="form-control" name="features[]" value="${feature}" placeholder="Enter a feature">
                            <button class="btn btn-outline-danger remove-feature" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#featuresContainer').append(featureInput);
                });
            } else if (level === 'gold') {
                const goldFeatures = [
                    'Unlimited transactions',
                    'Advanced store customization',
                    'Custom domain support',
                    'Priority customer support',
                    'Unlimited games',
                    'Content creation tools',
                    'Advanced analytics',
                    'Marketing tools'
                ];
                
                goldFeatures.forEach(feature => {
                    const featureInput = `
                        <div class="input-group mb-2 feature-input">
                            <input type="text" class="form-control" name="features[]" value="${feature}" placeholder="Enter a feature">
                            <button class="btn btn-outline-danger remove-feature" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#featuresContainer').append(featureInput);
                });
            }
        });
        
        // Set suggested price based on level and duration
        $('#level, #duration_days').on('change', function() {
            const level = $('#level').val();
            const duration = parseInt($('#duration_days').val());
            
            if (!isNaN(duration)) {
                let basePrice = 0;
                
                if (level === 'silver') {
                    basePrice = 50000; // Monthly price for Silver
                } else if (level === 'gold') {
                    basePrice = 100000; // Monthly price for Gold
                }
                
                // Calculate price based on duration with slight discount for longer durations
                let calculatedPrice = 0;
                if (duration <= 30) {
                    calculatedPrice = basePrice;
                } else if (duration <= 90) {
                    // 3 months: 5% discount
                    calculatedPrice = basePrice * 3 * 0.95;
                } else if (duration <= 180) {
                    // 6 months: 10% discount
                    calculatedPrice = basePrice * 6 * 0.9;
                } else {
                    // 12 months: 15% discount
                    calculatedPrice = basePrice * 12 * 0.85;
                }
                
                // Round to nearest 1000
                calculatedPrice = Math.round(calculatedPrice / 1000) * 1000;
                
                $('#price').val(calculatedPrice);
            }
        });
    });
</script>
@endpush