@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Personalized Recommendations</h4>
                
                <!-- Hobby and Goal Selection Form -->
                <div class="mb-4">
                    <h5>Select Hobbies & Goals</h5>
                    <form id="recommendationForm" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Hobbies</label>
                            <div class="hobby-selection">
                                @foreach($hobbies as $hobby)
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input hobby-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $hobby->id }}" 
                                                       id="hobby-{{ $hobby->id }}"
                                                       name="selected_hobbies[]">
                                                <label class="form-check-label" for="hobby-{{ $hobby->id }}">
                                                    {{ $hobby->name }} ({{ $hobby->experience_level }})
                                                </label>
                                            </div>
                                            
                                            @if($hobby->goals->isNotEmpty())
                                                <div class="ms-4 mt-2 goals-container" id="goals-{{ $hobby->id }}" style="display: none;">
                                                    <label class="form-label">Select Goals:</label>
                                                    @foreach($hobby->goals as $goal)
                                                        <div class="form-check">
                                                            <input class="form-check-input goal-checkbox" 
                                                                   type="checkbox" 
                                                                   value="{{ $goal->id }}" 
                                                                   id="goal-{{ $goal->id }}"
                                                                   name="selected_goals[]"
                                                                   data-hobby="{{ $hobby->id }}">
                                                            <label class="form-check-label" for="goal-{{ $goal->id }}">
                                                                {{ $goal->title }}
                                                                <span class="badge {{ $goal->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                                                                    {{ ucfirst($goal->status) }}
                                                                </span>
                                                            </label>
                                                            
                                                            <!-- Goal Details Section -->
                                                            <div class="goal-details ms-4 mt-2">
                                                                <div class="progress mb-2" style="height: 10px;">
                                                                    <div class="progress-bar {{ $goal->progress == 100 ? 'bg-success' : 'bg-primary' }}" 
                                                                         role="progressbar" 
                                                                         style="width: {{ $goal->progress }}%" 
                                                                         aria-valuenow="{{ $goal->progress }}" 
                                                                         aria-valuemin="0" 
                                                                         aria-valuemax="100">
                                                                        {{ $goal->progress }}%
                                                                    </div>
                                                                </div>
                                                                
                                                                @if($goal->milestones->isNotEmpty())
                                                                    <div class="milestones-list small">
                                                                        <strong>Milestones:</strong>
                                                                        <ul class="list-unstyled mb-0">
                                                                            @foreach($goal->milestones as $milestone)
                                                                                <li class="ms-2">
                                                                                    <i class="bi {{ $milestone->completed ? 'bi-check-circle-fill text-success' : 'bi-circle' }} me-1"></i>
                                                                                    <span class="{{ $milestone->completed ? 'text-decoration-line-through' : '' }}">
                                                                                        {{ $milestone->description }}
                                                                                    </span>
                                                                                    <small class="text-muted">(Due: {{ $milestone->due_date->format('M d, Y') }})</small>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <button type="button" id="getRecommendations" class="btn btn-primary w-100">
                            <i class="bi bi-lightbulb"></i> Get Recommendations
                        </button>
                    </form>
                </div>

                <!-- Previous Recommendations Section -->
                <div class="previous-recommendations-header">
                    <div class="d-flex align-items-center mb-4">
                        <div class="recommendation-icon me-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Previous Recommendations</h5>
                            <small class="text-muted">Your personalized improvement suggestions</small>
                        </div>
                    </div>
                </div>
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

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Generating personalized recommendations...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle goals when hobby is selected
    document.querySelectorAll('.hobby-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const goalsContainer = document.getElementById(`goals-${this.value}`);
            if (goalsContainer) {
                goalsContainer.style.display = this.checked ? 'block' : 'none';
                
                // Uncheck all goals when hobby is unselected
                if (!this.checked) {
                    goalsContainer.querySelectorAll('.goal-checkbox').forEach(goalCheckbox => {
                        goalCheckbox.checked = false;
                    });
                }
            }
        });
    });

    // Handle recommendation request
    document.getElementById('getRecommendations').addEventListener('click', async function() {
        const selectedHobbies = Array.from(document.querySelectorAll('.hobby-checkbox:checked')).map(cb => cb.value);
        const selectedGoals = Array.from(document.querySelectorAll('.goal-checkbox:checked')).map(cb => cb.value);

        if (selectedHobbies.length === 0) {
            alert('Please select at least one hobby');
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
                    selected_hobbies: selectedHobbies,
                    selected_goals: selectedGoals
                })
            });

            const data = await response.json();

            if (data.success) {
                // Create new recommendation card
                const newCard = document.createElement('div');
                newCard.className = 'card mb-3 recommendation-card';
                newCard.dataset.id = data.recommendation_id;
                newCard.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-muted">Just now</small>
                            <button class="btn btn-sm btn-danger delete-recommendation" data-id="${data.recommendation_id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="recommendation-content">
                            ${data.recommendations.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                `;

                const savedRecommendations = document.getElementById('savedRecommendations');
                savedRecommendations.insertBefore(newCard, savedRecommendations.firstChild);
            } else {
                alert(data.error || 'Failed to get recommendations');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error getting recommendations');
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
