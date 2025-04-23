<!-- resources/views/store/index.blade.php -->
@extends('layouts.store')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start" data-aos="fade-right">
                <h1 class="fw-bold mb-4">{{ $reseller->store_name }}</h1>
                <p class="fs-5 mb-4">{{ $reseller->store_description ?? 'Top up game favorit Anda dengan cepat, aman, dan harga terbaik!' }}</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="#games" class="btn btn-primary-custom btn-lg px-4 me-md-2">Top Up Now</a>
                    <a href="{{ route('store.track', $reseller->subdomain ?? $reseller->custom_domain) }}" class="btn btn-outline-light btn-lg px-4">Track Order</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left">
                <img src="{{ asset('images/hero-image.png') }}" alt="Gaming Illustration" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Featured Games Section -->
<section class="py-5" id="games">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">Featured Games</h2>
            <p class="text-muted">Top up your favorite games with the best price</p>
        </div>
        
        <div class="row">
            @foreach($featuredGames as $game)
                <div class="col-6 col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('store.game', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug]) }}" class="text-decoration-none">
                        <div class="card game-card h-100">
                            @if($game->banner)
                                <img src="{{ asset('storage/' . $game->banner) }}" class="card-img-top" alt="{{ $game->name }}">
                            @else
                                <div class="bg-light text-center py-5">
                                    <i class="fas fa-gamepad fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $game->name }}</h5>
                                <p class="card-text small text-muted">{{ Str::limit($game->description, 60) }}</p>
                                <button class="btn btn-sm btn-primary-custom">Top Up Now</button>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="#all-games" class="btn btn-outline-primary">View All Games</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">Why Choose Us</h2>
            <p class="text-muted">The benefits of using our service</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                            <i class="fas fa-bolt fa-2x"></i>
                        </div>
                        <h4 class="card-title">Fast Process</h4>
                        <p class="card-text">Transaksi instan dan proses otomatis yang cepat dalam hitungan detik.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                            <i class="fas fa-lock fa-2x"></i>
                        </div>
                        <h4 class="card-title">100% Secure</h4>
                        <p class="card-text">Sistem keamanan terbaik dan transaksi yang aman untuk semua pembayaran.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                            <i class="fas fa-headset fa-2x"></i>
                        </div>
                        <h4 class="card-title">24/7 Support</h4>
                        <p class="card-text">Layanan pelanggan siap membantu Anda 24 jam setiap hari.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- All Games Section -->
<section class="py-5" id="all-games">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">All Games</h2>
            <p class="text-muted">Browse all available games</p>
        </div>
        
        <div class="row">
            @foreach($games as $game)
                <div class="col-6 col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index % 4 * 100 }}">
                    <a href="{{ route('store.game', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug]) }}" class="text-decoration-none">
                        <div class="card game-card h-100">
                            @if($game->logo)
                                <img src="{{ asset('storage/' . $game->logo) }}" class="card-img-top p-3" alt="{{ $game->name }}">
                            @else
                                <div class="bg-light text-center py-4">
                                    <i class="fas fa-gamepad fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $game->name }}</h5>
                                <button class="btn btn-sm btn-primary-custom">Top Up Now</button>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection