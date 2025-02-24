@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Resource Library</h4>

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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

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
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="rated" {{ request('sort') == 'rated' ? 'selected' : '' }}>Highly Rated</option>
                    </select>
                </div>

                <!-- Resources List -->
                <div class="resources-list">
                    @foreach($items as $item)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="#" class="text-decoration-none view-content" 
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
                                <p class="card-text">{{ $item->description }}</p>

                                @if($item->type === 'video')
                                    <div class="embed-responsive embed-responsive-16by9 mb-3">
                                        <iframe class="embed-responsive-item" src="{{ $item->video_url }}" allowfullscreen></iframe>
                                    </div>
                                @else
                                    <div class="content-preview mb-3">
                                        {{ Str::limit($item->content, 200) }}
                                    </div>
                                @endif

                                @if($item->file_path)
                                    <a href="{{ Storage::url($item->file_path) }}" class="btn btn-sm btn-outline-primary mb-3">
                                        Download Resource
                                    </a>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Star Rating -->
                                        <div class="rating me-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $item->userRating(auth()->user()) && $item->userRating(auth()->user())->rating >= $i ? '-fill' : '' }} text-warning rating-star" 
                                                   data-rating="{{ $i }}" 
                                                   data-item="{{ $item->id }}"></i>
                                            @endfor
                                            <small class="text-muted ms-2">({{ $item->rating_count }})</small>
                                        </div>

                                        <!-- Favorite Button -->
                                        <button class="btn btn-sm {{ $item->isFavoritedBy(auth()->user()) ? 'btn-danger' : 'btn-outline-danger' }} favorite-btn"
                                                data-item="{{ $item->id }}">
                                            <i class="bi bi-heart{{ $item->isFavoritedBy(auth()->user()) ? '-fill' : '' }}"></i>
                                        </button>
                                    </div>

                                    <div class="reactions">
                                        <button class="btn btn-sm btn-outline-success reaction-btn" data-item="{{ $item->id }}" data-type="like">
                                            👍 <span class="likes-count">{{ $item->likes }}</span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger reaction-btn" data-item="{{ $item->id }}" data-type="dislike">
                                            👎 <span class="dislikes-count">{{ $item->dislikes }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Comments Section -->
                                <div class="comments-section mt-4">
                                    <h6>Comments ({{ $item->comments->count() }})</h6>
                                    
                                    <!-- Add Comment Form -->
                                    <form class="add-comment-form mb-3" data-item="{{ $item->id }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Add a comment...">
                                            <button class="btn btn-primary" type="submit">Post</button>
                                        </div>
                                    </form>

                                    <!-- Comments List -->
                                    <div class="comments-list" data-item="{{ $item->id }}">
                                        @foreach($item->comments->take(3) as $comment)
                                            <div class="comment mb-2">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('images/default-profile.png') }}" 
                                                         class="profile-image-small me-2" alt="Profile">
                                                    <strong>{{ $comment->user->name }}</strong>
                                                    <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-1 ms-4">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($item->comments->count() > 3)
                                        <div class="comments-toggle">
                                            <button class="btn btn-link show-more-comments" 
                                                    data-item="{{ $item->id }}" 
                                                    data-showing="less">
                                                Show more comments
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ $items->previousPageUrl() }}" class="btn btn-outline-primary {{ $items->onFirstPage() ? 'disabled' : '' }}">Previous</a>
                        {{ $items->links() }}
                        <a href="{{ $items->nextPageUrl() }}" class="btn btn-outline-primary {{ $items->hasMorePages() ? '' : 'disabled' }}">Next</a>
                    </div>
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

@push('scripts')
<script src="{{ asset('js/library.js') }}"></script>
@endpush
@endsection

