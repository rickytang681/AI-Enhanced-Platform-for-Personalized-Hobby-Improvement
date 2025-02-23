@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Community Support</h4>

                <!-- Post Section -->
                <div class="post-section">
                    <h5>Post:</h5>
                    <form>
                        <div class="mb-3">
                            <label for="cover" class="form-label">Cover:</label>
                            <div class="post-cover mb-2" id="cover">
                                <span>Image Placeholder</span>
                            </div>
                            <button type="button" class="btn btn-outline-secondary">Add Cover</button>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title:</label>
                            <input type="text" class="form-control" id="title" placeholder="Enter title">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea class="form-control" id="description" rows="3" placeholder="Enter description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tag" class="form-label">Select Tag:</label>
                            <select class="form-select" id="tag">
                                <option selected>Reading</option>
                                <option>Writing</option>
                                <option>Coding</option>
                                <option>Gardening</option>
                                <option>Photography</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>

                <!-- Sort Options -->
                <div class="mb-3">
                    <select class="form-select" style="max-width: 200px;">
                        <option selected>Popular</option>
                        <option>Newest</option>
                        <option>Highly Rated</option>
                    </select>
                </div>

                <!-- Post Item -->
                <div class="post-item">
                    <div class="post-cover">
                        <span>Image Placeholder</span>
                    </div>
                    <form class="mt-3">
                        <div class="mb-2">
                            <label>Author:</label>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <div class="mb-2">
                            <label>Title:</label>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <div class="mb-2">
                            <label>Description:</label>
                            <textarea class="form-control" rows="3" readonly></textarea>
                        </div>
                    </form>
                    <div class="actions mt-2">
                        <a href="#" class="btn btn-link">Share</a>
                        <i class="thumbs">👍</i>
                        <i class="thumbs">👎</i>
                        <a href="#" class="btn btn-link">Bookmark</a>
                    </div>
                    <div class="mt-3">
                        <label>Comment:</label>
                        <textarea class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection