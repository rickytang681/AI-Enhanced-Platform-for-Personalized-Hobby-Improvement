@extends('layouts.logoutHeader')

@section('content')
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
                                    <label class="form-label">What hobbies are related to this goal?</label>
                                    <div id="hobby-container">
                                        <div class="hobby-entry mb-2">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text" name="hobbies[]" class="form-control" 
                                                           placeholder="Enter Hobby" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="experience[]" class="form-select" required>
                                                        <option value="">Select Level</option>
                                                        <option value="Beginner">Beginner</option>
                                                        <option value="Intermediate">Intermediate</option>
                                                        <option value="Expert">Expert</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger remove-hobby w-100">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary mt-2" id="add-hobby">
                                        <i class="bi bi-plus"></i> Add Another Hobby
                                    </button>
                                </div>

                                <!-- Deadline with min date -->
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">When do you want to achieve this?</label>
                                    <input type="date" id="deadline" name="deadline" class="form-control" 
                                           min="{{ date('Y-m-d') }}" value="{{ old('deadline') }}">
                                </div>

                                <!-- Notes section -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes (optional)</label>
                                    <textarea id="notes" name="notes" class="form-control" 
                                            rows="3" placeholder="Add any additional details or milestones">{{ old('notes') }}</textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-100">Create Goal</button>
                                </div>
                            </form>
                        </div>

                        <!-- Existing Goals Tab -->
                        <div class="tab-pane fade" id="my-goals">
                            @if(isset($goals) && count($goals) > 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-group mb-3">
                                            <button class="btn btn-outline-primary active" data-filter="all">All</button>
                                            <button class="btn btn-outline-primary" data-filter="in-progress">In Progress</button>
                                            <button class="btn btn-outline-primary" data-filter="completed">Completed</button>
                                        </div>
                                    </div>
                                </div>

                                @foreach($goals as $goal)
                                    <div class="card mb-3 goal-card" data-status="{{ $goal->status }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="card-title">{{ $goal->goal }}</h5>
                                                <span class="badge bg-{{ $goal->status == 'completed' ? 'success' : 'primary' }}">
                                                    {{ ucfirst($goal->status) }}
                                                </span>
                                            </div>
                                            
                                            <p class="card-text">Hobbies:</p>
                                            <ul>
                                                @foreach($goal->hobbies as $hobby)
                                                    <li>{{ $hobby['name'] }} - <strong>{{ $hobby['experience'] }}</strong></li>
                                                @endforeach
                                            </ul>
                                            
                                            @if($goal->deadline)
                                                <div class="d-flex justify-content-between">
                                                    <small>Deadline: {{ $goal->deadline->format('Y-m-d') }}</small>
                                                </div>
                                            @endif

                                            @if($goal->notes)
                                                <p class="card-text"><small class="text-muted">Notes: {{ $goal->notes }}</small></p>
                                            @endif

                                            <div class="progress mt-2 mb-2">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $goal->progress }}%"
                                                     aria-valuenow="{{ $goal->progress }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ $goal->progress }}%
                                                </div>
                                            </div>

                                            <!-- Add Milestone Section -->
                                            <div class="milestones-section mt-3">
                                                <h6 class="mb-2">Milestones</h6>
                                                
                                                <!-- Display Existing Milestones -->
                                                @if($goal->milestones && count($goal->milestones) > 0)
                                                    <ul class="list-group mb-2">
                                                        @foreach($goal->milestones as $milestone)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <input type="checkbox" 
                                                                           class="form-check-input me-2 milestone-checkbox"
                                                                           data-goal-id="{{ $goal->id }}"
                                                                           data-milestone-id="{{ $milestone->id }}"
                                                                           {{ $milestone->completed ? 'checked' : '' }}>
                                                                    {{ $milestone->description }}
                                                                </div>
                                                                <small class="text-muted">Due: {{ $milestone->due_date->format('Y-m-d') }}</small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                                <!-- Add Milestone Form -->
                                                <form method="POST" action="{{ route('goals.milestones.store', $goal) }}" class="milestone-form">
                                                    @csrf
                                                    <div class="input-group">
                                                        <input type="text" name="description" class="form-control" 
                                                               placeholder="Add a milestone" required>
                                                        <input type="date" name="due_date" class="form-control"
                                                               min="{{ date('Y-m-d') }}" required>
                                                        <button type="submit" class="btn btn-outline-secondary">Add</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <!-- Add Delete Button -->
                                            <form method="POST" action="{{ route('goals.destroy', $goal) }}" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete Goal</button>
                                            </form>
                                        </div>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
@endsection