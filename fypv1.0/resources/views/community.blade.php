@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Community</h4>

                <!-- Search Bar and Filters -->
                <div class="filter-section mb-4">
                    <form id="search-form" class="mb-3">
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" 
                                       id="searchInput" 
                                       name="search"
                                       class="form-control" 
                                       placeholder="Search posts..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch">
                                    <i class="bi bi-sliders"></i> Filters
                                </button>
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>

                        <!-- Advanced Search Options -->
                        <div class="collapse" id="advancedSearch">
                            <div class="card card-body">
                                <div class="row">
                                    <!-- Date Range -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date Range</label>
                                        <select name="date_filter" class="form-select">
                                            <option value="">Any Time</option>
                                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                                            <option value="year" {{ request('date_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                                        </select>
                                    </div>

                                    <!-- Post Type -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Post Type</label>
                                        <select name="post_type" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="question" {{ request('post_type') == 'question' ? 'selected' : '' }}>Questions</option>
                                            <option value="experience" {{ request('post_type') == 'experience' ? 'selected' : '' }}>Experience Sharing</option>
                                            <option value="discussion" {{ request('post_type') == 'discussion' ? 'selected' : '' }}>Discussion Topics</option>
                                        </select>
                                    </div>

                                    <!-- Tag Filter -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Filter by Tag</label>
                                        <select name="tag" class="form-select">
                                            <option value="">All Tags</option>
                                            @foreach($tags as $tag)
                                                <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>
                                                    {{ $tag }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Search In -->
                                    <div class="col-12 mb-3">
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
                                                       value="content" 
                                                       id="searchContent"
                                                       {{ in_array('content', request('search_in', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="searchContent">Content</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="has_image" 
                                                       value="1" 
                                                       id="hasImage"
                                                       {{ request('has_image') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="hasImage">Has Image</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Saved Posts Filter -->
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="savedFilter" 
                                                   name="saved" 
                                                   value="true"
                                                   {{ request('saved') === 'true' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="savedFilter">Show Saved Posts Only</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Create Post Button -->
                <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="bi bi-plus-circle"></i> Create New Post
                </button>

                <!-- Posts List -->
                <div class="posts-container">
                    @foreach($posts as $post)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $post->user->profile_picture ? asset('storage/' . $post->user->profile_picture) : asset('images/default-profile.png') }}" 
                                             class="profile-image-small me-2" 
                                             alt="Profile">
                                        <div>
                                            <h5 class="card-title mb-0">{{ $post->title }}</h5>
                                            <small class="text-muted">Posted by {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary">{{ $post->post_type }}</span>
                                        <span class="badge bg-secondary">{{ $post->tag }}</span>
                                    </div>
                                </div>

                                @if($post->cover_image)
                                    <div class="post-cover mb-3 d-flex justify-content-center">
                                        <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                             class="img-fluid" 
                                             style="max-height: 400px;width: auto;"
                                             alt="Post cover">
                                    </div>
                                @endif

                                <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                                @if(strlen($post->content) > 200)
                                    <a href="#" class="view-content" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#contentModal"
                                       data-title="{{ $post->title }}"
                                       data-content="{{ $post->content }}">Read more</a>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <!-- Reaction Buttons -->
                                    <div class="btn-group">
                                        <button class="btn btn-sm reaction-btn {{ $post->userReaction && $post->userReaction->reaction_type === 'like' ? 'btn-success' : 'btn-outline-success' }}"
                                                data-item="{{ $post->id }}"
                                                data-type="like">
                                            <i class="bi bi-hand-thumbs-up"></i>
                                            <span class="likes-count">{{ $post->likes }}</span>
                                        </button>
                                        <button class="btn btn-sm reaction-btn {{ $post->userReaction && $post->userReaction->reaction_type === 'dislike' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                data-item="{{ $post->id }}"
                                                data-type="dislike">
                                            <i class="bi bi-hand-thumbs-down"></i>
                                            <span class="dislikes-count">{{ $post->dislikes }}</span>
                                        </button>
                                    </div>

                                    <!-- Save Button -->
                                    <button class="btn btn-sm save-btn {{ $post->isSavedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}"
                                            data-item="{{ $post->id }}"
                                            title="{{ $post->isSavedBy(auth()->user()) ? 'Remove from saved' : 'Save for later' }}">
                                        <i class="bi {{ $post->isSavedBy(auth()->user()) ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                    </button>
                                </div>

                                <!-- Comments Section -->
                                <div class="comments-section mt-3">
                                    <h6>Comments ({{ $post->comments->count() }})</h6>
                                    <form class="add-comment-form mb-2" data-item="{{ $post->id }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Add a comment...">
                                            <button class="btn btn-primary" type="submit">Post</button>
                                        </div>
                                    </form>
                                    <div class="comments-list">
                                        @foreach($post->comments->take(3) as $comment)
                                            <div class="comment">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('images/default-profile.png') }}" 
                                                         class="profile-image-small me-2" 
                                                         alt="Profile">
                                                    <strong>{{ $comment->user->name }}</strong>
                                                    <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 ms-4">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($post->comments->count() > 3)
                                        <button class="btn btn-link show-more-comments" 
                                                data-item="{{ $post->id }}"
                                                data-showing="less">
                                            Show more comments
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPostModalLabel">Create New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
                    @csrf
                    
                    <!-- Post Type -->
                    <div class="mb-3">
                        <label for="postType" class="form-label">Post Type</label>
                        <select name="post_type" id="postType" class="form-select" required>
                            <option value="" disabled selected>Select post type</option>
                            <option value="question">Question</option>
                            <option value="experience">Experience Sharing</option>
                            <option value="discussion">Discussion Topic</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               required 
                               maxlength="255" 
                               placeholder="Enter post title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" 
                                  id="content" 
                                  class="form-control @error('content') is-invalid @enderror" 
                                  rows="5" 
                                  required 
                                  placeholder="Share your thoughts..."></textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cover Image -->
                    <div class="mb-3">
                        <label for="coverImage" class="form-label">Cover Image (optional)</label>
                        <input type="file" 
                               name="cover_image" 
                               id="coverImage" 
                               class="form-control @error('cover_image') is-invalid @enderror" 
                               accept="image/*">
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tag Selection -->
                    <div class="mb-3">
                        <label for="tagSelect" class="form-label">Tag</label>
                        <div class="input-group">
                            <select name="tag" id="tagSelect" class="form-select" required>
                                <option value="" disabled selected>Select a tag</option>
                                <option value="new">+ Add New Tag</option>
                                <option value="Programming">Programming</option>
                                <option value="Reading">Reading</option>
                                <option value="Photography">Photography</option>
                                <option value="Writing">Writing</option>
                                <option value="Gardening">Gardening</option>
                            </select>
                            <input type="text" 
                                   name="new_tag" 
                                   id="newTag" 
                                   class="form-control" 
                                   placeholder="Enter new tag" 
                                   style="display: none;">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Content View Modal -->
<div class="modal fade" id="contentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="content-container"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/community.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tag selection
    const tagSelect = document.getElementById('tagSelect');
    const newTagInput = document.getElementById('newTag');

    tagSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            newTagInput.style.display = 'block';
            newTagInput.required = true;
        } else {
            newTagInput.style.display = 'none';
            newTagInput.required = false;
        }
    });

    // Reset form when modal is closed
    const createPostModal = document.getElementById('createPostModal');
    createPostModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('createPostForm').reset();
        newTagInput.style.display = 'none';
        newTagInput.required = false;
    });

    // Handle form submission errors
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('createPostModal')).show();
    @endif
});
</script>
@endpush
@endsection
