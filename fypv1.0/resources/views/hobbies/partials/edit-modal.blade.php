<div class="modal fade" id="editHobbyModal{{ $hobby->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Hobby</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('hobbies.update', $hobby->id) }}" method="POST">
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
                        <select name="experience_level" class="form-select" required>
                            <option value="">Select Level</option>
                            <option value="Beginner" {{ $hobby->experience_level === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ $hobby->experience_level === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Expert" {{ $hobby->experience_level === 'Expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Hobby</button>
                </div>
            </form>
        </div>
    </div>
</div>