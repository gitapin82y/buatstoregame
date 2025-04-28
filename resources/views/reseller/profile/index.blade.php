<!-- resources/views/reseller/profile/index.blade.php -->
@extends('layouts.reseller')

@section('title', 'Profile Settings')

@section('page-title', 'Profile Settings')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Settings Menu</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('reseller.profile.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-user-cog me-2"></i> Profile & Store
                </a>
                <a href="{{ route('reseller.profile.domain') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-globe me-2"></i> Domain Settings
                </a>
                <a href="#passwordSection" class="list-group-item list-group-item-action">
                    <i class="fas fa-lock me-2"></i> Change Password
                </a>
                <a href="{{ route('reseller.membership.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-id-card me-2"></i> Membership
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reseller.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                            <small class="text-muted">Contact support to change your email</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Store Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $reseller->store_name) }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="store_theme_color" class="form-label">Theme Color</label>
                            <input type="color" class="form-control form-control-color @error('store_theme_color') is-invalid @enderror" id="store_theme_color" name="store_theme_color" value="{{ old('store_theme_color', $reseller->store_theme_color) }}" title="Choose your store theme color">
                            @error('store_theme_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="store_description" class="form-label">Store Description</label>
                            <textarea class="form-control @error('store_description') is-invalid @enderror" id="store_description" name="store_description" rows="3">{{ old('store_description', $reseller->store_description) }}</textarea>
                            @error('store_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="store_logo" class="form-label">Store Logo</label>
                            <input type="file" class="form-control @error('store_logo') is-invalid @enderror" id="store_logo" name="store_logo">
                            <small class="text-muted">Recommended size: 200x200px. Max file size: 2MB.</small>
                            @if($reseller->store_logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $reseller->store_logo) }}" alt="{{ $reseller->store_name }}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            @error('store_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="store_banner" class="form-label">Store Banner (Optional)</label>
                            <input type="file" class="form-control @error('store_banner') is-invalid @enderror" id="store_banner" name="store_banner">
                            <small class="text-muted">Recommended size: 1200x300px. Max file size: 2MB.</small>
                            @if($reseller->store_banner)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $reseller->store_banner) }}" alt="{{ $reseller->store_name }}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            @error('store_banner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Social Media Links (Optional)</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="social_facebook" class="form-label">Facebook Username</label>
                            <div class="input-group">
                                <span class="input-group-text">facebook.com/</span>
                                <input type="text" class="form-control @error('social_facebook') is-invalid @enderror" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $reseller->social_facebook) }}">
                            </div>
                            @error('social_facebook')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_instagram" class="form-label">Instagram Username</label>
                            <div class="input-group">
                                <span class="input-group-text">instagram.com/</span>
                                <input type="text" class="form-control @error('social_instagram') is-invalid @enderror" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $reseller->social_instagram) }}">
                            </div>
                            @error('social_instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_twitter" class="form-label">Twitter Username</label>
                            <div class="input-group">
                                <span class="input-group-text">twitter.com/</span>
                                <input type="text" class="form-control @error('social_twitter') is-invalid @enderror" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $reseller->social_twitter) }}">
                            </div>
                            @error('social_twitter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_tiktok" class="form-label">TikTok Username</label>
                            <div class="input-group">
                                <span class="input-group-text">tiktok.com/@</span>
                                <input type="text" class="form-control @error('social_tiktok') is-invalid @enderror" id="social_tiktok" name="social_tiktok" value="{{ old('social_tiktok', $reseller->social_tiktok) }}">
                            </div>
                            @error('social_tiktok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm" id="passwordSection">
            <div class="card-header">
                <h5 class="card-title mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reseller.profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock me-2"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection