<!-- resources/views/admin/games/services/show.blade.php -->
@extends('layouts.admin')

@section('title', $service->name)

@section('page-title', $service->name)

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.index') }}">Games</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.show', $game->id) }}">{{ $game->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.index', $game->id) }}">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Service Information</h5>
                <div>
                    <a href="{{ route('admin.games.services.edit', ['game' => $game->id, 'service' => $service->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.games.services.options.index', ['game' => $game->id, 'service' => $service->id]) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-cogs me-1"></i> Manage Options
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="img-fluid rounded">
                        @else
                            <div class="bg-light rounded p-4 text-center text-muted">
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
                                <p class="mt-2 mb-0">No Image</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Name:</strong></td>
                                <td>{{ $service->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug:</strong></td>
                                <td>{{ $service->slug }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>
                                    @if($service->type === 'topup')
                                        Top Up (Diamond/Currency)
                                    @elseif($service->type === 'joki')
                                        Joki Rank Service
                                    @elseif($service->type === 'coaching')
                                        Coaching Service
                                    @elseif($service->type === 'formation')
                                        Formation Setup Service
                                    @else
                                        {{ ucfirst($service->type) }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Price Range:</strong></td>
                                <td>{{ $service->price_range ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($service->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>{{ $service->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated At:</strong></td>
                                <td>{{ $service->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="fw-bold">Description:</h6>
                    <p>{{ $service->description ?: 'No description available.' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Service Options ({{ $service->options->count() }})</h5>
            </div>
            <div class="card-body">
                @if($service->options->count() > 0)
                    <div class="list-group">
                        @foreach($service->options as $option)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $option->name }}</h6>
                                    <small class="text-muted">Rp {{ number_format($option->base_price, 0, ',', '.') }}</small>
                                </div>
                                <span class="badge {{ $option->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($option->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="mb-0">No options available for this service yet.</p>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.games.services.options.index', ['game' => $game->id, 'service' => $service->id]) }}" class="btn btn-primary w-100">
                    <i class="fas fa-cogs me-1"></i> Manage Options
                </a>
            </div>
        </div>
    </div>
</div>
@endsection