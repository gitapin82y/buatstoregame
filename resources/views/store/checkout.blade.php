<!-- resources/views/store/checkout.blade.php -->
@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
<!-- Checkout Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('store.index', $reseller->subdomain ?? $reseller->custom_domain) }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('store.game', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug]) }}">{{ $game->name }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('store.service', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug, 'serviceSlug' => $service->slug]) }}">{{ $service->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                    </ol>
                </nav>
                
                <h1 class="fw-bold mb-4">Checkout</h1>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Form -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Order Details</h5>
                        
                        <form id="checkoutForm" action="{{ route('store.checkout.process', ['domain' => $reseller->subdomain ?? $reseller->custom_domain, 'gameSlug' => $game->slug, 'serviceSlug' => $service->slug]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="option_id" value="{{ $option->id }}">
                            
                            <!-- Customer Information -->
                            <div class="mb-4">
                                <h6 class="mb-3">Customer Information</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone_number ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Game Information -->
                            <div class="mb-4">
                                <h6 class="mb-3">Game Information</h6>
                                
                                @if($service->type === 'topup')
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label">User ID</label>
                                            <input type="text" class="form-control" id="user_id" name="user_id" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="server_id" class="form-label">Server ID (if applicable)</label>
                                            <input type="text" class="form-control" id="server_id" name="server_id">
                                        </div>
                                    </div>
                                @elseif($service->type === 'joki')
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label">User ID / Account</label>
                                            <input type="text" class="form-control" id="user_id" name="user_id" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <small class="text-muted">Your password is securely encrypted and will only be used for this service.</small>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="notes" class="form-label">Notes / Instructions</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label">User ID / Account</label>
                                            <input type="text" class="form-control" id="user_id" name="user_id" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="notes" class="form-label">Notes / Instructions</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Voucher/Discount -->
                            <div class="mb-4">
                                <h6 class="mb-3">Voucher</h6>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="voucher_code" name="voucher_code" placeholder="Enter voucher code">
                                    <button class="btn btn-outline-secondary" type="button" id="checkVoucher">Apply</button>
                                </div>
                                <div id="voucherMessage" class="mt-2"></div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary-custom btn-lg">
                                    <i class="fas fa-lock me-2"></i> Complete Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Game:</span>
                            <span>{{ $game->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Service:</span>
                            <span>{{ $service->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Option:</span>
                            <span>{{ $option->name }}</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Price:</span>
                            <span>Rp {{ number_format($resellerOption->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none !important;">
                            <span>Discount:</span>
                            <span>- Rp <span id="discountAmount">0</span></span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold" id="totalAmount">Rp {{ number_format($resellerOption->selling_price, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Your order will be processed immediately after payment is completed.
                        </div>
                        
                        <div class="mt-3">
                            <h6>Payment Methods:</h6>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge bg-light text-dark p-2"><i class="fas fa-university me-1"></i> Bank Transfer</span>
                                <span class="badge bg-light text-dark p-2"><i class="fas fa-wallet me-1"></i> E-Wallet</span>
                                <span class="badge bg-light text-dark p-2"><i class="fab fa-cc-visa me-1"></i> Credit Card</span>
                                <span class="badge bg-light text-dark p-2"><i class="fas fa-store me-1"></i> Retail</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const originalPrice = {{ $resellerOption->selling_price }};
        let finalPrice = originalPrice;
        
        // Check voucher
        $('#checkVoucher').click(function() {
            const voucherCode = $('#voucher_code').val();
            
            if (!voucherCode) {
                $('#voucherMessage').html('<div class="text-danger">Please enter a voucher code.</div>');
                return;
            }
            
            $('#voucherMessage').html('<div class="text-muted">Checking voucher...</div>');
            
            $.ajax({
                url: "{{ route('store.voucher.check', $reseller->subdomain ?? $reseller->custom_domain) }}",
                type: 'POST',
                data: {
                    code: voucherCode,
                    amount: originalPrice
                },
                success: function(response) {
                    if (response.success) {
                        $('#voucherMessage').html('<div class="text-success">Voucher applied successfully!</div>');
                        $('#discountRow').show();
                        $('#discountAmount').text(number_format(response.discount, 0, ',', '.'));
                        finalPrice = response.final_amount;
                        $('#totalAmount').text('Rp ' + number_format(finalPrice, 0, ',', '.'));
                    } else {
                        $('#voucherMessage').html('<div class="text-danger">' + response.message + '</div>');
                        resetVoucher();
                    }
                },
                error: function() {
                    $('#voucherMessage').html('<div class="text-danger">Error checking voucher. Please try again.</div>');
                    resetVoucher();
                }
            });
        });
        
        function resetVoucher() {
            $('#discountRow').hide();
            $('#discountAmount').text('0');
            finalPrice = originalPrice;
            $('#totalAmount').text('Rp ' + number_format(finalPrice, 0, ',', '.'));
        }
        
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }
    });
</script>
@endpush
