<!-- resources/views/admin/resellers/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manage Resellers')

@section('page-title', 'Manage Resellers')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resellers List</h5>
                <a href="{{ route('admin.resellers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add New Reseller
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="resellersTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Store Name</th>
                                <th>Membership</th>
                                <th>Status</th>
                                <th>Domain</th>
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
                Are you sure you want to delete this reseller? This action cannot be undone and will delete all related data.
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
        var table = $('#resellersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.resellers.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'store_name', name: 'store_name' },
                { data: 'membership', name: 'membership' },
                { data: 'status', name: 'status' },
                { data: 'domain', name: 'domain' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
        
        // Delete Reseller
        var deleteId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').click(function() {
            $.ajax({
                url: "{{ url('admin/resellers') }}/" + deleteId,
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
                        text: 'An error occurred while deleting the reseller.'
                    });
                }
            });
        });
    });
</script>
@endpush

<!-- resources/views/admin/resellers/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Reseller Details')

@section('page-title', 'Reseller Details')

@section('content')
<div class="row mt-4">
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Reseller Information</h5>
                <div>
                    <a href="{{ route('admin.resellers.edit', $reseller->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <h6 class="fw-bold">User Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="200"><strong>Name:</strong></td>
                                <td>{{ $reseller->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $reseller->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone Number:</strong></td>
                                <td>{{ $reseller->phone_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($reseller->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Registered At:</strong></td>
                                <td>{{ $reseller->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    @if($profile)
                    <div class="col-md-12 mb-4">
                        <h6 class="fw-bold">Store Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="200"><strong>Store Name:</strong></td>
                                <td>{{ $profile->store_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Subdomain:</strong></td>
                                <td>
                                    <a href="https://{{ $profile->subdomain }}.buattokogame.com" target="_blank">
                                        {{ $profile->subdomain }}.buattokogame.com
                                    </a>
                                </td>
                            </tr>
                            @if($profile->custom_domain)
                            <tr>
                                <td><strong>Custom Domain:</strong></td>
                                <td>
                                    <a href="https://{{ $profile->custom_domain }}" target="_blank">
                                        {{ $profile->custom_domain }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Membership Level:</strong></td>
                                <td>{{ ucfirst($profile->membership_level) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Membership Status:</strong></td>
                                <td>
                                    @if($profile->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($profile->isGracePeriod())
                                        <span class="badge bg-warning">Grace Period</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Expires At:</strong></td>
                                <td>{{ $profile->membership_expires_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Balance:</strong></td>
                                <td>Rp {{ number_format($profile->balance, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-12">
                        <h6 class="fw-bold">Store Description</h6>
                        <p>{{ $profile->store_description ?: 'No description available.' }}</p>
                    </div>
                    @else
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This user does not have a reseller profile yet.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Games Offered -->
        @if($profile && $profile->resellerGames->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Games Offered ({{ $profile->resellerGames->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Game</th>
                                <th>Status</th>
                                <th>Profit Margin</th>
                                <th>Services</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profile->resellerGames as $resellerGame)
                            <tr>
                                <td>{{ $resellerGame->game->name }}</td>
                                <td>
                                    @if($resellerGame->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $resellerGame->profit_margin }}%</td>
                                <td>{{ $resellerGame->resellerGameServices->count() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <!-- Stats Widget -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4>{{ $stats['total_transactions'] }}</h4>
                        <small class="text-muted">Total Transactions</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4>{{ $stats['total_paid_transactions'] }}</h4>
                        <small class="text-muted">Paid Transactions</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4>Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</h4>
                        <small class="text-muted">Total Sales</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4>Rp {{ number_format($stats['total_profit'], 0, ',', '.') }}</h4>
                        <small class="text-muted">Total Profit</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ $stats['total_games'] }}</h4>
                        <small class="text-muted">Games</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ $stats['pending_withdrawals'] }}/{{ $stats['total_withdrawals'] }}</h4>
                        <small class="text-muted">Pending/Total Withdrawals</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions Widget -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($profile)
                <form action="{{ route('admin.resellers.extend-membership', $reseller->id) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label for="extend_package_id" class="form-label">Extend Membership</label>
                        <select class="form-select" id="extend_package_id" name="package_id" required>
                            <option value="">Select a package</option>
                            @foreach($membershipHistory as $package)
                                <option value="{{ $package->id }}">
                                    {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }} ({{ $package->duration_days }} days)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="duration_days" class="form-label">Duration (Days)</label>
                        <input type="number" class="form-control" id="duration_days" name="duration_days" value="30" min="30" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Extend Membership</button>
                    </div>
                </form>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ $profile->getDomainUrl() }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i> Visit Store
                    </a>
                    <a href="{{ route('admin.transactions.index') }}?reseller_id={{ $profile->id }}" class="btn btn-outline-info">
                        <i class="fas fa-list me-2"></i> View Transactions







                        Saya mengerti situasi Anda. Ketika prompt sebelumnya mencapai batas dan Anda harus memulai obrolan baru, memang dapat mengganggu kelanjutan proyek yang hampir selesai. Berikut cara efektif untuk melanjutkan proyek dengan saya:

                        Berikan konteks singkat tentang proyek
                        
                        Jelaskan ini adalah kelanjutan dari proyek sebelumnya
                        Sebutkan nama proyek (contoh: BuatTokoGame.com)
                        Jelaskan singkat teknologi yang digunakan (Laravel, dll)
                        
                        
                        Rangkum progress sejauh ini
                        
                        Apa saja yang sudah dibuat?
                        Apa bagian terakhir yang dikerjakan?
                        
                        
                        Lampirkan contoh kode penting
                        
                        Struktur database/model
                        Controller terkait






                        "Saya ingin membuat website platform reseller game(pengguna jika ingin bisnis membuat store game dapat daftar membership dengan biaya yang telah ditentukan admin perbulannya dengan pembayaran payment gateway xendit pengguna dapat publish website domainnya secara langsung tanpa membuatnya dari 0) teknologi yang kami gunakan yaitu html,css,js,bootstrap,laravel.

Tolong bantu saya dengan membuat proyek ini menggunakan laravel dari 0 hingga selesai, proyek ini mencakup 3 role yaitu admin untuk manajemen keseluruhan website, reseller untuk mengelola storenya sendiri, dan terakhir role user biasa yang melakukan action seperti membeli diamond, joki push rank support semua game dan jasa lainnya seperti racikan pemain efootbal yang lagi ramai saat ini pada website store milik reseller, adapun fitur lainnnya yaitu :
role admin
1. dashboard admin
2. manajemen user store reseller.
3. Integrasi API game(anda bebas menggunakan API darimana dan berikan saya intruksinya)
4. support pengiriman email notifikasi (request withdrawal pencairan dana, terdapat user yang join sebagai reseller, terdapat reseller yang masa aktif membership mau habis atau sudah habis)
5. manajemen transaksi.
6. manajemen masa aktif reseller membership dan domain store reseller.
7. 
8. manajemen game 
9. manajemen jasa lainnya seperti jasa joki push rank, jasa racik formasi tim dll.
10. request withdrawal dari reseller untuk pencairan dana
11. 
1. manajement user store reseller (role admin).
2. dashboard admin dan dashboard untuk store reseller
3. Integrasi API game(anda bebas menggunakan API darimana dan berikan saya intruksinya)
4. solusi untuk mengelola berbagai game yang berbeda dalam satu platform
5. sistem keamanan dan autentikasi
6. withdrawl untuk reseller store
7. generate migration,model dan controller sesuai data dan fitur yang dibutuhkan.
                        Contoh view yang sudah jadi (sebagai referensi style)
                        
                        
                        Tentukan dengan jelas apa yang perlu dilanjutkan
                        
                        File view mana yang harus dibuat?
                        Fitur spesifik apa yang harus dikerjakan?
                        
                        
                        
                        Contoh prompt lanjutan yang efektif:
                        "Saya melanjutkan proyek BuatTokoGame.com yang menggunakan Laravel. Sebelumnya kita sudah membuat model Game, GameCategory, dan controller AdminGameController. Kita juga sudah membuat view untuk dashboard admin. Sekarang saya butuh bantuan untuk melanjutkan membuat view admin untuk manajemen game seperti admin/games/index.blade.php, form create dan edit. Berikut struktur model Game: [masukkan kode]. Dan ini contoh view yang sudah ada: [masukkan kode]."






                        "Saya ingin membuat website platform reseller game(pengguna jika ingin bisnis membuat store game dapat daftar membership dengan biaya yang telah ditentukan admin perbulannya dengan pembayaran payment gateway xendit pengguna dapat publish website domainnya secara langsung tanpa membuatnya dari 0) teknologi yang kami gunakan yaitu html,css,js,bootstrap,laravel.

Tolong bantu saya dengan membuat proyek ini menggunakan laravel dari 0 hingga selesai, proyek ini mencakup 3 role yaitu admin untuk manajemen keseluruhan website, reseller untuk mengelola storenya sendiri, dan terakhir role user biasa yang melakukan action seperti membeli diamond, joki push rank support semua game dan jasa lainnya seperti racikan pemain efootbal yang lagi ramai saat ini pada website store milik reseller, adapun fitur lainnnya yaitu :
role admin
1. dashboard admin
2. manajemen user store reseller.
3. Integrasi API game(anda bebas menggunakan API darimana dan berikan saya intruksinya)
4. support pengiriman email notifikasi (request withdrawal pencairan dana, terdapat user yang join sebagai reseller, terdapat reseller yang masa aktif membership mau habis atau sudah habis)
5. manajemen transaksi.
6. manajemen masa aktif reseller membership dan domain store reseller.
7. 
8. manajemen game 
9. manajemen jasa lainnya seperti jasa joki push rank, jasa racik formasi tim dll.
10. request withdrawal dari reseller untuk pencairan dana
11. 
1. manajement user store reseller (role admin).
2. dashboard admin dan dashboard untuk store reseller
3. Integrasi API game(anda bebas menggunakan API darimana dan berikan saya intruksinya)
4. solusi untuk mengelola berbagai game yang berbeda dalam satu platform
5. sistem keamanan dan autentikasi
6. withdrawl untuk reseller store
7. generate migration,model dan controller sesuai data dan fitur yang dibutuhkan.