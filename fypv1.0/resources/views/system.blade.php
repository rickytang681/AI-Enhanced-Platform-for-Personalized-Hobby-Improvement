@extends('layouts.logoutHeader')

@section('content')
<div class="container">
    <h1>System Administration</h1>

    <!-- Add User Section -->
    <div class="section mb-4">
        <h2>Add New User</h2>
        <div class="card">
            <div class="card-body">
                <form id="addUserForm" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User List Section -->
    <div class="section">
        <h2>User Management</h2>
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($user->id !== auth()->id())
                                <button class="btn btn-danger btn-sm delete-user" 
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}">
                                    Delete
                                </button>
                                @else
                                <span class="text-muted">Current User</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Library Resources Management Section -->
    <div class="section mt-5">
        <h2>Library Resources Management</h2>
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resources as $resource)
                        <tr>
                            <td>{{ $resource->id }}</td>
                            <td>{{ $resource->title }}</td>
                            <td>{{ $resource->type }}</td>
                            <td>{{ $resource->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-resource" 
                                        data-resource-id="{{ $resource->id }}"
                                        data-resource-title="{{ $resource->title }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Library Comments Management Section -->
    <div class="section mt-5">
        <h2>Library Comments Management</h2>
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Resource</th>
                            <th>Comment</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($libraryComments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->user ? $comment->user->name : 'Deleted User' }}</td>
                            <td>{{ $comment->libraryItem ? $comment->libraryItem->title : 'Deleted Item' }}</td>
                            <td>{{ Str::limit($comment->content, 50) }}</td>
                            <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-comment" 
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment-preview="{{ Str::limit($comment->content, 30) }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="section mt-5">
        <h2>Community Posts Management</h2>
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Tag</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($communityPosts as $post)
                        <tr>
                            <td>{{ $post->id }}</td>
                            <td>{{ $post->user ? $post->user->name : 'Deleted User' }}</td>
                            <td>{{ Str::limit($post->title, 30) }}</td>
                            <td>{{ $post->post_type }}</td>
                            <td>{{ $post->tag }}</td>
                            <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-community-post" 
                                        data-post-id="{{ $post->id }}"
                                        data-post-title="{{ $post->title }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="section mt-5">
        <h2>Community Comments Management</h2>
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Post Title</th>
                            <th>Comment</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($communityComments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->user ? $comment->user->name : 'Deleted User' }}</td>
                            <td>{{ $comment->community ? Str::limit($comment->community->title, 30) : 'Deleted Post' }}</td>
                            <td>{{ Str::limit($comment->content, 50) }}</td>
                            <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-community-comment" 
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment-preview="{{ Str::limit($comment->content, 30) }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete user: <span id="deleteUserName"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Resource Delete Confirmation Modal -->
<div class="modal fade" id="deleteResourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete resource: <span id="deleteResourceTitle"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmResourceDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Comment Confirmation Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this comment: <br>
                <span id="deleteCommentPreview" class="fst-italic"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCommentDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCommunityPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this post: <br>
                <span id="deleteCommunityPostTitle" class="fst-italic"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCommunityPostDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCommunityCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this comment: <br>
                <span id="deleteCommunityCommentPreview" class="fst-italic"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCommunityCommentDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add User Form Submission
    const addUserForm = document.getElementById('addUserForm');
    addUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(addUserForm);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        try {
            const response = await fetch('{{ route("system.addUser") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const responseData = await response.json();
            
            if (response.ok && responseData.success) {
                window.location.reload();
            } else {
                let errorMessage = responseData.message || 'Error creating user';
                if (responseData.errors) {
                    errorMessage = Object.values(responseData.errors).flat().join('\n');
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error creating user. Please check console for details.');
        }
    });

    // Delete User Handling
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let userToDelete = null;

    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', () => {
            userToDelete = button.dataset.userId;
            document.getElementById('deleteUserName').textContent = button.dataset.userName;
            deleteModal.show();
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`/system/users/${userToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Error deactivating user: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deactivating user. Please try again.');
        } finally {
            deleteModal.hide();
        }
    });

    // Resource Delete Handling
    const deleteResourceModal = new bootstrap.Modal(document.getElementById('deleteResourceModal'));
    let resourceToDelete = null;

    document.querySelectorAll('.delete-resource').forEach(button => {
        button.addEventListener('click', () => {
            resourceToDelete = button.dataset.resourceId;
            document.getElementById('deleteResourceTitle').textContent = button.dataset.resourceTitle;
            deleteResourceModal.show();
        });
    });

    document.getElementById('confirmResourceDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`/system/resources/${resourceToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting resource: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting resource');
        }
        
        deleteResourceModal.hide();
    });

    // Comment Delete Handling
    const deleteCommentModal = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
    let commentToDelete = null;

    document.querySelectorAll('.delete-comment').forEach(button => {
        button.addEventListener('click', () => {
            commentToDelete = button.dataset.commentId;
            document.getElementById('deleteCommentPreview').textContent = button.dataset.commentPreview;
            deleteCommentModal.show();
        });
    });

    document.getElementById('confirmCommentDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`/system/comments/${commentToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting comment: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting comment');
        }
        
        deleteCommentModal.hide();
    });

    // Community Post Delete Handling
    const deleteCommunityPostModal = new bootstrap.Modal(document.getElementById('deleteCommunityPostModal'));
    let postToDelete = null;

    document.querySelectorAll('.delete-community-post').forEach(button => {
        button.addEventListener('click', () => {
            postToDelete = button.dataset.postId;
            document.getElementById('deleteCommunityPostTitle').textContent = button.dataset.postTitle;
            deleteCommunityPostModal.show();
        });
    });

    document.getElementById('confirmCommunityPostDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`/system/community-posts/${postToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting post: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting post');
        }
        
        deleteCommunityPostModal.hide();
    });

    // Community Comment Delete Handling
    const deleteCommunityCommentModal = new bootstrap.Modal(document.getElementById('deleteCommunityCommentModal'));
    let communityCommentToDelete = null;

    document.querySelectorAll('.delete-community-comment').forEach(button => {
        button.addEventListener('click', () => {
            communityCommentToDelete = button.dataset.commentId;
            document.getElementById('deleteCommunityCommentPreview').textContent = button.dataset.commentPreview;
            deleteCommunityCommentModal.show();
        });
    });

    document.getElementById('confirmCommunityCommentDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`/system/community-comments/${communityCommentToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting comment: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error deleting comment');
        }
        
        deleteCommunityCommentModal.hide();
    });
});
</script>
@endpush

<style>
.section {
    margin-bottom: 2rem;
}

.card {
    padding: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table {
    margin-bottom: 0;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}
</style>
