@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Hobbies</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHobbyModal">
                        <i class="bi bi-plus-lg"></i> Add Hobby
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                            @if(session('new_hobby_id'))
                                <a href="{{ route('goals.index') }}" class="alert-link">Create a goal now!</a>
                            @endif
                        </div>
                    @endif

                    <div class="row">
                        @forelse($hobbies as $hobby)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title">{{ $hobby->name }}</h5>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editHobbyModal{{ $hobby->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-hobby-btn" 
                                                        data-hobby-id="{{ $hobby->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="card-text">{{ $hobby->description }}</p>
                                        <span class="badge bg-primary">{{ $hobby->experience_level }}</span>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('goals.index') }}" class="btn btn-link">
                                            <i class="bi bi-trophy"></i> View Related Goals
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p class="text-muted">You haven't added any hobbies yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Hobby Modal -->
<div class="modal fade" id="createHobbyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Hobby</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('hobbies.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Hobby Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Experience Level</label>
                        <select name="experience_level" class="form-select" required>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Expert">Expert</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Hobby</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Hobby Modals -->
@foreach($hobbies as $hobby)
    <div class="modal fade" id="editHobbyModal{{ $hobby->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Hobby</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('hobbies.update', $hobby) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hobby Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $hobby->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required>{{ $hobby->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Experience Level</label>
                            <select name="experience_level" class="form-control" required>
                                <option value="Beginner" {{ $hobby->experience_level == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ $hobby->experience_level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Expert" {{ $hobby->experience_level == 'Expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Delete Confirmation Form -->
<form id="deleteHobbyForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove the inline onclick and add event listeners
        document.querySelectorAll('.delete-hobby-btn').forEach(button => {
            button.addEventListener('click', function() {
                confirmDelete(this.dataset.hobbyId);
            });
        });
    });

    function confirmDelete(hobbyId) {
        if (confirm('Are you sure you want to delete this hobby? All associated goals and milestones will also be deleted.')) {
            const form = document.getElementById('deleteHobbyForm');
            form.action = `/hobbies/${hobbyId}`;
            form.submit();
        }
    }
</script>
@endsection
