@extends('layouts.logoutHeader')

{{-- Place this right after the @extends directive --}}
@push('scripts')
<script>
// Define the functions first
function confirmDeleteGoal(goalId) {
    if (confirm('Are you sure you want to delete this goal? All associated milestones will also be deleted.')) {
        const form = document.getElementById('deleteGoalForm');
        form.action = `/goals/${goalId}`;
        form.submit();
    }
}

function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    container.prepend(alertDiv);

    // Auto-dismiss alert after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

function confirmDeleteMilestone(goalId, milestoneId) {
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
                const milestoneElement = document.querySelector(`[data-milestone-id="${milestoneId}"]`)
                    .closest('.list-group-item');
                milestoneElement.remove();

                // Update goal progress
                updateGoalProgress(goalId, data.progress, data.status);

                // Show success message
                showAlert('Milestone deleted successfully');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to delete milestone. Please try again.', 'danger');
        });
    }
}

function updateGoalProgress(goalId, progress, status) {
    const progressBar = document.querySelector(`#goal-${goalId} .progress-bar`);
    const statusBadge = document.querySelector(`#goal-${goalId} .status-badge`);
    
    if (progressBar) {
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.textContent = `${progress}%`;
        
        if (progress === 100) {
            progressBar.classList.add('bg-success');
        } else {
            progressBar.classList.remove('bg-success');
        }
    }
    
    if (statusBadge) {
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusBadge.className = `badge status-badge ${status === 'completed' ? 'bg-success' : 'bg-primary'}`;
    }
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Attach event listeners to delete buttons
    document.querySelectorAll('[data-delete-goal]').forEach(button => {
        button.addEventListener('click', function() {
            confirmDeleteGoal(this.dataset.goalId);
        });
    });

    document.querySelectorAll('[data-delete-milestone]').forEach(button => {
        button.addEventListener('click', function() {
            confirmDeleteMilestone(this.dataset.goalId, this.dataset.milestoneId);
        });
    });

    // Add event listeners for modal cleanup
    document.querySelectorAll('.modal').forEach(modalElement => {
        modalElement.addEventListener('hidden.bs.modal', function() {
            const modalBackdrop = document.querySelector('.modal-backdrop');
            if (modalBackdrop) {
                modalBackdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });
});
</script>
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Goal Setting Section</h4>
                    
                    <!-- Add tabs for better organization -->
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#new-goal">New Goal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#my-goals">My Goals</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="new-goal">
                            <!-- Alert messages -->
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Goal Form -->
                            <form method="POST" action="{{ route('goals.store') }}">
                                @csrf
                                
                                <!-- Goal with better description -->
                                <div class="mb-3">
                                    <label for="goal" class="form-label">What is your goal?</label>
                                    <input type="text" id="goal" name="goal" class="form-control" 
                                           placeholder="e.g., Learn to play 3 songs on guitar" 
                                           value="{{ old('goal') }}" required>
                                    <small class="text-muted">Be specific and measurable with your goal</small>
                                </div>

                                <!-- Hobby Input with better UX -->
                                <div class="mb-3">
                                    <label class="form-label">Select Hobby</label>
                                    <select name="hobby_id" id="hobby_id" class="form-select" required>
                                        <option value="">Choose a hobby</option>
                                        @foreach($hobbies as $hobby)
                                            <option value="{{ $hobby->id }}" {{ old('hobby_id') == $hobby->id ? 'selected' : '' }}>
                                                {{ $hobby->name }} ({{ $hobby->experience_level }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Deadline with min date -->
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">When do you want to achieve this?</label>
                                    <input type="date" id="deadline" name="deadline" class="form-control" 
                                           min="{{ date('Y-m-d') }}" value="{{ old('deadline') }}">
                                </div>

                                <!-- Milestones section -->
                                <div class="mb-3">
                                    <label for="milestones" class="form-label">Milestones</label>
                                    <div id="milestones-container">
                                        <div class="milestone-entry mb-2">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input type="text" name="milestones[]" class="form-control" 
                                                           placeholder="Enter milestone" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="date" name="milestone_dates[]" class="form-control milestone-date" 
                                                           required min="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary mt-2" id="add-milestone">
                                        <i class="bi bi-plus"></i> Add Another Milestone
                                    </button>
                                </div>

                                <!-- Submit Button -->
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-100">Create Goal</button>
                                </div>
                            </form>
                        </div>

                        <!-- Existing Goals Tab -->
                        <div class="tab-pane fade" id="my-goals">
                            @if($hobbiesWithoutGoals->isNotEmpty())
                                <div class="alert alert-info">
                                    <h5>Hobbies Without Goals</h5>
                                    <ul>
                                        @foreach($hobbiesWithoutGoals as $hobby)
                                            <li>
                                                {{ $hobby->name }} - 
                                                <a href="#" onclick="setupGoalForHobby({{ $hobby->id }}, '{{ $hobby->name }}')" class="alert-link">
                                                    <i class="bi bi-plus-circle"></i> Click here to add a goal
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($goalsByHobby) && $goalsByHobby->isNotEmpty())
                                @foreach($goalsByHobby as $hobbyId => $goals)
                                    <div class="hobby-goals-section mb-4">
                                        <h4>{{ $goals->first()->hobby->name }}</h4>
                                        @foreach($goals as $goal)
                                            <div class="card mb-3 goal-card" data-goal-id="{{ $goal->id }}" data-status="{{ $goal->status }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div>
                                                            <h5 class="card-title">{{ $goal->goal }}</h5>
                                                            <small class="text-muted">
                                                                Deadline: {{ $goal->deadline->format('d M Y') }}
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <span class="badge {{ $goal->progress == 100 ? 'bg-success' : 'bg-primary' }} me-2">
                                                                {{ $goal->progress == 100 ? 'Completed' : 'In Progress' }}
                                                            </span>
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editGoalModal{{ $goal->id }}">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                @can('delete', $goal)
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        data-delete-goal 
                                                                        data-goal-id="{{ $goal->id }}">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="progress mb-3" style="height: 20px;">
                                                        <div class="progress-bar {{ $goal->progress == 100 ? 'bg-success' : '' }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $goal->progress }}%"
                                                             aria-valuenow="{{ $goal->progress }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ $goal->progress }}% Complete
                                                        </div>
                                                    </div>

                                                    <div class="milestones-section">
                                                        <h6 class="d-flex justify-content-between align-items-center">
                                                            Milestones
                                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal{{ $goal->id }}">
                                                                <i class="bi bi-plus"></i> Add Milestone
                                                            </button>
                                                        </h6>
                                                        <ul class="list-group">
                                                            @foreach($goal->milestones as $milestone)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div class="milestone-content">
                                                                        <input type="checkbox" class="form-check-input me-2 milestone-checkbox"
                                                                               data-milestone-id="{{ $milestone->id }}"
                                                                               data-goal-id="{{ $goal->id }}"
                                                                               {{ $milestone->completed ? 'checked' : '' }}>
                                                                        <span class="{{ $milestone->completed ? 'text-decoration-line-through' : '' }}">
                                                                            {{ $milestone->description }}
                                                                        </span>
                                                                        <small class="text-muted ms-2">Due: {{ $milestone->due_date->format('Y-m-d') }}</small>
                                                                    </div>
                                                                    <div class="milestone-actions">
                                                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#editMilestoneModal{{ $milestone->id }}">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                        @can('delete', $milestone)
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger"
                                                                                data-delete-milestone
                                                                                data-goal-id="{{ $goal->id }}"
                                                                                data-milestone-id="{{ $milestone->id }}">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                        @endcan
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">You haven't set any goals yet.</p>
                                    <button class="btn btn-primary" onclick="document.querySelector('[href=\'#new-goal\']').click()">
                                        Create Your First Goal
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <!-- Updated JavaScript -->
    <script>
    // ... existing add/remove hobby code ...

    // Add filter functionality
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            document.querySelectorAll('.goal-card').forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update active button
            document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Handle milestone checkbox clicks
    document.querySelectorAll('.milestone-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const milestoneId = this.dataset.milestoneId;
            const goalId = this.dataset.goalId;
            const completed = this.checked;

            fetch(`/goals/${goalId}/milestones/${milestoneId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ completed })
            })
            .then(response => response.json())
            .then(data => {
                // Update goal progress
                const goalCard = this.closest('.goal-card');
                const progressBar = goalCard.querySelector('.progress-bar');
                progressBar.style.width = `${data.progress}%`;
                progressBar.textContent = `${data.progress}%`;
                progressBar.setAttribute('aria-valuenow', data.progress);

                // Update goal status badge if needed
                const statusBadge = goalCard.querySelector('.badge');
                if (data.status === 'completed') {
                    statusBadge.classList.remove('bg-primary');
                    statusBadge.classList.add('bg-success');
                    statusBadge.textContent = 'Completed';
                } else {
                    statusBadge.classList.remove('bg-success');
                    statusBadge.classList.add('bg-primary');
                    statusBadge.textContent = 'In Progress';
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Add Milestone functionality
        const milestonesContainer = document.getElementById('milestones-container');
        const addMilestoneBtn = document.getElementById('add-milestone');

        addMilestoneBtn.addEventListener('click', function() {
            const milestoneEntry = document.createElement('div');
            milestoneEntry.className = 'milestone-entry mb-2';
            milestoneEntry.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="milestones[]" class="form-control" 
                               placeholder="Enter milestone" required>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="date" name="milestone_dates[]" class="form-control milestone-date" 
                                   required min="{{ date('Y-m-d') }}" ${deadline.value ? 'max="' + deadline.value + '"' : ''}>
                            <button type="button" class="btn btn-danger remove-milestone">×</button>
                        </div>
                    </div>
                </div>
            `;
            milestonesContainer.appendChild(milestoneEntry);
        });

        // Remove Milestone functionality
        milestonesContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-milestone')) {
                e.target.closest('.milestone-entry').remove();
            }
        });

        // Validate milestone dates when goal deadline changes
        const deadline = document.getElementById('deadline');
        const milestoneDates = document.getElementsByClassName('milestone-date');

        deadline.addEventListener('change', function() {
            Array.from(milestoneDates).forEach(date => {
                date.max = this.value;
            });
        });

        // Switch to My Goals tab if specified
        @if(session('activeTab') === 'my-goals')
            document.querySelector('[href="#my-goals"]').click();
        @endif
    });

    function setupGoalForHobby(hobbyId, hobbyName) {
        document.querySelector('[href="#new-goal"]').click();
        document.getElementById('hobby_id').value = hobbyId;
        document.getElementById('goal').focus();
    }

    function updateGoalProgress(goalId, progress, status) {
        const goalCard = document.querySelector(`.goal-card[data-goal-id="${goalId}"]`);
        const progressBar = goalCard.querySelector('.progress-bar');
        const statusBadge = goalCard.querySelector('.badge');

        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.textContent = `${progress}% Complete`;

        if (progress === 100) {
            progressBar.classList.add('bg-success');
            statusBadge.className = 'badge bg-success';
            statusBadge.textContent = 'Completed';
        } else {
            progressBar.classList.remove('bg-success');
            statusBadge.className = 'badge bg-primary';
            statusBadge.textContent = 'In Progress';
        }
    }

    // Update milestone checkbox handler
    document.querySelectorAll('.milestone-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const goalId = this.dataset.goalId;
            const milestoneId = this.dataset.milestoneId;

            fetch(`/goals/${goalId}/milestones/${milestoneId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    completed: this.checked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateGoalProgress(goalId, data.progress, data.status);
                    if (this.checked) {
                        this.closest('.milestone-content').querySelector('span').classList.add('text-decoration-line-through');
                    } else {
                        this.closest('.milestone-content').querySelector('span').classList.remove('text-decoration-line-through');
                    }
                }
            });
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
@endsection

<!-- Add Edit Goal Modal -->
@foreach($goals as $goal)
    <div class="modal fade" id="editGoalModal{{ $goal->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('goals.update', $goal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Goal</label>
                            <input type="text" name="goal" class="form-control" value="{{ $goal->goal }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hobby</label>
                            <select name="hobby_id" class="form-select" required>
                                @foreach($hobbies as $hobby)
                                    <option value="{{ $hobby->id }}" {{ $goal->hobby_id == $hobby->id ? 'selected' : '' }}>
                                        {{ $hobby->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" 
                                   value="{{ $goal->deadline->format('Y-m-d') }}" required>
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

<!-- Add Edit Milestone Modal -->
@foreach($goals as $goal)
    <div class="modal fade" id="addMilestoneModal{{ $goal->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Milestone</h5>
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
                                   max="{{ $goal->deadline->format('Y-m-d') }}" required>
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

<!-- Add Edit Milestone Modal -->
@foreach($goals as $goal)
    @foreach($goal->milestones as $milestone)
    <div class="modal fade" id="editMilestoneModal{{ $milestone->id }}" tabindex="-1" aria-labelledby="editMilestoneModalLabel{{ $milestone->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMilestoneModalLabel{{ $milestone->id }}">Edit Milestone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDescription{{ $milestone->id }}" class="form-label">Description</label>
                        <input type="text" id="editDescription{{ $milestone->id }}" class="form-control" 
                               value="{{ $milestone->description }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDueDate{{ $milestone->id }}" class="form-label">Due Date</label>
                        <input type="date" id="editDueDate{{ $milestone->id }}" class="form-control" 
                               value="{{ $milestone->due_date->format('Y-m-d') }}"
                               min="{{ date('Y-m-d') }}" 
                               max="{{ $goal->deadline->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateMilestone({{ $goal->id }}, {{ $milestone->id }})">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endforeach

<!-- Add delete forms -->
<form id="deleteGoalForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="deleteMilestoneForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function updateMilestone(goalId, milestoneId) {
    const description = document.getElementById(`editDescription${milestoneId}`).value;
    const dueDate = document.getElementById(`editDueDate${milestoneId}`).value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Get the modal instance before making the fetch request
    const modalElement = document.getElementById(`editMilestoneModal${milestoneId}`);
    const modal = bootstrap.Modal.getInstance(modalElement);

    fetch(`/goals/${goalId}/milestones/${milestoneId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            description: description,
            due_date: dueDate
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the milestone text in the UI
            const milestoneElement = document.querySelector(`[data-milestone-id="${milestoneId}"]`);
            if (milestoneElement) {
                const descriptionSpan = milestoneElement.querySelector('.milestone-content span');
                if (descriptionSpan) {
                    descriptionSpan.textContent = description;
                }
            }

            // Close the modal
            if (modal) {
                modal.hide();
            }

            // Show success message using the existing showAlert function
            showAlert('Milestone updated successfully');

            // Update the due date display if you have one
            const dueDateElement = milestoneElement.querySelector('.milestone-due-date');
            if (dueDateElement) {
                const formattedDate = new Date(dueDate).toLocaleDateString();
                dueDateElement.textContent = formattedDate;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to update milestone. Please try again.', 'danger');
    })
    .finally(() => {
        // Clean up any remaining modal backdrop
        const modalBackdrop = document.querySelector('.modal-backdrop');
        if (modalBackdrop) {
            modalBackdrop.remove();
        }
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
}
</script>
