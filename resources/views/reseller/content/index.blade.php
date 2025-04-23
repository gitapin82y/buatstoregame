<!-- resources/views/reseller/content/index.blade.php -->
@extends('layouts.reseller')

@section('title', 'Content Generator')

@section('page-title', 'Content Generator')

@section('content')
<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Generate Content</h5>
            </div>
            <div class="card-body">
                <form id="generateForm">
                    <div class="mb-3">
                        <label for="prompt" class="form-label">What would you like to create?</label>
                        <textarea class="form-control" id="prompt" name="prompt" rows="3" placeholder="e.g. Create a promotional post for Mobile Legends diamonds sale" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="platform" class="form-label">Platform</label>
                            <select class="form-select" id="platform" name="platform" required>
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="twitter">Twitter</option>
                                <option value="all">All Platforms</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Content Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="caption">Caption</option>
                                <option value="post">Full Post</option>
                                <option value="image">Image & Caption</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="game_id" class="form-label">Game (Optional)</label>
                        <select class="form-select" id="game_id" name="game_id">
                            <option value="">Select a game</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}">{{ $game->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="generateBtn">
                            <i class="fas fa-magic me-2"></i> Generate Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Content Calendar</h5>
            </div>
            <div class="card-body">
                <p>Generate a 7-day content calendar for your social media.</p>
                
                <form id="calendarForm">
                    <div class="mb-3">
                        <label for="calendar_game_id" class="form-label">Game</label>
                        <select class="form-select" id="calendar_game_id" name="calendar_game_id" required>
                            <option value="">Select a game</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}">{{ $game->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success" id="calendarBtn">
                            <i class="fas fa-calendar-alt me-2"></i> Generate Calendar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Generated Content</h5>
                <button type="button" class="btn btn-sm btn-primary d-none" id="saveContentBtn">
                    <i class="fas fa-save me-1"></i> Save Content
                </button>
            </div>
            <div class="card-body">
                <div id="loadingContent" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Generating content, please wait...</p>
                </div>
                
                <div id="emptyContent" class="text-center py-5">
                    <i class="fas fa-magic fa-3x text-muted mb-3"></i>
                    <p>Your generated content will appear here</p>
                </div>
                
                <div id="generatedContentContainer" class="d-none">
                    <div id="generatedImageContainer" class="text-center mb-4 d-none">
                        <img id="generatedImage" src="" alt="Generated Image" class="img-fluid rounded mb-2">
                    </div>
                    
                    <div id="generatedTextContainer">
                        <div id="generatedContent" class="border rounded p-3 bg-light"></div>
                    </div>
                </div>
                
                <div id="calendarContainer" class="d-none">
                    <div id="generatedCalendar" class="border rounded p-3 bg-light"></div>
                </div>
                
                <!-- Save Content Modal -->
                <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="saveModalLabel">Save Content</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="saveForm">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content_platform" class="form-label">Platform</label>
                                        <select class="form-select" id="content_platform" name="content_platform" required>
                                            <option value="instagram">Instagram</option>
                                            <option value="facebook">Facebook</option>
                                            <option value="twitter">Twitter</option>
                                            <option value="all">All Platforms</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content_status" class="form-label">Status</label>
                                        <select class="form-select" id="content_status" name="content_status" required>
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="scheduled">Scheduled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3 d-none" id="scheduledDateContainer">
                                        <label for="scheduled_at" class="form-label">Schedule Date</label>
                                        <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at">
                                    </div>
                                    
                                    <input type="hidden" id="content_type" name="content_type">
                                    <input type="hidden" id="content_text" name="content_text">
                                    <input type="hidden" id="content_image" name="content_image">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Content</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshContentBtn">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="contentTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Platform</th>
                                <th>Status</th>
                                <th>Created</th>
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

<!-- View Content Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewImageContainer" class="text-center mb-4 d-none">
                    <img id="viewImage" src="" alt="Content Image" class="img-fluid rounded mb-2">
                </div>
                
                <div id="viewTextContainer">
                    <div id="viewContent" class="border rounded p-3 bg-light"></div>
                </div>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>Platform:</strong> <span id="viewPlatform"></span></span>
                        <span><strong>Status:</strong> <span id="viewStatus"></span></span>
                    </div>
                    <div id="viewScheduledContainer" class="d-none mt-2">
                        <strong>Scheduled for:</strong> <span id="viewScheduled"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteContentModal" tabindex="-1" aria-labelledby="deleteContentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteContentModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this content? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteContent">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var contentTable = $('#contentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reseller.content.index') }}",
            columns: [
                { data: 'title', name: 'title' },
                { data: 'type_badge', name: 'type' },
                { data: 'platform_badge', name: 'platform' },
                { data: 'status_badge', name: 'status' },
                { data: 'date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[4, 'desc']]
        });
        
        // Generate Content
        $('#generateForm').submit(function(e) {
            e.preventDefault();
            
            $('#emptyContent').addClass('d-none');
            $('#generatedContentContainer').addClass('d-none');
            $('#calendarContainer').addClass('d-none');
            $('#loadingContent').removeClass('d-none');
            $('#saveContentBtn').addClass('d-none');
            
            const data = {
                prompt: $('#prompt').val(),
                platform: $('#platform').val(),
                type: $('#type').val(),
                game_id: $('#game_id').val(),
            };
            
            $.ajax({
                url: "{{ route('reseller.content.generate') }}",
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#loadingContent').addClass('d-none');
                    
                    if (response.success) {
                        $('#generatedContentContainer').removeClass('d-none');
                        $('#saveContentBtn').removeClass('d-none');
                        
                        if (response.type === 'image' && response.image_url) {
                            $('#generatedImageContainer').removeClass('d-none');
                            $('#generatedImage').attr('src', response.image_url);
                            $('#content_image').val(response.image_url);
                        } else {
                            $('#generatedImageContainer').addClass('d-none');
                        }
                        
                        $('#generatedContent').html(formatText(response.content));
                        $('#content_text').val(response.content);
                        $('#content_type').val(response.type);
                        $('#content_platform').val(data.platform);
                    } else {
                        $('#emptyContent').removeClass('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    $('#loadingContent').addClass('d-none');
                    $('#emptyContent').removeClass('d-none');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while generating content. Please try again later.'
                    });
                }
            });
        });
        
        // Generate Calendar
        $('#calendarForm').submit(function(e) {
            e.preventDefault();
            
            $('#emptyContent').addClass('d-none');
            $('#generatedContentContainer').addClass('d-none');
            $('#calendarContainer').addClass('d-none');
            $('#loadingContent').removeClass('d-none');
            $('#saveContentBtn').addClass('d-none');
            
            const gameId = $('#calendar_game_id').val();
            
            if (!gameId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a game'
                });
                return;
            }
            
            $.ajax({
                url: "{{ route('reseller.content.generate-calendar') }}",
                type: 'POST',
                data: { game_id: gameId },
                success: function(response) {
                    $('#loadingContent').addClass('d-none');
                    
                    if (response.success) {
                        $('#calendarContainer').removeClass('d-none');
                        $('#generatedCalendar').html(formatText(response.calendar));
                    } else {
                        $('#emptyContent').removeClass('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    $('#loadingContent').addClass('d-none');
                    $('#emptyContent').removeClass('d-none');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while generating calendar. Please try again later.'
                    });
                }
            });
        });
        
        // Save Content Button
        $('#saveContentBtn').click(function() {
            // Prepopulate title with a suggestion
            $('#title').val('Content for ' + $('#platform option:selected').text() + ' - ' + new Date().toLocaleDateString());
            $('#saveModal').modal('show');
        });
        
        // Toggle scheduled date field
        $('#content_status').change(function() {
            if ($(this).val() === 'scheduled') {
                $('#scheduledDateContainer').removeClass('d-none');
                $('#scheduled_at').prop('required', true);
            } else {
                $('#scheduledDateContainer').addClass('d-none');
                $('#scheduled_at').prop('required', false);
            }
        });
        
        // Save Content Form
        $('#saveForm').submit(function(e) {
            e.preventDefault();
            
            const data = {
                title: $('#title').val(),
                content: $('#content_text').val(),
                image: $('#content_image').val(),
                type: $('#content_type').val(),
                platform: $('#content_platform').val(),
                status: $('#content_status').val(),
                scheduled_at: $('#content_status').val() === 'scheduled' ? $('#scheduled_at').val() : null,
            };
            
            $.ajax({
                url: "{{ route('reseller.content.store') }}",
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#saveModal').modal('hide');
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        
                        contentTable.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    $('#saveModal').modal('hide');
                    
                    let errorMessage = 'An error occurred while saving content.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        });
        
        // Refresh Content Table
        $('#refreshContentBtn').click(function() {
            contentTable.ajax.reload();
        });
        
        // View Content
        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: "{{ url('reseller/content') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const content = response.content;
                        
                        $('#viewModalLabel').text(content.title);
                        
                        if (content.image && content.type === 'image') {
                            $('#viewImageContainer').removeClass('d-none');
                            $('#viewImage').attr('src', response.image_url);
                        } else {
                            $('#viewImageContainer').addClass('d-none');
                        }
                        
                        $('#viewContent').html(formatText(content.content));
                        $('#viewPlatform').text(formatPlatform(content.platform));
                        $('#viewStatus').text(formatStatus(content.status));
                        
                        if (content.status === 'scheduled' && content.scheduled_at) {
                            $('#viewScheduledContainer').removeClass('d-none');
                            $('#viewScheduled').text(formatDate(content.scheduled_at));
                        } else {
                            $('#viewScheduledContainer').addClass('d-none');
                        }
                        
                        $('#viewModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while loading content details.'
                    });
                }
            });
        });
        
        // Delete Content
        var deleteContentId;
        
        $(document).on('click', '.btn-delete', function() {
            deleteContentId = $(this).data('id');
            $('#deleteContentModal').modal('show');
        });
        
        $('#confirmDeleteContent').click(function() {
            $.ajax({
                url: "{{ url('reseller/content') }}/" + deleteContentId,
                type: 'DELETE',
                success: function(response) {
                    $('#deleteContentModal').modal('hide');
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        
                        contentTable.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    $('#deleteContentModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting content.'
                    });
                }
            });
        });
        
        // Helper Functions
        function formatText(text) {
            if (!text) return '';
            
            // Convert line breaks to <br>
            text = text.replace(/\n/g, '<br>');
            
            // Highlight hashtags
            text = text.replace(/#(\w+)/g, '<span class="text-primary">#$1</span>');
            
            // Highlight mentions
            text = text.replace(/@(\w+)/g, '<span class="text-info">@$1</span>');
            
            return text;
        }
        
        function formatPlatform(platform) {
            switch (platform) {
                case 'instagram': return 'Instagram';
                case 'facebook': return 'Facebook';
                case 'twitter': return 'Twitter';
                default: return 'All Platforms';
            }
        }
        
        function formatStatus(status) {
            switch (status) {
                case 'published': return 'Published';
                case 'scheduled': return 'Scheduled';
                default: return 'Draft';
            }
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString();
        }
    });
</script>
@endpush