<!-- resources/views/admin/resellers/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Add New Reseller')

@section('page-title', 'Add New Reseller')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Reseller Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.resellers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name') }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="store_description" class="form-label">Store Description</label>
                            <textarea class="form-control @error('store_description') is-invalid @enderror" id="store_description" name="store_description" rows="3">{{ old('store_description') }}</textarea>
                            @error('store_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="subdomain" class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required>
                                <span class="input-group-text">.buattokogame.com</span>
                            </div>
                            <small class="text-muted">Only lowercase letters, numbers, and hyphens. Min 3 characters.</small>
                            @error('subdomain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="membership_level" class="form-label">Membership Level</label>
                            <select class="form-select @error('membership_level') is-invalid @enderror" id="membership_level" name="membership_level" required>
                                <option value="silver" {{ old('membership_level') == 'silver' ? 'selected' : '' }}>Silver</option>
                                <option value="gold" {{ old('membership_level') == 'gold' ? 'selected' : '' }}>Gold</option>
                            </select>
                            @error('membership_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="package_id" class="form-label">Package</label>
                            <select class="form-select @error('package_id') is-invalid @enderror" id="package_id" name="package_id" required>
                                <option value="">Select a package</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }} ({{ $package->duration_days }} days)
                                    </option>
                                @endforeach
                            </select>
                            @error('package_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="duration_days" class="form-label">Duration (Days)</label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" min="30" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.resellers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Reseller</button>
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
        // Update subdomain based on store name
        $('#store_name').on('blur', function() {
            if ($('#subdomain').val() === '') {
                var storeName = $(this).val();
                var subdomain = storeName.toLowerCase()
                    .replace(/\s+/g, '-')           // Replace spaces with -
                    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                    .replace(/^-+/, '')             // Trim - from start of text
                    .replace(/-+$/, '');            // Trim - from end of text
                
                $('#subdomain').val(subdomain);
            }
        });
        
        // Update duration based on package selection
        $('#package_id').on('change', function() {
            var packageId = $(this).val();
            var packageOption = $(this).find('option:selected');
            var packageText = packageOption.text();
            
            // Extract membership level from package name (if contains "Gold" or "Silver")
            if (packageText.includes('Gold')) {
                $('#membership_level').val('gold');
            } else if (packageText.includes('Silver')) {
                $('#membership_level').val('silver');
            }
            
            // Extract duration days from package text (match number followed by "days")
            var durationMatch = packageText.match(/(\d+)\s*days/);
            if (durationMatch && durationMatch[1]) {
                $('#duration_days').val(durationMatch[1]);
            }
        });
    });
</script>
@endpush