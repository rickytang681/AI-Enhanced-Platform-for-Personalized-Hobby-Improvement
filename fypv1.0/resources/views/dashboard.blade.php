@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-4">
    <!-- Welcome Banner -->
    <div class="welcome-banner shadow-sm rounded p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-muted mb-0">Track your hobbies, set goals, and watch your progress grow.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHobbyModal">
                    <i class="bi bi-plus-circle"></i> Add New Hobby
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-trophy text-warning fs-3"></i>
                    <h3 class="mt-2 mb-1">{{ $hobbies->count() }}</h3>
                    <p class="text-muted small mb-0">Active Hobbies</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success fs-3"></i>
                    <h3 class="mt-2 mb-1">{{ $hobbies->sum('completed_goals_count') }}</h3>
                    <p class="text-muted small mb-0">Goals Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up text-primary fs-3"></i>
                    <h3 class="mt-2 mb-1">{{ $overallProgress }}%</h3>
                    <p class="text-muted small mb-0">Overall Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check text-info fs-3"></i>
                    <h3 class="mt-2 mb-1">{{ $hobbies->sum('goals_count') }}</h3>
                    <p class="text-muted small mb-0">Total Goals</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Hobbies Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">My Hobbies</h5>
                </div>
                <div class="card-body">
                    @if($hobbies->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-smile text-muted fs-1"></i>
                            <h6 class="mt-3">No hobbies added yet</h6>
                            <p class="text-muted small">Start by adding your first hobby!</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHobbyModal">
                                Add Hobby
                            </button>
                        </div>
                    @else
                        <div class="accordion" id="hobbiesAccordion">
                            @foreach($hobbies as $hobby)
                                <div class="accordion-item border-0 mb-3">
                                    <div class="accordion-header" id="hobby-{{ $hobby->id }}-heading">
                                        <div class="d-flex justify-content-between align-items-center p-3">
                                            <div>
                                                <h6 class="mb-0">{{ $hobby->name }}</h6>
                                                <span class="badge bg-primary">{{ $hobby->experience_level }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="btn-group me-3">
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
                                                <button class="btn btn-sm btn-link text-decoration-none" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#hobby-{{ $hobby->id }}-content">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $hobby->goals_count > 0 ? ($hobby->completed_goals_count / $hobby->goals_count) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center px-3 py-2">
                                            <small class="text-muted">
                                                {{ $hobby->completed_goals_count }}/{{ $hobby->goals_count }} Goals Completed
                                            </small>
                                        </div>
                                    </div>

                                    <div id="hobby-{{ $hobby->id }}-content" class="collapse" 
                                         aria-labelledby="hobby-{{ $hobby->id }}-heading" 
                                         data-bs-parent="#hobbiesAccordion">
                                        <div class="accordion-body">
                                            @if($hobby->goals->isEmpty())
                                                <p class="text-center text-muted my-3">No goals set for this hobby yet.</p>
                                                <div class="text-center">
                                                    <a href="{{ route('goals.create') }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Add Goal
                                                    </a>
                                                </div>
                                            @else
                                                <div class="list-group list-group-flush">
                                                    @foreach($hobby->goals as $goal)
                                                        <div class="list-group-item border-0 px-3 py-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="bi {{ $goal->status === 'completed' ? 'bi-check-circle-fill text-success' : 'bi-circle' }} me-2"></i>
                                                                    {{ $goal->goal }}
                                                                </div>
                                                                <span class="badge {{ $goal->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                                                                    {{ ucfirst($goal->status) }}
                                                                </span>
                                                            </div>
                                                            @if($goal->milestones->isNotEmpty())
                                                                <div class="ms-4 mt-2">
                                                                    @foreach($goal->milestones as $milestone)
                                                                        <div class="d-flex align-items-center mb-1">
                                                                            <i class="bi {{ $milestone->completed ? 'bi-check-circle-fill text-success' : 'bi-circle' }} me-2 small"></i>
                                                                            <small class="{{ $milestone->completed ? 'text-decoration-line-through' : '' }}">
                                                                                {{ $milestone->description }}
                                                                            </small>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('goals.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-bullseye"></i> Set New Goal
                        </a>
                        <a href="{{ route('recommendation') }}" class="btn btn-outline-info">
                            <i class="bi bi-lightbulb"></i> Get Recommendations
                        </a>
                        <a href="{{ route('community.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-people"></i> Join Community
                        </a>
                        <a href="{{ route('library') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-book"></i> Browse Resources
                        </a>
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
                            <select name="experience_level" class="form-select" required>
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

<!-- Delete Form -->
<form id="deleteHobbyForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete button clicks
        document.querySelectorAll('.delete-hobby-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                confirmDelete(this.dataset.hobbyId);
            });
        });

        function confirmDelete(hobbyId) {
            if (confirm('Are you sure you want to delete this hobby? All associated goals and milestones will also be deleted.')) {
                const form = document.getElementById('deleteHobbyForm');
                form.action = `/hobbies/${hobbyId}`;
                form.submit();
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert) {
                    alert.style.display = 'none';
                }
            });
        }, 5000);
    });
</script>
@endsection
