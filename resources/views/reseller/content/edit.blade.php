<!-- resources/views/reseller/content/edit.blade.php -->
@extends('layouts.reseller')

@section('title', 'Edit Content')

@section('page-title', 'Edit Content')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Content</h5>
                <a href="{{ route('reseller.content.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Content List
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('reseller.content.update', $content->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $content->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="content_text" class="form-label">Content</label>
                                <textarea class="form-control @error('content_text') is-invalid @enderror" id="content_text" name="content_text" rows="8" required>{{ old('content_text', $content->content) }}</textarea>
                                @error('content_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">Content Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="post" {{ old('type', $content->type) == 'post' ? 'selected' : '' }}>Full Post</option>
                                    <option value="caption" {{ old('type', $content->type) == 'caption' ? 'selected' : '' }}>Caption</option>
                                    <option value="image" {{ old('type', $content->type) == 'image' ? 'selected' : '' }}>Image & Caption</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="platform" class="form-label">Platform</label>
                                <select class="form-select @error('platform') is-invalid @enderror" id="platform" name="platform" required>
                                    <option value="instagram" {{ old('platform', $content->platform) == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="facebook" {{ old('platform', $content->platform) == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="twitter" {{ old('platform', $content->platform) == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                    <option value="all" {{ old('platform', $content->platform) == 'all' ? 'selected' : '' }}>All Platforms</option>
                                </select>
                                @error('platform')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="draft" {{ old('status', $content->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $content->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status', $content->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 {{ old('status', $content->status) != 'scheduled' ? 'd-none' : '' }}" id="scheduledDateContainer">
                                <label for="scheduled_at" class="form-label">Schedule Date</label>
                                <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $content->scheduled_at ? date('Y-m-d\TH:i', strtotime($content->scheduled_at)) : '') }}">
                                @error('scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if($content->type == 'image')
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                                    <small class="text-muted">Leave empty to keep current image</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($content->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $content->image) }}" alt="{{ $content->title }}" class="img-fluid rounded">
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('reseller.content.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Content</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle scheduled date field
        $('#status').change(function() {
            if ($(this).val() === 'scheduled') {
                $('#scheduledDateContainer').removeClass('d-none');
                $('#scheduled_at').prop('required', true);
            } else {
                $('#scheduledDateContainer').addClass('d-none');
                $('#scheduled_at').prop('required', false);
            }
        });
    });
</script>
@endpush