<!-- resources/views/admin/resellers/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Reseller')

@section('page-title', 'Edit Reseller')

@section('content')
<div class="row mt-4">
    <div class="col-12">
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
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $reseller->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
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
                            <label for="status" class="form-label">User Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $reseller->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $reseller->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
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
                            <label for="custom_domain" class="form-label">Custom Domain</label>
                            <input type="text" class="form-control @error('custom_domain') is-invalid @enderror" id="custom_domain" name="custom_domain" value="{{ old('custom_domain', $reseller->resellerProfile->custom_domain ?? '') }}">
                            <small class="text-muted">Leave empty if not using custom domain</small>
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
                            <input type="datetime-local" class="form-control @error('membership_expires_at') is-invalid @enderror" id="membership_expires_at" name="membership_expires_at" value="{{ old('membership_expires_at', $reseller->resellerProfile && $reseller->resellerProfile->membership_expires_at ? $reseller->resellerProfile->membership_expires_at->format('Y-m-d\TH:i') : '') }}" required>
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
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.resellers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Reseller</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection