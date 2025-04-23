
<!-- resources/views/layouts/store.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Game Store') - {{ $reseller->store_name }}</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta -->
    <meta name="description" content="{{ $reseller->store_description ?? 'Game top-up and services' }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $reseller->store_logo ? asset('storage/' . $reseller->store_logo) : asset('images/favicon.ico') }}" type="image/x-icon">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: {{ $reseller->store_theme_color ?? '#3490dc' }};
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar-brand {
            font-weight: 700;
        }
        
        .bg-primary-custom {
            background-color: var(--primary-color);
        }
        
        .text-primary-custom {
            color: var(--primary-color);
        }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-color);
            filter: brightness(90%);
            color: white;
        }
        
        .game-card {
            transition: transform 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('images/hero-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 40px 0;
        }
        
        .social-icons a {
            color: white;
            font-size: 24px;
            margin-right: 15px;
        }
        
        .service-card {
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}">
                @if($reseller->store_logo)
                    <img src="{{ asset('storage/' . $reseller->store_logo) }}" alt="{{ $reseller->store_name }}" height="40">
                @else
                    {{ $reseller->store_name }}
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('store.index') ? 'active' : '' }}" href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('store.about') ? 'active' : '' }}" href="{{ route('store.about', $reseller->subdomain ?? $reseller->custom_domain) }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('store.track') ? 'active' : '' }}" href="{{ route('store.track', $reseller->subdomain ?? $reseller->custom_domain) }}">Track Order</a>
                    </li>
                    @auth
                        @if(Auth::user()->role === 'user')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.dashboard') }}">Dashboard</a>
                            </li>
                        @elseif(Auth::user()->role === 'reseller' && Auth::user()->id === $reseller->user_id)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('reseller.dashboard') }}">My Dashboard</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if (session('success'))
            <div class="container mt-4">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="container mt-4">
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>{{ $reseller->store_name }}</h5>
                    <p>{{ $reseller->store_description }}</p>
                    <div class="social-icons">
                        @if($reseller->social_facebook)
                            <a href="https://facebook.com/{{ $reseller->social_facebook }}" target="_blank"><i class="fab fa-facebook"></i></a>
                        @endif
                        @if($reseller->social_instagram)
                            <a href="https://instagram.com/{{ $reseller->social_instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if($reseller->social_twitter)
                            <a href="https://twitter.com/{{ $reseller->social_twitter }}" target="_blank"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if($reseller->social_tiktok)
                            <a href="https://tiktok.com/@{{ $reseller->social_tiktok }}" target="_blank"><i class="fab fa-tiktok"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}" class="text-white">Home</a></li>
                        <li><a href="{{ route('store.about', $reseller->subdomain ?? $reseller->custom_domain) }}" class="text-white">About</a></li>
                        <li><a href="{{ route('store.track', $reseller->subdomain ?? $reseller->custom_domain) }}" class="text-white">Track Order</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> {{ $reseller->user->email }}</li>
                        @if($reseller->user->phone_number)
                            <li><i class="fas fa-phone me-2"></i> {{ $reseller->user->phone_number }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-4 bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ $reseller->store_name }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Powered by <a href="{{ route('home') }}" class="text-white">BuatTokoGame</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        // Initialize AOS
        AOS.init();
        
        // CSRF Token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>