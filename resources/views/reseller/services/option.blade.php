<!-- resources/views/reseller/services/options.blade.php -->
@extends('layouts.reseller')

@section('title', 'Manage Service Options')

@section('page-title', 'Manage Options for ' . $service->name)

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.index') }}">Games</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.edit', $game->id) }}">{{ $game->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reseller.games.services', $game->id) }}">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $service->name }} Options</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $service->name }} Options</h5>
                <span class="badge bg-{{ $resellerGameService->is_active ? 'success' : 'secondary' }}">
                    {{ $resellerGameService->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i> 
                    Set your selling prices for each option. The base price is the wholesale price, and your selling price should include your profit margin.
                </div>
                
                <form action="{{ route('reseller.services.update-options', ['gameId' => $game->id, 'serviceId' => $service->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>Option</th>
                                    <th>Base Price</th>
                                    <th>Selling Price</th>
                                    <th style="width: 120px;">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($options as $option)
                                    @php
                                        $resellerOption = $option->resellerServiceOptions->first();
                                        $basePrice = $option->base_price;
                                        $sellingPrice = $resellerOption ? $resellerOption->selling_price : ($basePrice * (1 + ($resellerGameService->profit_margin / 100)));
                                        $profit = $sellingPrice - $basePrice;
                                        $profitPercent = $basePrice > 0 ? ($profit / $basePrice * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active_{{ $option->id }}" name="options[{{ $option->id }}][is_active]" value="1" {{ $resellerOption && $resellerOption->is_active ? 'checked' : '' }}>
                                                <input type="hidden" name="options[{{ $option->id }}][id]" value="{{ $option->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <label for="is_active_{{ $option->id }}" class="form-check-label">
                                                <strong>{{ $option->name }}</strong>
                                                @if($option->description)
                                                    <small class="d-block text-muted">{{ $option->description }}</small>
                                                @endif
                                            </label>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" value="{{ number_format($basePrice, 0, ',', '.') }}" disabled>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control selling-price" id="selling_price_{{ $option->id }}" name="options[{{ $option->id }}][selling_price]" value="{{ $sellingPrice }}" min="{{ $basePrice }}" step="100" data-base-price="{{ $basePrice }}">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="profit-amount">Rp {{ number_format($profit, 0, ',', '.') }}</span>
                                            <small class="d-block text-muted profit-percent">{{ number_format($profitPercent, 2) }}%</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">Bulk Set Profit</span>
                                    <input type="number" class="form-control" id="bulkProfitMargin" min="0" max="100" step="0.1" value="{{ $resellerGameService->profit_margin }}">
                                    <span class="input-group-text">%</span>
                                    <button type="button" class="btn btn-primary" id="applyBulkProfit">Apply</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select/Deselect All</label>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Save All Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mt-2 mb-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('reseller.games.services', $game->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Services
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update profit amount and percentage when selling price changes
        $('.selling-price').on('input', function() {
            const basePrice = parseFloat($(this).data('base-price'));
            const sellingPrice = parseFloat($(this).val());
            const profitAmount = sellingPrice - basePrice;
            const profitPercent = basePrice > 0 ? (profitAmount / basePrice * 100) : 0;
            
            const row = $(this).closest('tr');
            row.find('.profit-amount').text('Rp ' + profitAmount.toLocaleString('id-ID'));
            row.find('.profit-percent').text(profitPercent.toFixed(2) + '%');
        });
        
        // Apply bulk profit margin to all options
        $('#applyBulkProfit').click(function() {
            const profitMargin = parseFloat($('#bulkProfitMargin').val()) / 100;
            
            $('.selling-price').each(function() {
                const basePrice = parseFloat($(this).data('base-price'));
                const newSellingPrice = Math.ceil(basePrice * (1 + profitMargin) / 100) * 100; // Round up to nearest 100
                $(this).val(newSellingPrice).trigger('input');
            });
        });
        
        // Select/deselect all options
        $('#selectAll').change(function() {
            $('input[name^="options"][name$="[is_active]"]').prop('checked', $(this).prop('checked'));
        });
    });
</script>
@endpush