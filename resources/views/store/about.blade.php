<!-- resources/views/store/about.blade.php -->
@extends('layouts.store')

@section('title', 'About Us')

@section('content')
<!-- About Hero Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="fw-bold mb-4">About {{ $reseller->store_name }}</h1>
                <p class="lead mb-4">{{ $reseller->store_description ?? 'Welcome to our game store. We provide the best gaming products and services at competitive prices.' }}</p>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                @if($reseller->store_banner)
                    <img src="{{ asset('storage/' . $reseller->store_banner) }}" alt="{{ $reseller->store_name }}" class="img-fluid rounded shadow">
                @elseif($reseller->store_logo)
                    <img src="{{ asset('storage/' . $reseller->store_logo) }}" alt="{{ $reseller->store_name }}" class="img-fluid" style="max-height: 200px;">
                @else
                    <div class="bg-primary-custom rounded p-5 text-white">
                        <i class="fas fa-store fa-5x"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-10 text-center" data-aos="fade-up">
                <h2 class="fw-bold mb-4">Our Story</h2>
                <p class="mb-4">{{ $reseller->store_name }} was founded with a simple mission: to provide gamers with a reliable, affordable, and convenient way to access their favorite games and services.</p>
                <p>We understand the passion and dedication that gamers have for their favorite titles, and we're committed to enhancing your gaming experience through our carefully curated selection of services and products.</p>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                            <i class="fas fa-medal fa-2x"></i>
                        </div>
                        <h4 class="card-title">Quality Service</h4>
                        <p class="card-text">We pride ourselves on providing top-notch service to all our customers. Your satisfaction is our priority.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                            <i class="fas fa-bolt fa-2x"></i>
                        </div>
                        <h4 class="card-title">Fast Delivery</h4>
                        <p class="card-text">Our automated systems ensure that your orders are processed quickly and efficiently, minimizing waiting time.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h4 class="card-title">Secure Transactions</h4>
                        <p class="card-text">We use industry-standard security practices to ensure that all your transactions are safe and protected.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center" data-aos="fade-up">
                <h2 class="fw-bold mb-4">Get In Touch</h2>
                <p class="mb-5">Have questions or need assistance? We're here to help! Feel free to reach out to us through any of the following channels.</p>
                
                <div class="row">
                    @if($reseller->user->email)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                                <h5 class="card-title">Email</h5>
                                <p class="card-text"><a href="mailto:{{ $reseller->user->email }}" class="text-decoration-none">{{ $reseller->user->email }}</a></p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($reseller->user->phone_number)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-phone fa-2x"></i>
                                </div>
                                <h5 class="card-title">Phone</h5>
                                <p class="card-text"><a href="tel:{{ $reseller->user->phone_number }}" class="text-decoration-none">{{ $reseller->user->phone_number }}</a></p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary-custom text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-comments fa-2x"></i>
                                </div>
                                <h5 class="card-title">Social Media</h5>
                                <div class="d-flex justify-content-center gap-3 mt-3">
                                    @if($reseller->social_facebook)
                                        <a href="https://facebook.com/{{ $reseller->social_facebook }}" target="_blank" class="text-decoration-none">
                                            <i class="fab fa-facebook fa-lg"></i>
                                        </a>
                                    @endif
                                    @if($reseller->social_instagram)
                                        <a href="https://instagram.com/{{ $reseller->social_instagram }}" target="_blank" class="text-decoration-none">
                                            <i class="fab fa-instagram fa-lg"></i>
                                        </a>
                                    @endif
                                    @if($reseller->social_twitter)
                                        <a href="https://twitter.com/{{ $reseller->social_twitter }}" target="_blank" class="text-decoration-none">
                                            <i class="fab fa-twitter fa-lg"></i>
                                        </a>
                                    @endif
                                    @if($reseller->social_tiktok)
                                        <a href="https://tiktok.com/@{{ $reseller->social_tiktok }}" target="_blank" class="text-decoration-none">
                                            <i class="fab fa-tiktok fa-lg"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection