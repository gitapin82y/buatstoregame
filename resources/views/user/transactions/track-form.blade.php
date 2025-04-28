<!-- resources/views/user/transactions/track-form.blade.php -->
@extends('layouts.user')

@section('title', 'Track Order')

@section('page-title', 'Track Order')

@section('content')
<div class="row mt-4">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Track Your Order</h5>
            </div>
            <div class="card-body">
                <p class="mb-4">Enter your invoice number to track the status of your order. You can find the invoice number in your order confirmation email.</p>
                
                <form action="{{ route('user.transactions.track') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control form-control-lg @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" placeholder="e.g. INV-1234567890" required>
                        </div>
                        @error('invoice_number')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i> Track Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('user.transactions.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Back to Transaction History
            </a>
        </div>
    </div>
</div>
@endsection