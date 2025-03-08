@extends('layouts.logoutHeader')

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
                                <ul class="list-unstyled">
                                    @foreach($hobbies->take(3) as $hobby)
                                        <li class="mb-2">
                                            <strong>{{ $hobby->name }}</strong>
                                            <div class="small text-muted">
                                                Goals: {{ $hobby->completed_goals_count }}/{{ $hobby->goals_count }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
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
