<!-- resources/views/admin/games/services/options/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Option')

@section('page-title', 'Edit Option')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.index') }}">Games</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.show', $game->id) }}">{{ $game->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.index', $game->id) }}">Services</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.show', ['game' => $game->id, 'service' => $service->id]) }}">{{ $service->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.options.index', ['game' => $game->id, 'service' => $service->id]) }}">Options</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{ $option->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Option Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.games.services.options.update', ['game' => $game->id, 'service' => $service->id, 'option' => $option->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Option Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $option->name) }}" required>
                            <small class="text-muted">E.g. "100 Diamonds", "Epic to Legend", "Full Coach Session"</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="base_price" class="form-label">Base Price (Rp)</label>
                            <input type="number" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price', $option->base_price) }}" min="0" step="0.01" required>
                            <small class="text-muted">The wholesale price before reseller markup</small>
                            @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="api_code" class="form-label">API Code (Optional)</label>
                            <input type="text" class="form-control @error('api_code') is-invalid @enderror" id="api_code" name="api_code" value="{{ old('api_code', $option->api_code) }}">
                            <small class="text-muted">The code used for API integration</small>
                            @error('api_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $option->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $option->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $option->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.games.services.options.index', ['game' => $game->id, 'service' => $service->id]) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Option</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection