@extends('layouts.logoutHeader')

@section('styles')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #0d6efd;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }
    .progress {
        background-color: #e9ecef;
    }
    .card {
        border: 1px solid rgba(0,0,0,.125);
    }
    .milestone-completed {
        text-decoration: line-through;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row g-4">
        <!-- Overview Card -->
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Overview</h4>
                    <div class="row g-4">
                        <!-- Hobbies & Goals Section -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-5 me-2">🎯</span>
                                <h5 class="mb-0">Your Hobbies</h5>
                            </div>
                            @if($hobbies->isEmpty())
                                <p class="text-muted">No hobbies added yet</p>
                            @else
                                <div class="accordion" id="hobbiesAccordion">
                                    @foreach($hobbies as $hobby)
                                        <div class="accordion-item mb-2">
                                            <h2 class="accordion-header" id="hobby-{{ $hobby->id }}-header">
                                                <button class="accordion-button collapsed" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#hobby-{{ $hobby->id }}-content">
                                                    <div class="d-flex justify-content-between align-items-center w-100">
                                                        <strong>{{ $hobby->name }}</strong>
                                                        <span class="badge bg-primary ms-2">
                                                            {{ $hobby->completed_goals_count }}/{{ $hobby->goals_count }} Goals
                                                        </span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="hobby-{{ $hobby->id }}-content" 
                                                 class="accordion-collapse collapse" 
                                                 aria-labelledby="hobby-{{ $hobby->id }}-header" 
                                                 data-bs-parent="#hobbiesAccordion">
                                                <div class="accordion-body">
                                                    @if($hobby->goals->isEmpty())
                                                        <p class="text-muted small">No goals set for this hobby yet</p>
                                                    @else
                                                        @foreach($hobby->goals->take(2) as $goal)
                                                            <div class="card mb-2">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="card-title mb-0">{{ $goal->goal }}</h6>
                                                                        <span class="badge {{ $goal->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                                                                            {{ ucfirst($goal->status) }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="progress mb-2" style="height: 10px;">
                                                                        <div class="progress-bar {{ $goal->progress == 100 ? 'bg-success' : '' }}" 
                                                                             role="progressbar" 
                                                                             style="width: {{ $goal->progress }}%" 
                                                                             aria-valuenow="{{ $goal->progress }}" 
                                                                             aria-valuemin="0" 
                                                                             aria-valuemax="100">
                                                                        </div>
                                                                    </div>
                                                                    @if($goal->milestones->isNotEmpty())
                                                                        <div class="milestones mt-2">
                                                                            <small class="text-muted d-block mb-1">Key Milestones:</small>
                                                                            @foreach($goal->milestones->take(2) as $milestone)
                                                                                <div class="d-flex align-items-center small mb-1">
                                                                                    <i class="bi {{ $milestone->completed ? 'bi-check-circle-fill text-success' : 'bi-circle' }} me-2"></i>
                                                                                    <span class="{{ $milestone->completed ? 'text-decoration-line-through' : '' }}">
                                                                                        {{ Str::limit($milestone->description, 30) }}
                                                                                    </span>
                                                                                </div>
                                                                            @endforeach
                                                                            @if($goal->milestones->count() > 2)
                                                                                <small class="text-muted">+ {{ $goal->milestones->count() - 2 }} more milestones</small>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @if($hobby->goals->count() > 2)
                                                            <div class="text-center mt-2">
                                                                <a href="{{ route('goals.index') }}" class="btn btn-sm btn-outline-primary">
                                                                    View All Goals ({{ $hobby->goals->count() }})
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Quick Links Section -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-5 me-2">✅</span>
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('hobbies.index') }}" class="btn btn-outline-primary btn-sm">
                                    ➕ Create a new hobby
                                </a>
                                <a href="{{ route('goals.index') }}" class="btn btn-outline-success btn-sm">
                                    🎯 Set a goal
                                </a>
                                <a href="{{ route('recommendation') }}" class="btn btn-outline-info btn-sm">
                                    💡 Get AI recommendations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Highlights -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <span class="fs-5 me-2">🏘️</span>
                            <h5 class="mb-0">Community Highlights</h5>
                        </div>
                        <a href="{{ route('community.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    @if($recentCommunityPosts->isEmpty())
                        <p class="text-muted">No recent posts</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentCommunityPosts as $post)
                                <a href="{{ route('community.index') }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ Str::limit($post->title, 30) }}</h6>
                                        <small>{{ $post->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Library Resources -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <span class="fs-5 me-2">📚</span>
                            <h5 class="mb-0">Recent Resources</h5>
                        </div>
                        <a href="{{ route('library') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    @if($popularResources->isEmpty())
                        <p class="text-muted">No resources available</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($popularResources as $resource)
                                <a href="{{ route('library') }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ Str::limit($resource->title, 30) }}</h6>
                                        <small>{{ $resource->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
