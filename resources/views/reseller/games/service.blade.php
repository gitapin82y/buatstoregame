<!-- resources/views/reseller/games/services.blade.php -->
@extends('layouts.reseller')

@section('title', 'Manage Services')

@section('page-title', 'Manage Services for ' . $game->name)

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.index') }}">Games</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.edit', $game->id) }}">{{ $game->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-12 mb-4">
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i> Managing Game Services</h5>
            <p class="mb-0">Here you can activate/deactivate services, adjust profit margins, and manage options for each service. Changes will be reflected immediately on your store.</p>
        </div>
    </div>
    
    @foreach($services as $service)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $service->name }}</h5>
                    <span class="badge bg-{{ $service->resellerGameServices->first() && $service->resellerGameServices->first()->is_active ? 'success' : 'secondary' }}">
                        {{ $service->resellerGameServices->first() && $service->resellerGameServices->first()->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted">{{ $service->description }}</p>
                        <p class="mb-1"><strong>Type:</strong> {{ ucfirst($service->type) }}</p>
                        <p><strong>Available Options:</strong> {{ $service->options->where('status', 'active')->count() }}</p>
                    </div>
                    
                    <form action="{{ route('reseller.services.update', ['gameId' => $game->id, 'serviceId' => $service->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="profit_margin_{{ $service->id }}" class="form-label">Profit Margin (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="profit_margin_{{ $service->id }}" name="profit_margin" value="{{ $service->resellerGameServices->first() ? $service->resellerGameServices->first()->profit_margin : $resellerGame->profit_margin }}" min="0" max="100" step="0.01" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="display_order_{{ $service->id }}" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="display_order_{{ $service->id }}" name="display_order" value="{{ $service->resellerGameServices->first() ? $service->resellerGameServices->first()->display_order : 0 }}" min="0" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active_{{ $service->id }}" name="is_active" value="1" {{ $service->resellerGameServices->first() && $service->resellerGameServices->first()->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active_{{ $service->id }}">
                                        Service is active
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="apply_to_all_{{ $service->id }}" name="apply_to_all" value="1">
                                    <label class="form-check-label" for="apply_to_all_{{ $service->id }}">
                                        Apply profit margin to all options
                                    </label>
                                    <small class="text-muted d-block">This will update all option prices based on the profit margin</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('reseller.services.options', ['gameId' => $game->id, 'serviceId' => $service->id]) }}" class="btn btn-primary">
                                <i class="fas fa-cogs me-1"></i> Manage Options
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    
    @if($services->isEmpty())
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> No services are available for this game yet.
            </div>
        </div>
    @endif
    
    <div class="col-md-12 mt-2 mb-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('reseller.games.edit', $game->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Game Settings
            </a>
        </div>
    </div>
</div>
@endsection