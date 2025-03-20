@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Personalized Recommendations</h4>
                
                <!-- Hobby and Goal Selection Form -->
                <div class="mb-4">
                    <h5>Select Hobby & Goal</h5>
                    <form id="recommendationForm" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Hobby</label>
                            <select class="form-select" id="hobbySelect" name="selected_hobby" required>
                                <option value="">Choose a hobby...</option>
                                @foreach($hobbies as $hobby)
                                    <option value="{{ $hobby->id }}" {{ $selectedHobbyId == $hobby->id ? 'selected' : '' }}>
                                        {{ $hobby->name }} ({{ $hobby->experience_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Goal</label>
                            <select class="form-select" id="goalSelect" name="selected_goal" required>
                                <option value="">Choose a goal...</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="button" id="getRecommendations" class="btn btn-primary">
                                Get Recommendations
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Generating recommendations...</p>
                </div>

                <!-- Recommendations Display -->
                <div id="recommendationsContainer"></div>

                <!-- Previous Recommendations Section -->
                <div id="savedRecommendations">
                    @foreach($recommendations as $recommendation)
                        <div class="card mb-3 recommendation-card" data-id="{{ $recommendation->id }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">{{ $recommendation->created_at->diffForHumans() }}</small>
                                    <button class="btn btn-sm btn-danger delete-recommendation" data-id="{{ $recommendation->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="recommendation-content">
                                    {!! nl2br(e($recommendation->content)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hobbySelect = document.getElementById('hobbySelect');
    const goalSelect = document.getElementById('goalSelect');
    const selectedHobbyId = '{{ $selectedHobbyId }}';
    const selectedGoalId = '{{ $selectedGoalId }}';
    const autoGenerate = {{ $autoGenerate ? 'true' : 'false' }};

    // Load goals when hobby is selected
    async function loadGoals(hobbyId) {
        try {
            const response = await fetch(`/api/hobbies/${hobbyId}/goals`);
            if (!response.ok) throw new Error('Failed to fetch goals');
            const goals = await response.json();
            
            goalSelect.innerHTML = '<option value="">Choose a goal...</option>';
            goals.forEach(goal => {
                const option = new Option(goal.goal, goal.id);
                if (goal.id == selectedGoalId) {
                    option.selected = true;
                }
                goalSelect.add(option);
            });
            goalSelect.disabled = false;

            // If this is auto-generate mode and we have a selected goal
            if (autoGenerate && selectedGoalId) {
                document.getElementById('getRecommendations').click();
            }
        } catch (error) {
            console.error('Error loading goals:', error);
        }
    }

    // Initial load if hobby is selected
    if (selectedHobbyId) {
        loadGoals(selectedHobbyId);
    }

    // Event listener for hobby selection
    hobbySelect.addEventListener('change', function() {
        if (this.value) {
            loadGoals(this.value);
        } else {
            goalSelect.innerHTML = '<option value="">Choose a goal...</option>';
            goalSelect.disabled = true;
        }
    });

    // Handle goal selection
    goalSelect.addEventListener('change', async function() {
        milestonesContainer.style.display = 'none';
        milestonesList.innerHTML = '';
        
        if (this.value) {
            try {
                const response = await fetch(`/api/goals/${this.value}/milestones`);
                if (!response.ok) {
                    throw new Error('Failed to fetch milestones');
                }
                const goal = await response.json();
                
                if (goal.milestones && goal.milestones.length > 0) {
                    goal.milestones.forEach(milestone => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';
                        li.innerHTML = `
                            <i class="bi ${milestone.completed ? 'bi-check-circle-fill text-success' : 'bi-circle'} me-2"></i>
                            <span class="${milestone.completed ? 'text-decoration-line-through' : ''}">
                                ${milestone.description}
                            </span>
                            <small class="text-muted d-block ms-4">Due: ${milestone.due_date}</small>
                        `;
                        milestonesList.appendChild(li);
                    });
                    milestonesContainer.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching milestones:', error);
                alert('Error loading milestones');
            }
        }
    });

    // Handle recommendation request
    document.getElementById('getRecommendations').addEventListener('click', async function() {
        const hobbyId = hobbySelect.value;
        const goalId = goalSelect.value;

        if (!hobbyId) {
            alert('Please select a hobby');
            return;
        }
        if (!goalId) {
            alert('Please select a goal');
            return;
        }

        const loadingSpinner = document.getElementById('loadingSpinner');
        loadingSpinner.classList.remove('d-none');
        this.disabled = true;

        try {
            const response = await fetch('{{ route("recommendation.get") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    selected_hobbies: [hobbyId],
                    selected_goals: [goalId]
                })
            });

            const data = await response.json();

            if (data.success) {
                const newCard = document.createElement('div');
                newCard.className = 'card mb-3 recommendation-card';
                newCard.dataset.id = data.recommendation_id;
                
                // Convert newlines to <br> tags for proper formatting
                const formattedContent = data.recommendations.replace(/\n/g, '<br>');
                
                newCard.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-muted">Just now</small>
                            <button class="btn btn-sm btn-danger delete-recommendation" data-id="${data.recommendation_id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="recommendation-content">
                            ${formattedContent}
                        </div>
                    </div>
                `;

                const savedRecommendations = document.getElementById('savedRecommendations');
                savedRecommendations.insertBefore(newCard, savedRecommendations.firstChild);
            } else {
                alert(data.error || 'Failed to generate recommendations');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error generating recommendations');
        } finally {
            loadingSpinner.classList.add('d-none');
            this.disabled = false;
        }
    });

    // Delete recommendation handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-recommendation')) {
            const button = e.target.closest('.delete-recommendation');
            const recommendationId = button.dataset.id;
            const card = button.closest('.recommendation-card');

            if (confirm('Are you sure you want to delete this recommendation?')) {
                fetch(`/recommendations/${recommendationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        card.remove();
                    } else {
                        alert('Failed to delete recommendation');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting recommendation');
                });
            }
        }
    });
});
</script>
@endpush
