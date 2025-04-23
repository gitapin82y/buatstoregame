<!-- resources/views/store/track.blade.php -->
@extends('layouts.store')

@section('title', 'Track Order')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary-custom text-white">
                        <h4 class="mb-0">Track Your Order</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">Enter your invoice number to track the status of your order.</p>
                        
                        <form action="{{ route('store.track.post', $reseller->subdomain ?? $reseller->custom_domain) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control form-control-lg @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" placeholder="e.g. INV-1234567890" required>
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-custom btn-lg">
                                    <i class="fas fa-search me-2"></i> Track Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection