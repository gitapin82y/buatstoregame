<!-- resources/views/reseller/profile/domain.blade.php -->
@extends('layouts.reseller')

@section('title', 'Domain Settings')

@section('page-title', 'Domain Settings')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.profile.index') }}">Profile Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page">Domain Settings</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Settings Menu</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('reseller.profile.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-cog me-2"></i> Profile & Store
                </a>
                <a href="{{ route('reseller.profile.domain') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-globe me-2"></i> Domain Settings
                </a>
                <a href="{{ route('reseller.profile.index') }}#passwordSection" class="list-group-item list-group-item-action">
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
                <h5 class="card-title mb-0">Subdomain Settings</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Your store can be accessed using a subdomain on our platform. This is included with all membership plans.
                </div>
                
                <form action="{{ route('reseller.profile.update-subdomain') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="subdomain" class="form-label">Your Subdomain</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('subdomain') is-invalid @enderror" id="subdomain" name="subdomain" value="{{ old('subdomain', $reseller->subdomain) }}" required>
                            <span class="input-group-text">.buattokogame.com</span>
                        </div>
                        <small class="text-muted">Only lowercase letters, numbers, and hyphens. Min 3 characters.</small>
                        @error('subdomain')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Store URL</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $reseller->subdomain }}.buattokogame.com" disabled readonly>
                            <a href="https://{{ $reseller->subdomain }}.buattokogame.com" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Update Subdomain
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Custom Domain</h5>
            </div>
            <div class="card-body">
                @if($canUseCustomDomain)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Custom domains allow you to use your own domain name (e.g., yourstore.com) for your game store. This feature is available with Gold membership.
                    </div>
                    
                    @if($reseller->custom_domain)
                        <div class="mb-4">
                            <label class="form-label">Current Custom Domain</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $reseller->custom_domain }}" disabled readonly>
                                <a href="https://{{ $reseller->custom_domain }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('reseller.profile.update-custom-domain') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="custom_domain" class="form-label">Custom Domain</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('custom_domain') is-invalid @enderror" id="custom_domain" name="custom_domain" value="{{ old('custom_domain', $reseller->custom_domain) }}" placeholder="yourdomain.com" required>
                                <button class="btn btn-outline-secondary" type="button" id="checkDomain">
                                    <i class="fas fa-check"></i> Check
                                </button>
                            </div>
                            <small class="text-muted">Enter your domain without http:// or www.</small>
                            <div id="domainStatus"></div>
                            @error('custom_domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Custom Domain
                            </button>
                        </div>
                    </form>
                    
                    @if(session()->has('dns_instructions'))
                        <div class="alert alert-success mt-4">
                            <h5><i class="fas fa-info-circle me-2"></i> DNS Configuration Instructions</h5>
                            <p>To connect your custom domain to your store, please add the following DNS record to your domain:</p>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Value</th>
                                            <th>TTL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ session('dns_instructions')['type'] }}</td>
                                            <td>{{ session('dns_instructions')['name'] }}</td>
                                            <td>{{ session('dns_instructions')['value'] }}</td>
                                            <td>{{ session('dns_instructions')['ttl'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p>DNS changes may take 24-48 hours to propagate globally.</p>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-crown me-2"></i> Gold Membership Feature</h5>
                        <p>Custom domain is only available with Gold membership. Upgrade your membership to use your own domain for your game store.</p>
                        <a href="{{ route('reseller.membership.index') }}" class="btn btn-warning mt-2">
                            <i class="fas fa-arrow-up me-2"></i> Upgrade to Gold
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Check domain availability
        $('#checkDomain').click(function() {
            const domain = $('#custom_domain').val();
            if (!domain) {
                $('#domainStatus').html('<div class="text-danger mt-2"><i class="fas fa-times-circle me-1"></i> Please enter a domain name.</div>');
                return;
            }
            
            $('#domainStatus').html('<div class="text-muted mt-2"><i class="fas fa-spinner fa-spin me-1"></i> Checking domain...</div>');
            
            $.ajax({
                url: "{{ route('reseller.profile.check-domain') }}",
                type: 'POST',
                data: { domain: domain },
                success: function(response) {
                    if (response.success) {
                        if (response.is_available) {
                            $('#domainStatus').html('<div class="text-success mt-2"><i class="fas fa-check-circle me-1"></i> Domain is available and can be used.</div>');
                        } else {
                            $('#domainStatus').html('<div class="text-warning mt-2"><i class="fas fa-exclamation-circle me-1"></i> Domain appears to be in use. You must own this domain to proceed.</div>');
                        }
                    } else {
                        $('#domainStatus').html('<div class="text-danger mt-2"><i class="fas fa-times-circle me-1"></i> Error checking domain.</div>');
                    }
                },
                error: function() {
                    $('#domainStatus').html('<div class="text-danger mt-2"><i class="fas fa-times-circle me-1"></i> Error checking domain.</div>');
                }
            });
        });
    });
</script>
@endpush