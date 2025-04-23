<!-- resources/views/store/game.blade.php -->
@extends('layouts.store')

@section('title', $game->name)

@section('content')
<!-- Game Banner -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6" data-aos="fade-right">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $game->name }}</li>
                    </ol>
                </nav>
                <h1 class="fw-bold mb-3">{{ $game->name }}</h1>
                <p>{{ $game->description }}</p>
            </div>
            <div class="col-md-6 text-center" data-aos="fade-left">
                @if($game->logo)
                    <img src="{{ asset('storage/' . $game->logo) }}" alt="{{ $game->name }}" class="img-fluid" style="max-height: 200px;">
                @else
                    <div class="bg-secondary rounded p-5 text-white">
                        <i class="fas fa-gamepad fa-5x"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Available Services</h2>
        
        <div class="row">
            @foreach($services as $service)
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card service-card h-100">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top p-3" alt="{{ $service->name }}">
                        @else
                            <div class="card-img-top bg-light text-center p-4">
                                @if($service->type === 'topup')
                                    <i class="fas fa-coins fa-3x text-primary-custom"></i>
                                @elseif($service->type === 'joki')
                                    <i class="fas fa-user-ninja fa-3x text-primary-custom"></i>
                                @elseif($service->type === 'coaching')
                                    <i class="fas fa-chalkboard-teacher fa-3x text-primary-custom"></i>
                                @elseif($service->type === 'formation')
                                    <i class="fas fa-sitemap fa-3x text-primary-custom"></i>
                                @else
                                    <i class="fas fa-gamepad fa-3x text-primary-custom"></i>
                                @endif
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text">{{ $service->description }}</p>
                            @if($service->price_range)
                                <p class="card-text"><small class="text-muted">Price Range: {{ $service->price_range }}</small></p>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="{{ route('store.service', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug, 'serviceSlug' => $service->slug]) }}" class="btn btn-primary-custom w-100">
                                Order Now
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if($services->isEmpty())
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No services available for this game at the moment.
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection