<!-- resources/views/admin/games/show.blade.php -->
@extends('layouts.admin')

@section('title', $game->name)

@section('page-title', $game->name)

@section('content')
<div class="row mt-4">
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Game Information</h5>
                <div>
                    <a href="{{ route('admin.games.edit', $game->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.games.services.index', $game->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-cogs me-1"></i> Manage Services
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if($game->logo)
                            <img src="{{ asset('storage/' . $game->logo) }}" alt="{{ $game->name }}" class="img-fluid rounded">
                        @else
                            <div class="bg-light rounded p-4 text-center text-muted">
                                <i class="fas fa-gamepad fa-5x"></i>
                                <p class="mt-2 mb-0">No Logo</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Name:</strong></td>
                                <td>{{ $game->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug:</strong></td>
                                <td>{{ $game->slug }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($game->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>{{ $game->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated At:</strong></td>
                                <td>{{ $game->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="fw-bold">Description:</h6>
                    <p>{{ $game->description ?: 'No description available.' }}</p>
                </div>
                
                @if($game->banner)
                    <div class="mt-4">
                        <h6 class="fw-bold">Banner:</h6>
                        <img src="{{ asset('storage/' . $game->banner) }}" alt="{{ $game->name }}" class="img-fluid rounded">
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Services ({{ $game->services->count() }})</h5>
            </div>
            <div class="card-body">
                @if($game->services->count() > 0)
                    <div class="list-group">
                        @foreach($game->services as $service)
                            <a href="{{ route('admin.games.services.show', ['game' => $game->id, 'service' => $service->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $service->name }}</h6>
                                    <small class="text-muted">{{ ucfirst($service->type) }} - {{ $service->options_count }} options</small>
                                </div>
                                <span class="badge {{ $service->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($service->status) }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="mb-0">No services available for this game yet.</p>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.games.services.create', $game->id) }}" class="btn btn-primary w-100">
                    <i class="fas fa-plus-circle me-1"></i> Add New Service
                </a>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Resellers</h5>
            </div>
            <div class="card-body">
                @if($game->resellerGames->count() > 0)
                    <div class="list-group">
                        @foreach($game->resellerGames as $resellerGame)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $resellerGame->reseller->store_name }}</h6>
                                    <small class="text-muted">{{ $resellerGame->reseller->user->email }}</small>
                                </div>
                                <span class="badge {{ $resellerGame->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $resellerGame->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-store fa-3x text-muted mb-3"></i>
                        <p class="mb-0">No resellers are offering this game yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection