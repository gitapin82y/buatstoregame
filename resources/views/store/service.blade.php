<!-- resources/views/store/service.blade.php -->
@extends('layouts.store')

@section('title', $service->name . ' - ' . $game->name)

@section('content')
<!-- Service Details -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('store.game', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug]) }}">{{ $game->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row align-items-center">
            <div class="col-md-6" data-aos="fade-right">
                <h1 class="fw-bold mb-3">{{ $service->name }}</h1>
                <p>{{ $service->description }}</p>
                
                <!-- Service Type Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Service Information</h5>
                        <p><strong>Type:</strong> 
                            @if($service->type === 'topup')
                                Top Up (Diamond/Currency)
                            @elseif($service->type === 'joki')
                                Joki Rank Service
                            @elseif($service->type === 'coaching')
                                Coaching Service
                            @elseif($service->type === 'formation')
                                Formation Setup Service
                            @else
                                {{ ucfirst($service->type) }} Service
                            @endif
                        </p>
                        <p class="mb-0"><strong>Game:</strong> {{ $game->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center" data-aos="fade-left">
                @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="img-fluid rounded shadow">
                @else
                    <div class="bg-primary-custom rounded p-5 text-white">
                        @if($service->type === 'topup')
                            <i class="fas fa-coins fa-5x"></i>
                        @elseif($service->type === 'joki')
                            <i class="fas fa-user-ninja fa-5x"></i>
                        @elseif($service->type === 'coaching')
                            <i class="fas fa-chalkboard-teacher fa-5x"></i>
                        @elseif($service->type === 'formation')
                            <i class="fas fa-sitemap fa-5x"></i>
                        @else
                            <i class="fas fa-gamepad fa-5x"></i>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Service Options -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <h2>Select Option</h2>
                <p>Choose an option below to proceed with your order.</p>
            </div>
            
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if($options->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No options available for this service at the moment.
                            </div>
                        @else
                            <form action="{{ route('store.checkout', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug, 'serviceSlug' => $service->slug]) }}" method="GET">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th width="50"></th>
                                                <th>Option</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($options as $option)
                                                @php
                                                    $resellerOption = $option->resellerServiceOptions->first();
                                                    $price = $resellerOption ? $resellerOption->selling_price : 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="option_id" id="option{{ $option->id }}" value="{{ $option->id }}" {{ $loop->first ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label for="option{{ $option->id }}" class="form-check-label">
                                                            <strong>{{ $option->name }}</strong>
                                                            @if($option->description)
                                                                <div><small class="text-muted">{{ $option->description }}</small></div>
                                                            @endif
                                                        </label>
                                                    </td>
                                                    <td>Rp {{ number_format($price, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary-custom">
                                        <i class="fas fa-shopping-cart me-2"></i> Proceed to Checkout
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">How to Order</h5>
                        <ol class="ps-3">
                            <li class="mb-2">Select the option you want to purchase</li>
                            <li class="mb-2">Fill in the required information</li>
                            <li class="mb-2">Complete the payment process</li>
                            <li class="mb-2">Your order will be processed immediately</li>
                        </ol>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Need Help?</h5>
                        <p>If you have any questions or need assistance, please contact our customer support.</p>
                        <div class="d-grid">
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-headset me-2"></i> Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection