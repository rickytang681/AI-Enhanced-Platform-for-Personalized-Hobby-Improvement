@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <!-- Header with Resource Library title and My Resources button -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Resource Library</h4>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#myResourcesModal">
                        <i class="bi bi-folder-fill me-1"></i> My Resources
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="filter-section mb-4">
                    <form id="search-form" action="{{ route('library') }}" method="GET" class="mb-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Search resources..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch">
                                    <i class="bi bi-funnel"></i> Filters
                                </button>
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>

                        <!-- Advanced Search Options -->
                        <div class="collapse mb-3" id="advancedSearch">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Date Range</label>
                                        <select name="date_filter" class="form-select">
                                            <option value="">Any Time</option>
                                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                                            <option value="year" {{ request('date_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Resource Type</label>
                                        <select name="type" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Text</option>
                                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label">Search In</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="search_in[]" 
                                                       value="title" 
                                                       id="searchTitle"
                                                       {{ in_array('title', request('search_in', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="searchTitle">Title</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="search_in[]" 
                                                       value="description" 
                                                       id="searchDesc"
                                                       {{ in_array('description', request('search_in', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="searchDesc">Description</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="search_in[]" 
                                                       value="content" 
                                                       id="searchContent"
                                                       {{ in_array('content', request('search_in', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="searchContent">Content</label>
                                            </div>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="saved" 
                                           value="true" 
                                           id="savedFilter"
                                           {{ request('saved') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="savedFilter">Show Saved Only</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            
                <!-- Upload Button -->
                <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    Upload New Resource
                </button>

                <!-- Categories & Levels -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-4">
                            <h5>Categories</h5>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($categories as $category)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input category-filter" 
                                               value="{{ $category }}" id="cat-{{ $loop->index }}"
                                               {{ request('category') == $category ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat-{{ $loop->index }}">{{ $category }}</label>
                                    </div>
                                @endforeach
                    </div>
                </div>

                        <div>
                            <h5>Level</h5>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($subcategories as $subcategory)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input subcategory-filter"
                                               value="{{ $subcategory }}" id="sub-{{ $loop->index }}"
                                               {{ request('subcategory') == $subcategory ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sub-{{ $loop->index }}">{{ $subcategory }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="mb-4">
                    <select class="form-select" id="sort-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular (Most Likes)</option>
                        <option value="rated" {{ request('sort') == 'rated' ? 'selected' : '' }}>Highly Rated (Best Average Rating)</option>
                    </select>
                </div>

                <!-- Resources List -->
                <div class="resources-list">
                    @foreach($items as $item)
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title mb-3">
                                        <a href="#" 
                                           class="view-content text-decoration-none text-dark" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#contentModal"
                                           data-title="{{ $item->title }}"
                                           data-type="{{ $item->type }}"
                                           data-content="{{ $item->content }}"
                                           data-video="{{ $item->video_url }}"
                                           data-description="{{ $item->description }}">
                                            {{ $item->title }}
                                        </a>
                                    </h5>
                                    <div class="d-flex gap-2">
                                        @if($item->file_path)
                                            <a href="{{ route('library.download', $item->id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Download Resource">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                        <span class="badge bg-secondary">{{ $item->type }}</span>
                                    </div>
                                </div>
                                
                                <p class="card-text text-muted mb-3">{{ $item->description }}</p>

                                <!-- Resource Content -->
                                <div class="resource-content mb-4">
                                    @if($item->type === 'video')
                                        <div class="ratio ratio-16x9 mb-3">
                                            <iframe src="{{ $item->video_url }}" 
                                                    allowfullscreen 
                                                    class="rounded"></iframe>
                                        </div>
                                    @else
                                        <div class="content-text border rounded p-3 mb-3" style="max-height: 200px; overflow-y: auto;">
                                            {!! nl2br(e($item->content)) !!}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="rating-container">
                                        <div class="stars" data-user-rating="{{ $item->userRating(auth()->user()) ? $item->userRating(auth()->user())->rating : 0 }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star rating-star {{ $item->userRating(auth()->user()) && $item->userRating(auth()->user())->rating >= $i ? 'bi-star-fill' : '' }}"
                                                   data-rating="{{ $i }}"
                                                   data-item="{{ $item->id }}"></i>
                                            @endfor
                                            <span class="ms-2 text-muted small">
                                                ({{ number_format($item->average_rating, 1) }} / {{ $item->rating_count }} {{ Str::plural('rating', $item->rating_count) }})
                                            </span>
                                        </div>
                                    </div>
                                    <div class="category-badges">
                                        <span class="badge bg-primary">{{ $item->category }}</span>
                                        <span class="badge bg-info">{{ $item->subcategory }}</span>
                                        <span class="badge bg-secondary">{{ ucfirst($item->type) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-2">
                                        <!-- Save Button -->
                                        <button class="btn btn-sm {{ $item->isSavedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }} save-btn"
                                                data-item="{{ $item->id }}"
                                                title="{{ $item->isSavedBy(auth()->user()) ? 'Remove from saved' : 'Save for later' }}">
                                            <i class="bi bi-bookmark{{ $item->isSavedBy(auth()->user()) ? '-fill' : '' }}"></i>
                                        </button>

                                        <!-- Reactions -->
                                        <div class="reactions">
                                            <button class="btn btn-sm {{ $item->userReaction && $item->userReaction->type === 'like' ? 'btn-success' : 'btn-outline-success' }} reaction-btn" 
                                                    data-item="{{ $item->id }}" 
                                                    data-type="like">
                                                👍 <span class="likes-count">{{ $item->likes }}</span>
                                            </button>
                                            <button class="btn btn-sm {{ $item->userReaction && $item->userReaction->type === 'dislike' ? 'btn-danger' : 'btn-outline-danger' }} reaction-btn" 
                                                    data-item="{{ $item->id }}" 
                                                    data-type="dislike">
                                                👎 <span class="dislikes-count">{{ $item->dislikes }}</span>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Posted by {{ $item->user ? $item->user->name : 'Deleted User' }} {{ $item->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                <!-- Comments Section -->
                                <div class="comments-section mt-4">
                                    <h6 class="mb-3">Comments</h6>
                                    
                                    <!-- Add Comment Form -->
                                    <form class="add-comment-form mb-3" data-item="{{ $item->id }}">
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Add a comment..." 
                                                   required>
                                            <button type="submit" class="btn btn-primary">Post</button>
                                        </div>
                                    </form>

                                    <!-- Comments List -->
                                    <div class="comments-list" data-item="{{ $item->id }}">
                                        @foreach($item->comments->take(3) as $comment)
                                            <div class="comment border-bottom pb-2 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('images/default-profile.png') }}" 
                                                         class="rounded-circle" 
                                                         width="30" 
                                                         height="30" 
                                                         alt="Profile">
                                                    <div class="ms-2">
                                                        <strong>{{ $comment->user ? $comment->user->name : 'Deleted User' }}</strong>
                                                        <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <p class="mb-0 mt-2">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($item->comments->count() > 3)
                                        <button class="btn btn-link btn-sm show-more-comments" 
                                                data-item="{{ $item->id }}" 
                                                data-showing="less">
                                            Show more comments
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload New Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('library.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" 
                               name="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               required 
                               value="{{ old('title') }}"
                               maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  required 
                                  maxlength="1000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <div class="input-group">
                            <select name="category" 
                                    class="form-select @error('category') is-invalid @enderror" 
                                    id="category-select">
                                <option value="">Select Category</option>
                                <option value="new">+ Add New Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" 
                                   class="form-control @error('new_category') is-invalid @enderror" 
                                   id="new-category" 
                                   name="new_category" 
                                   placeholder="Enter new category"
                                   value="{{ old('new_category') }}"
                                   style="display: none;"
                                   maxlength="50">
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('new_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="subcategory" 
                                class="form-select @error('subcategory') is-invalid @enderror" 
                                required>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory }}" {{ old('subcategory') == $subcategory ? 'selected' : '' }}>
                                    {{ $subcategory }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcategory')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" 
                                class="form-select @error('type') is-invalid @enderror" 
                                id="content-type" 
                                required>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="text-content">
                        <label class="form-label">Content</label>
                        <textarea name="content" 
                                  class="form-control @error('content') is-invalid @enderror" 
                                  rows="5">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>

                    <div class="mb-3" id="video-content" style="display: none;">
                        <label class="form-label">Video URL</label>
                        <input type="url" 
                               name="video_url" 
                               class="form-control @error('video_url') is-invalid @enderror"
                               value="{{ old('video_url') }}">
                                <small class="text-muted">https://www.youtube.com/embed/videoid</small>
                        @error('video_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional File (Optional)</label>
                        <input type="file" 
                               name="file" 
                               class="form-control @error('file') is-invalid @enderror">
                        <small class="text-muted">Max size: 10MB. Supported formats: PDF, DOC, DOCX, TXT, MP4, ZIP, RAR</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Content View Modal -->
<div class="modal fade" id="contentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="description mb-4"></div>
                <div class="content-container">
                    <!-- Content will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- My Resources Modal -->
<div class="modal fade" id="myResourcesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">My Resources</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="myResourcesTable">
                            <!-- Content will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Resource Modal -->
<div class="modal fade" id="editResourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editResourceForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="resource_id" id="edit_resource_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="edit_title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" id="edit_description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control" id="edit_category" required>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subcategory</label>
                        <select name="subcategory" class="form-control" id="edit_subcategory" required>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory }}">{{ $subcategory }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Resource</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/library.js') }}"></script>
<script>
    // Load my resources when modal opens
    $('#myResourcesModal').on('show.bs.modal', function () {
        loadMyResources();
    });

    function loadMyResources() {
        $.get('/library/my-resources', function(response) {
            if (response.success) {
                let html = '';
                response.resources.forEach(function(resource) {
                    html += `
                        <tr data-resource-id="${resource.id}">
                            <td>${resource.title}</td>
                            <td>${resource.type}</td>
                            <td>${resource.category}</td>
                            <td>${new Date(resource.created_at).toLocaleDateString()}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-resource" 
                                        data-resource-id="${resource.id}"
                                        data-title="${resource.title}"
                                        data-description="${resource.description}"
                                        data-category="${resource.category}"
                                        data-subcategory="${resource.subcategory}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-resource" 
                                        data-resource-id="${resource.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#myResourcesTable').html(html);
            }
        });
    }

    // Edit resource
    $(document).on('click', '.edit-resource', function() {
        const resource = $(this).data();
        $('#edit_resource_id').val(resource.resourceId);
        $('#edit_title').val(resource.title);
        $('#edit_description').val(resource.description);
        $('#edit_category').val(resource.category);
        $('#edit_subcategory').val(resource.subcategory);
        
        $('#myResourcesModal').modal('hide');
        $('#editResourceModal').modal('show');
    });

    // Handle edit form submission
    $('#editResourceForm').on('submit', function(e) {
        e.preventDefault();
        const resourceId = $('#edit_resource_id').val();
        
        $.ajax({
            url: `/library/${resourceId}/update`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editResourceModal').modal('hide');
                    $('#myResourcesModal').modal('show');
                    loadMyResources();
                    alert('Resource updated successfully');
                }
            }
        });
    });

    // Delete resource
    $(document).on('click', '.delete-resource', function() {
        if (confirm('Are you sure you want to delete this resource?')) {
            const resourceId = $(this).data('resourceId');
            
            $.ajax({
                url: `/library/${resourceId}/delete`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        loadMyResources();
                        alert('Resource deleted successfully');
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection

