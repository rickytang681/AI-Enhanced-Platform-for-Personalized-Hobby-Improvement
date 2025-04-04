@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-4">
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">My Hobbies</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHobbyModal">
                <i class="bi bi-plus-circle"></i> Add New Hobby
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
                                            <a href="{{ route('goals.index', ['hobby_id' => $hobby->id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-plus-circle"></i> New Goal
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editHobbyModal{{ $hobby->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-hobby-btn"
                                                    data-hobby-id="{{ $hobby->id }}">
                                                <i class="bi bi-trash"></i> Delete
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
                                            <a href="{{ route('goals.index', ['hobby_id' => $hobby->id]) }}" class="btn btn-sm btn-primary">
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
                                                            <small class="text-muted ms-2">
                                                                <i class="bi bi-calendar-event"></i> Due: {{ $goal->deadline->format('Y-m-d') }}
                                                            </small>
                                                            <small class="text-muted ms-2">
                                                                <a href="/recommendation?hobby_id={{ $hobby->id }}&goal_id={{ $goal->id }}" 
                                                                   class="text-decoration-none text-primary">
                                                                    <i class="bi bi-lightbulb"></i> Get Recommendation
                                                                </a>
                                                            </small>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-outline-primary" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editGoalModal_{{ $hobby->id }}_{{ $goal->id }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger delete-goal-btn"
                                                                    data-goal-id="{{ $goal->id }}">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="ms-4 mt-2">
                                                        @foreach($goal->milestones as $milestone)
                                                            <div class="milestone-item">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <div class="milestone-content">
                                                                        <input type="checkbox" class="form-check-input me-2 milestone-checkbox" 
                                                                               data-goal-id="{{ $goal->id }}"
                                                                               data-milestone-id="{{ $milestone->id }}"
                                                                               {{ $milestone->completed ? 'checked' : '' }}>
                                                                        <span class="milestone-text {{ $milestone->completed ? 'text-decoration-line-through' : '' }}">
                                                                            {{ $milestone->description }}
                                                                        </span>
                                                                        <small class="text-muted milestone-due-date">Due: {{ $milestone->due_date->format('Y-m-d') }}</small>
                                                                    </div>
                                                                    <div class="btn-group">
                                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#editMilestoneModal_{{ $goal->id }}_{{ $milestone->id }}">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger delete-milestone-btn"
                                                                                data-goal-id="{{ $goal->id }}"
                                                                                data-milestone-id="{{ $milestone->id }}">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
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

<!-- Create Hobby Modal -->
@include('hobbies.partials.create-modal')

<!-- Edit Hobby Modals -->
@foreach($hobbies as $hobby)
    @include('hobbies.partials.edit-modal', ['hobby' => $hobby])
@endforeach

<!-- Goal Edit Modals -->
@foreach($hobbies as $hobby)
    @foreach($hobby->goals as $goal)
        <div class="modal fade" id="editGoalModal_{{ $hobby->id }}_{{ $goal->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Goal for {{ $hobby->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('goals.update', $goal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="hobby_id" value="{{ $hobby->id }}">
                            <div class="mb-3">
                                <label class="form-label">Goal</label>
                                <input type="text" name="goal" class="form-control" value="{{ $goal->goal }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deadline</label>
                                <input type="date" name="deadline" class="form-control" value="{{ $goal->deadline->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Goal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

<!-- Milestone Edit Modals -->
@foreach($hobbies as $hobby)
    @foreach($hobby->goals as $goal)
        @foreach($goal->milestones as $milestone)
            <div class="modal fade" id="editMilestoneModal_{{ $goal->id }}_{{ $milestone->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Milestone</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" class="form-control" 
                                       id="editDescription_{{ $goal->id }}_{{ $milestone->id }}" 
                                       value="{{ $milestone->description }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" 
                                       id="editDueDate_{{ $goal->id }}_{{ $milestone->id }}" 
                                       value="{{ $milestone->due_date->format('Y-m-d') }}" 
                                       min="{{ date('Y-m-d') }}"
                                       max="{{ $goal->deadline->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" 
                                    onclick="updateMilestone({{ $goal->id }}, {{ $milestone->id }})">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endforeach

<!-- Add Milestone Modals -->
@foreach($hobbies as $hobby)
    @foreach($hobby->goals as $goal)
        <div class="modal fade" id="addMilestoneModal_{{ $hobby->id }}_{{ $goal->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Milestone for "{{ $goal->goal }}"</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('goals.milestones.store', $goal->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" 
                                       min="{{ date('Y-m-d') }}" 
                                       max="{{ $goal->deadline->format('Y-m-d') }}" 
                                       required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Milestone</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

<!-- Delete Forms -->
<form id="deleteHobbyForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="deleteGoalForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle hobby delete button clicks
        document.querySelectorAll('.delete-hobby-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this hobby? All associated goals and milestones will also be deleted.')) {
                    const form = document.getElementById('deleteHobbyForm');
                    form.action = `/hobbies/${this.dataset.hobbyId}`;
                    form.submit();
                }
            });
        });

        // Handle goal delete button clicks
        document.querySelectorAll('.delete-goal-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this goal? All associated milestones will also be deleted.')) {
                    const form = document.getElementById('deleteGoalForm');
                    form.action = `/goals/${this.dataset.goalId}`;
                    form.submit();
                }
            });
        });

        // Handle milestone delete button clicks
        document.querySelectorAll('.delete-milestone-btn').forEach(button => {
            button.addEventListener('click', function() {
                const goalId = this.dataset.goalId;
                const milestoneId = this.dataset.milestoneId;
                
                if (confirm('Are you sure you want to delete this milestone?')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    
                    fetch(`/goals/${goalId}/milestones/${milestoneId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove the milestone element from the UI
                            const milestoneElement = this.closest('.d-flex');
                            if (milestoneElement) {
                                milestoneElement.remove();
                            }

                            // Find the goal container
                            const goalContainer = document.querySelector(`[data-goal-id="${goalId}"]`);
                            if (goalContainer) {
                                // Update progress bar
                                const progressBar = goalContainer.querySelector('.progress-bar');
                                if (progressBar) {
                                    progressBar.style.width = `${data.progress}%`;
                                    progressBar.setAttribute('aria-valuenow', data.progress);
                                }

                                // Update status if needed
                                const statusBadge = goalContainer.querySelector('.status-badge');
                                if (statusBadge) {
                                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                                    statusBadge.className = `badge status-badge ${data.status === 'completed' ? 'bg-success' : 'bg-primary'}`;
                                }
                            }

                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                Milestone deleted successfully
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            const container = document.querySelector('.container');
                            container.insertBefore(alertDiv, container.firstChild);

                            // Auto dismiss alert after 3 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 3000);
                        } else {
                            throw new Error(data.message || 'Failed to delete milestone');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            Failed to delete milestone. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        const container = document.querySelector('.container');
                        container.insertBefore(alertDiv, container.firstChild);
                    });
                }
            });
        });

        // Handle milestone checkbox clicks
        document.querySelectorAll('.milestone-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const goalId = this.dataset.goalId;
                const milestoneId = this.dataset.milestoneId;
                const completed = this.checked;
                
                // Disable the checkbox during the request
                this.disabled = true;
                
                // Get the milestone text element
                const milestoneText = this.closest('.milestone-content').querySelector('.milestone-text');
                
                fetch(`/goals/${goalId}/milestones/${milestoneId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ completed })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the UI to reflect the new state
                        if (completed) {
                            milestoneText.classList.add('text-decoration-line-through');
                        } else {
                            milestoneText.classList.remove('text-decoration-line-through');
                        }
                        
                        console.log('Milestone updated successfully');
                    } else {
                        // If there was an error, revert the checkbox state
                        this.checked = !completed;
                        console.error('Error from server:', data.message);
                        alert('Error updating milestone: ' + data.message);
                    }
                })
                .catch(error => {
                    // If there was an error, revert the checkbox state
                    this.checked = !completed;
                    console.error('Error toggling milestone:', error);
                    alert('Error updating milestone');
                })
                .finally(() => {
                    // Re-enable the checkbox
                    this.disabled = false;
                });
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert) {
                    alert.style.display = 'none';
                }
            });
        }, 5000);
    });

    function toggleMilestoneStatus(milestoneId) {
        fetch(`/milestones/${milestoneId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }

    function updateMilestone(goalId, milestoneId) {
        const description = document.getElementById(`editDescription_${goalId}_${milestoneId}`).value;
        const dueDate = document.getElementById(`editDueDate_${goalId}_${milestoneId}`).value;
        const completed = document.getElementById(`editCompleted_${goalId}_${milestoneId}`)?.checked || false;
        
        if (!description.trim()) {
            alert('Description cannot be empty');
            return;
        }

        fetch(`/goals/${goalId}/milestones/${milestoneId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                description: description,
                due_date: dueDate,
                completed: completed
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update milestone');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update milestone');
        });
    }

    function deleteMilestone(milestoneId) {
        if (confirm('Are you sure you want to delete this milestone?')) {
            fetch(`/milestones/${milestoneId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to delete milestone');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete milestone');
            });
        }
    }
</script>
@endpush

@endsection

















