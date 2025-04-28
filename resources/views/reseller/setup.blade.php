<!-- resources/views/reseller/setup.blade.php -->
@extends('layouts.app')

@section('title', 'Setup Your Store')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Setup Your Game Store</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-store fa-4x text-primary mb-3"></i>
                        <h2>Welcome to BuatTokoGame!</h2>
                        <p class="lead">You're just a few steps away from creating your own game store.</p>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Complete the form below to set up your store. You'll be able to customize it further later.
                    </div>

                    <form action="{{ route('reseller.setup.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name') }}" required>
                            <small class="text-muted">Choose a memorable name for your store</small>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="store_description" class="form-label">Store Description (Optional)</label>
                            <textarea class="form-control @error('store_description') is-invalid @enderror" id="store_description" name="store_description" rows="3">{{ old('store_description') }}</textarea>
                            <small class="text-muted">Brief description of your store</small>
                            @error('store_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="subdomain" class="form-label">Choose Your Subdomain</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required>
                                <span class="input-group-text">.buattokogame.com</span>
                            </div>
                            <small class="text-muted">Only lowercase letters, numbers, and hyphens. Min 3 characters.</small>
                            @error('subdomain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-success mt-4">
                            <h5><i class="fas fa-gift me-2"></i> Special Offer for New Resellers!</h5>
                            <p class="mb-0">You'll get a <strong>7-day FREE trial</strong> of our Silver membership package to help you get started!</p>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i> Create My Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-generate subdomain from store name
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
    });
</script>
@endpush