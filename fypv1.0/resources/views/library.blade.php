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
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search resources..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
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
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Categories</h5>
                        @foreach($categories as $category)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input category-filter" 
                                       value="{{ $category }}" id="cat-{{ $loop->index }}"
                                       {{ request('category') == $category ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat-{{ $loop->index }}">{{ $category }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-md-6">
                        <h5>Level</h5>
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
                                <h5 class="card-title">{{ $item->title }}</h5>
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

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="reactions">
                                        <button class="btn btn-sm btn-outline-success reaction-btn" data-item="{{ $item->id }}" data-type="like">
                                            👍 <span class="likes-count">{{ $item->likes }}</span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger reaction-btn" data-item="{{ $item->id }}" data-type="dislike">
                                            👎 <span class="dislikes-count">{{ $item->dislikes }}</span>
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        Posted by {{ $item->user->name }} on {{ $item->created_at->format('M d, Y') }}
                                    </small>
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
                        <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="subcategory" class="form-select" required>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory }}">{{ $subcategory }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" id="content-type" required>
                            <option value="text">Text</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div class="mb-3" id="text-content">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3" id="video-content" style="display: none;">
                        <label class="form-label">Video URL</label>
                        <input type="url" name="video_url" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional File (Optional)</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/library.js') }}"></script>
@endpush
@endsection

