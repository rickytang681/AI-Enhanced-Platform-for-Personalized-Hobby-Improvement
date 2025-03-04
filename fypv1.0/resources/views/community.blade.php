@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Community Support</h4>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Post Button -->
                <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#PostModal">
                    New Post
                </button>



                <!-- Posts List -->
                @foreach($posts as $post)
                    <div class="card mb-3">
                        <div class="card-body">
                            @if($post->cover_image)
                                <div class="d-flex justify-content-center">
                                    <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                        class="img-fluid mb-3" 
                                        alt="Cover Image"
                                        style="max-height: 300px;width: auto;">
                                </div>
                            @endif


                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <span class="badge bg-secondary">{{ $post->tag }}</span>
                            </div>

                            <p class="card-text">{{ $post->content }}</p>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Posted by {{ $post->user->name }} 
                                    {{ $post->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Post Modal -->
<div class="modal fade" id="PostModal" tabindex="-1" aria-labelledby="PostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="PostModalLabel">Create a New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Post Type -->
                    <div class="mb-3">
                        <label for="postType" class="form-label">Post Type:</label>
                        <select name="post_type" id="postType" class="form-select" required>
                            <option value="question">Question</option>
                            <option value="experience">Experience Sharing</option>
                            <option value="discussion">Discussion Topic</option>
                        </select>
                    </div>

                    <!-- Cover Image -->
                    <div class="mb-3">
                        <label for="coverImage" class="form-label">Cover Image:</label>
                        <input type="file" name="cover_image" id="coverImage" class="form-control" accept="image/*">
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" name="title" id="title" class="form-control" required maxlength="150" placeholder="Enter your title...">
                    </div>

                    <!-- Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Content:</label>
                        <textarea name="content" id="content" class="form-control" rows="4" required placeholder="Share your thoughts..."></textarea>
                    </div>

                    <!-- Tag Selector -->
                    <div class="mb-3">
                        <label for="tagSelect" class="form-label">Tag:</label>
                        <div class="input-group">
                            <select name="tag" id="tagSelect" class="form-select" required onchange="toggleNewTag()">
                                <option value="" disabled selected>Select Tag</option>
                                <option value="new">+ Add New Tag</option>
                                <option value="Programming">Programming</option>
                                <option value="Reading">Reading</option>
                                <option value="Photography">Photography</option>
                                <option value="Writing">Writing</option>
                                <option value="Gardening">Gardening</option>
                            </select>
                            <input type="text" name="new_tag" id="newTag" class="form-control" placeholder="Enter new tag" maxlength="50" style="display: none;">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tag select for create form
    const tagSelect = document.getElementById('tagSelect');
    const newTag = document.getElementById('newTag');
    
    if (tagSelect) {
        tagSelect.addEventListener('change', function() {
            if (this.value === 'new') {
                newTag.style.display = 'block';
                newTag.required = true;
                this.required = false;
            } else {
                newTag.style.display = 'none';
                newTag.required = false;
                this.required = true;
            }
        });
    }

    // Handle tag select for edit forms
    document.querySelectorAll('.tagSelect').forEach(select => {
        const newTagInput = select.parentElement.querySelector('.newTag');
        select.addEventListener('change', function() {
            if (this.value === 'new') {
                newTagInput.style.display = 'block';
                newTagInput.required = true;
                this.required = false;
            } else {
                newTagInput.style.display = 'none';
                newTagInput.required = false;
                this.required = true;
            }
        });
    });
});
</script>
@endpush

<style>
.card img.img-fluid {
    object-fit: cover;
    width: 100%;
    max-height: 300px;
}

.post-cover {
    position: relative;
    overflow: hidden;
    border-radius: 4px;
}
</style>
@endsection