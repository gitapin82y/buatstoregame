<!-- resources/views/reseller/games/add.blade.php -->
@extends('layouts.reseller')

@section('title', 'Add Game to Store')

@section('page-title', 'Add Game to Store')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.index') }}">Games</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add {{ $game->name }} to Store</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Add Game to Your Store</h5>
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
                    When you add this game to your store, all its services and options will be automatically added. You can customize pricing and availability later.
                </div>
                
                <form action="{{ route('reseller.games.store', $game->id) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profit_margin" class="form-label">Default Profit Margin (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('profit_margin') is-invalid @enderror" id="profit_margin" name="profit_margin" value="{{ old('profit_margin', 20) }}" min="0" max="100" step="0.01" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">This percentage will be added to the base price of all game services and options</small>
                            @error('profit_margin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0" required>
                            <small class="text-muted">Games with lower numbers appear first</small>
                            @error('display_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Activate game immediately
                                </label>
                                <small class="form-text text-muted d-block">If checked, the game will be visible on your store right away</small>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('reseller.games.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Add to My Store
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Game Services</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">This game includes the following services:</p>
                
                <div class="list-group">
                    @forelse($game->services as $service)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $service->name }}</h6>
                                <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </div>
                            <small class="text-muted">{{ ucfirst($service->type) }} Service</small>
                            <div class="mt-1">
                                <small class="text-muted">{{ $service->options->count() }} options available</small>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted">
                            <i class="fas fa-info-circle me-1"></i> No services available for this game yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update price examples based on profit margin
        $('#profit_margin').on('input', function() {
            const margin = parseFloat($(this).val()) || 0;
            
            // You can add code here to show price examples if needed
            // For example:
            // const basePrice = 100000;
            // const sellingPrice = basePrice * (1 + margin/100);
            // $('#price_example').text('Rp ' + sellingPrice.toLocaleString('id-ID'));
        });
    });
</script>
@endpush