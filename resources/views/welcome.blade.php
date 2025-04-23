<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BuatTokoGame') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f8f9fa;
            }
            
            .navbar {
                padding: 15px 0;
            }
            
            .hero-section {
                background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('images/hero-bg.jpg') }}');
                background-size: cover;
                background-position: center;
                color: white;
                padding: 120px 0;
                margin-bottom: 80px;
            }
            
            .hero-title {
                font-size: 3rem;
                font-weight: 700;
            }
            
            .feature-card {
                border-radius: 10px;
                overflow: hidden;
                transition: all 0.3s ease;
                height: 100%;
            }
            
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            
            .pricing-card {
                border-radius: 15px;
                overflow: hidden;
                transition: all 0.3s ease;
                border: none;
                height: 100%;
            }
            
            .pricing-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            
            .pricing-header {
                background-color: #3490dc;
                color: white;
                padding: 20px;
            }
            
            .pricing-gold {
                background-color: #f59e0b;
            }
            
            .cta-section {
                background-color: #3490dc;
                color: white;
                padding: 80px 0;
                margin-top: 80px;
            }
            
            .footer {
                padding: 60px 0 30px;
                background-color: #343a40;
                color: white;
            }
            
            .social-icons a {
                color: white;
                font-size: 24px;
                margin-right: 15px;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="fas fa-gamepad me-2"></i>{{ config('app.name', 'BuatTokoGame') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#pricing">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#faq">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                        @if (Route::has('login'))
                            @auth
                                @if(Auth::user()->role === 'admin')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                                    </li>
                                @elseif(Auth::user()->role === 'reseller')
                                    <li class="nav-item">
                                        <a href="{{ route('reseller.dashboard') }}" class="nav-link">Dashboard</a>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a href="{{ route('user.dashboard') }}" class="nav-link">Dashboard</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('login') }}" class="nav-link">Log in</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a href="{{ route('register') }}" class="btn btn-primary ms-2">Register</a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6" data-aos="fade-right">
                        <h1 class="hero-title mb-4">Mulai Bisnis Toko Game Online dengan Mudah</h1>
                        <p class="fs-5 mb-4">Platform terbaik untuk memulai bisnis toko game online. Daftar sekarang dan dapatkan toko game online siap pakai dalam hitungan menit!</p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                            <a href="#features" class="btn btn-outline-light btn-lg">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left">
                        <img src="{{ asset('images/hero-image.png') }}" alt="Gaming Platform" class="img-fluid">
                    </div>
                </div>
            </div>
        </section>

         <!-- Features Section -->
         <section id="features" class="py-5">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="fw-bold mb-3">Fitur Utama</h2>
                    <p class="text-muted">Platform lengkap untuk memulai dan mengembangkan bisnis game Anda</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-rocket fa-2x"></i>
                                </div>
                                <h4>Website Instan</h4>
                                <p class="text-muted">Dapatkan website toko game online siap pakai dalam hitungan menit. Tanpa perlu koding atau desain.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-gamepad fa-2x"></i>
                                </div>
                                <h4>Multi Game Support</h4>
                                <p class="text-muted">Mendukung berbagai game populer. Topup diamond, joki rank, dan berbagai layanan game lainnya.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-credit-card fa-2x"></i>
                                </div>
                                <h4>Payment Gateway</h4>
                                <p class="text-muted">Integrasi dengan Xendit untuk menerima pembayaran dari berbagai metode: transfer bank, e-wallet, dan lainnya.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-globe fa-2x"></i>
                                </div>
                                <h4>Custom Domain</h4>
                                <p class="text-muted">Gunakan domain sendiri atau subdomain kami untuk memperkuat brand bisnis Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                                <h4>Analitik Lengkap</h4>
                                <p class="text-muted">Dashboard analitik untuk memantau penjualan, produk populer, dan performa bisnis Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="600">
                        <div class="card feature-card shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle bg-primary text-white d-inline-flex p-3 mb-4">
                                    <i class="fas fa-robot fa-2x"></i>
                                </div>
                                <h4>AI Content Generator</h4>
                                <p class="text-muted">Buat konten promosi untuk media sosial secara otomatis dengan bantuan teknologi AI.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="fw-bold mb-3">Paket Membership</h2>
                    <p class="text-muted">Pilih paket yang sesuai dengan kebutuhan bisnis Anda</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card pricing-card shadow-sm h-100">
                            <div class="pricing-header text-center">
                                <h3 class="fw-bold">Silver</h3>
                                <p class="mb-0">Untuk pemula</p>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Rp 99.000</h2>
                                    <p class="text-muted">per bulan</p>
                                </div>
                                
                                <ul class="list-group list-group-flush mb-4">
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Subdomain (nama.buattokogame.com)</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Unlimited Game Integration</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Transaksi Tanpa Batas</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Support Email</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Custom Color Theme</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-times text-danger me-2"></i> Custom Domain</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-times text-danger me-2"></i> AI Content Generator</li>
                                </ul>
                                
                                <div class="d-grid">
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Mulai Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card pricing-card shadow-sm h-100">
                            <div class="pricing-header pricing-gold text-center">
                                <h3 class="fw-bold">Gold</h3>
                                <p class="mb-0">Untuk profesional</p>
                            </div>
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Rp 199.000</h2>
                                    <p class="text-muted">per bulan</p>
                                </div>
                                
                                <ul class="list-group list-group-flush mb-4">
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> <strong>Semua fitur Silver</strong></li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Custom Domain (.my.id)</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Content Generator dengan AI</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Social Media Content Planner</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Priority Support</li>
                                    <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> Brand Image Generator</li>
                                </ul>
                                
                                <div class="d-grid">
                                    <a href="{{ route('register') }}" class="btn btn-warning text-white">Mulai Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-5">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="fw-bold mb-3">Pertanyaan Umum</h2>
                    <p class="text-muted">Jawaban untuk pertanyaan yang sering ditanyakan</p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8" data-aos="fade-up">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                        Apa itu BuatTokoGame?
                                    </button>
                                </h2>
                                <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        BuatTokoGame adalah platform untuk membuat website toko game online dengan mudah dan cepat. Anda bisa membuat toko game Anda sendiri tanpa perlu keahlian teknis atau coding.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                        Bagaimana cara mendaftar?
                                    </button>
                                </h2>
                                <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Anda bisa mendaftar dengan mengklik tombol "Daftar Sekarang" di halaman ini. Setelah mengisi formulir pendaftaran, Anda akan langsung bisa membuat toko game online Anda.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                        Game apa saja yang didukung?
                                    </button>
                                </h2>
                                <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Kami mendukung berbagai game populer seperti Mobile Legends, Free Fire, PUBG Mobile, Call of Duty Mobile, Genshin Impact, dan masih banyak lagi. Kami terus menambahkan game baru secara rutin.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                        Berapa biaya untuk menggunakan platform ini?
                                    </button>
                                </h2>
                                <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Kami menawarkan dua paket: Silver (Rp 99.000/bulan) dan Gold (Rp 199.000/bulan). Anda bisa melihat perbandingan fitur di bagian Paket Membership.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                        Bagaimana sistem pembayarannya?
                                    </button>
                                </h2>
                                <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Kami menggunakan Xendit sebagai payment gateway yang mendukung berbagai metode pembayaran seperti transfer bank, e-wallet, kartu kredit, dan lainnya. Pembayaran aman dan instan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8" data-aos="fade-up">
                        <h2 class="fw-bold mb-4">Siap Memulai Bisnis Game Anda?</h2>
                        <p class="fs-5 mb-4">Daftar sekarang dan dapatkan toko game online siap pakai dalam hitungan menit!</p>
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h5 class="mb-4">BuatTokoGame</h5>
                        <p>Platform terbaik untuk memulai bisnis toko game online Anda dengan mudah dan cepat.</p>
                        <div class="social-icons mt-4">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="mb-4">Navigasi</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Home</a></li>
                            <li class="mb-2"><a href="#features" class="text-white text-decoration-none">Features</a></li>
                            <li class="mb-2"><a href="#pricing" class="text-white text-decoration-none">Pricing</a></li>
                            <li class="mb-2"><a href="#faq" class="text-white text-decoration-none">FAQ</a></li>
                            <li><a href="#contact" class="text-white text-decoration-none">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <h5 class="mb-4">Support</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Help Center</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Dokumentasi</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Status</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4" id="contact">
                        <h5 class="mb-4">Hubungi Kami</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@buattokogame.com</li>
                            <li class="mb-2"><i class="fas fa-phone me-2"></i> +62 812-3456-7890</li>
                            <li><i class="fas fa-map-marker-alt me-2"></i> Jakarta, Indonesia</li>
                        </ul>
                    </div>
                </div>
                <hr class="my-4 bg-light">
                <div class="text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} BuatTokoGame. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            
            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    </body>
</html>

<!-- resources/views/admin/games/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Game Management')

@section('page-title', 'Game Management')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Game List</h5>
                <a href="{{ route('admin.games.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add New Game
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="gamesTable">
                        <thead>
                            <tr>
                                <th width="60">Logo</th>
                                <th>Name</th>
                                <th>Services</th>
                                <th>Resellers</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this game? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#gamesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.games.index') }}",
            columns: [
                { data: 'logo', name: 'logo', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'services_count', name: 'services_count' },
                { data: 'reseller_games_count', name: 'reseller_games_count' },
                { data: 'status_badge', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
        
        // Delete Game
        var deleteId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').click(function() {
            $.ajax({
                url: "{{ url('admin/games') }}/" + deleteId,
                type: 'DELETE',
                success: function(data) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    });
                },
                error: function(error) {
                    $('#deleteModal').modal('hide');
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while deleting the game.'
                    });
                }
            });
        });
    });
</script>
@endpush