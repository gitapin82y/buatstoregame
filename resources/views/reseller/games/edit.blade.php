<!-- resources/views/reseller/games/edit.blade.php -->
@extends('layouts.reseller')

@section('title', 'Edit Game')

@section('page-title', 'Edit Game')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.index') }}">Games</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{ $game->name }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Game Settings</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        @if($game->logo)
                            <img src="{{ asset('storage/' . $game->logo) }}" alt="{{ $game->name }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="bg-light rounded p-3 mb-3 text-center">
                                <i class="fas fa-gamepad fa-4x text-secondary"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h4>{{ $game->name }}</h4>
                        <p>{{ $game->description }}</p>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> 
                    Configure how this game appears in your store and set the default profit margin. You can manage services and options separately.
                </div>
                
                <form action="{{ route('reseller.games.update', $game->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profit_margin" class="form-label">Default Profit Margin (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('profit_margin') is-invalid @enderror" id="profit_margin" name="profit_margin" value="{{ old('profit_margin', $resellerGame->profit_margin) }}" min="0" max="100" step="0.01" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">This percentage will be added to the base price of all game services and options</small>
                            @error('profit_margin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', $resellerGame->display_order) }}" min="0" required>
                            <small class="text-muted">Games with lower numbers appear first</small>
                            @error('display_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $resellerGame->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Game is active
                                </label>
                                <small class="form-text text-muted d-block">If checked, the game will be visible on your store</small>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('reseller.games.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('reseller.games.services', $game->id) }}" class="btn btn-primary">
                        <i class="fas fa-cogs me-2"></i> Manage Services
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#removeGameModal">
                        <i class="fas fa-trash me-2"></i> Remove from Store
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Store Preview</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Status:</span>
                    <span class="badge bg-{{ $resellerGame->is_active ? 'success' : 'danger' }}">
                        {{ $resellerGame->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Services:</span>
                    <span class="badge bg-secondary">{{ $resellerGame->resellerGameServices->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Last Updated:</span>
                    <span>{{ $resellerGame->updated_at->format('d M Y') }}</span>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('store.game', ['domain' => Auth::user()->resellerProfile->subdomain, 'gameSlug' => $game->slug]) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-external-link-alt me-1"></i> View in Store
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove Game Modal -->
<div class="modal fade" id="removeGameModal" tabindex="-1" aria-labelledby="removeGameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeGameModalLabel">Confirm Removal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove <strong>{{ $game->name }}</strong> from your store?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> This will remove all associated services and options from your store. This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('reseller.games.destroy', $game->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove Game</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection