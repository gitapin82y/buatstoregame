
<!-- resources/views/layouts/reseller.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reseller Dashboard') - {{ config('app.name') }}</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 text-white fs-4 fw-bold">
                <i class="fas fa-store me-2"></i>Reseller Panel
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="{{ route('reseller.dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{ route('reseller.games.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.games*') ? 'active' : '' }}">
                    <i class="fas fa-gamepad me-2"></i>Games
                </a>
                <a href="{{ route('reseller.transactions.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.transactions*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt me-2"></i>Transactions
                </a>
                <a href="{{ route('reseller.withdrawals.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.withdrawals*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave me-2"></i>Withdrawals
                </a>
                <a href="{{ route('reseller.membership.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.membership*') ? 'active' : '' }}">
                    <i class="fas fa-id-card me-2"></i>Membership
                </a>
                @if(Auth::user()->resellerProfile && Auth::user()->resellerProfile->membership_level === 'gold' && Auth::user()->resellerProfile->isActive())
                <a href="{{ route('reseller.content.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.content*') ? 'active' : '' }}">
                    <i class="fas fa-photo-video me-2"></i>Content
                </a>
                @endif
                <a href="{{ route('reseller.profile.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reseller.profile*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog me-2"></i>Profile
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-headset me-2"></i>Support
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-power-off me-2"></i>Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bars primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">@yield('page-title', 'Dashboard')</h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        @if(Auth::user()->resellerProfile)
                        <li class="nav-item me-3">
                            <a href="{{ Auth::user()->resellerProfile->getDomainUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i> View Store
                            </a>
                        </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('reseller.profile.index') }}"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('reseller.profile.domain') }}"><i class="fas fa-globe me-2"></i>Domain Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        // Toggle sidebar
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        
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