@extends('layouts.logoutHeader')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="text-center mb-4">Personalized Recommendations</h4>
                
                <!-- Current Hobbies Overview -->
                <div class="mb-4">
                    <h5>Your Current Hobbies</h5>
                    @if($hobbies->isEmpty())
                        <p class="text-muted">No hobbies added yet. Add some hobbies to get personalized recommendations!</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Hobby</th>
                                        <th>Goals</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hobbies as $hobby)
                                        <tr>
                                            <td>{{ $hobby->name }}</td>
                                            <td>{{ $hobby->goals->count() }}</td>
                                            <td>
                                                @php
                                                    $completed = $hobby->goals->where('status', 'completed')->count();
                                                    $total = $hobby->goals->count();
                                                    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
                                                @endphp
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $progress }}%" 
                                                         aria-valuenow="{{ $progress }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">{{ $progress }}%</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- AI Recommendations Section -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">AI Recommendations</h5>
                        <button id="getRecommendations" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-clockwise"></i> Get New Recommendations
                        </button>
                    </div>

                    <!-- Saved Recommendations -->
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

                    <!-- New Recommendation Container -->
                    <div id="recommendationsContainer" class="d-none">
                        <div class="card">
                            <div class="card-body">
                                <div id="recommendationsContent"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const getRecommendationsBtn = document.getElementById('getRecommendations');
    const recommendationsContainer = document.getElementById('recommendationsContainer');
    const recommendationsContent = document.getElementById('recommendationsContent');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const savedRecommendations = document.getElementById('savedRecommendations');

    // Delete recommendation handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-recommendation')) {
            const button = e.target.closest('.delete-recommendation');
            const recommendationId = button.dataset.id;
            const card = button.closest('.recommendation-card');

            if (confirm('Are you sure you want to delete this recommendation?')) {
                fetch(`/recommendation/${recommendationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                })
                .then(response => response.json())
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

    getRecommendationsBtn.addEventListener('click', async function() {
        try {
            loadingSpinner.classList.remove('d-none');
            recommendationsContainer.classList.add('d-none');
            getRecommendationsBtn.disabled = true;

            const response = await fetch('{{ route("recommendation.get") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const formattedContent = data.recommendations.replace(/\n/g, '<br>');
                
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
                            ${formattedContent}
                        </div>
                    </div>
                `;

                // Add the new card at the top
                savedRecommendations.insertBefore(newCard, savedRecommendations.firstChild);
            } else {
                alert(data.error || 'Failed to get recommendations');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error getting recommendations');
        } finally {
            loadingSpinner.classList.add('d-none');
            getRecommendationsBtn.disabled = false;
        }
    });
});
</script>

<style>
.recommendation-card {
    transition: all 0.3s ease;
}

.recommendation-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.recommendation-content {
    white-space: pre-line;
}
</style>
@endpush
