<!-- resources/views/admin/games/services/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Service')

@section('page-title', 'Edit Service')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.index') }}">Games</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.show', $game->id) }}">{{ $game->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.games.services.index', $game->id) }}">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{ $service->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Service Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.games.services.update', ['game' => $game->id, 'service' => $service->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Service Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Service Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="topup" {{ old('type', $service->type) == 'topup' ? 'selected' : '' }}>Top Up (Diamond/Currency)</option>
                                <option value="joki" {{ old('type', $service->type) == 'joki' ? 'selected' : '' }}>Joki Rank Service</option>
                                <option value="coaching" {{ old('type', $service->type) == 'coaching' ? 'selected' : '' }}>Coaching Service</option>
                                <option value="formation" {{ old('type', $service->type) == 'formation' ? 'selected' : '' }}>Formation Setup Service</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price_range" class="form-label">Price Range (Optional)</label>
                            <input type="text" class="form-control @error('price_range') is-invalid @enderror" id="price_range" name="price_range" value="{{ old('price_range', $service->price_range) }}" placeholder="e.g. Rp 10.000 - Rp 100.000">
                            @error('price_range')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Service Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <small class="text-muted">Recommended size: 400x400px. Max file size: 2MB.</small>
                            @if($service->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.games.services.index', $game->id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection