@extends('layouts.logoutHeader')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal Setting Section</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Goal Setting Section</h4>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
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

                    <form method="POST" action="{{ route('goals.store') }}">
                        @csrf
                        <!-- Goal -->
                        <div class="mb-3">
                            <label for="goal" class="form-label">Goal:</label>
                            <input type="text" id="goal" name="goal" class="form-control" value="{{ old('goal') }}" required>
                        </div>

                        <!-- Hobby Selection from User's Hobbies -->
                        <div class="mb-3">
                            <label for="hobby" class="form-label">Related Hobby:</label>
                            <select id="hobby" name="hobby" class="form-select" required>

                            </select>
                        </div>

                        <!-- Deadline -->
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline:</label>
                            <input type="date" id="deadline" name="deadline" class="form-control" value="{{ old('deadline') }}">
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Save Goal</button>
                        </div>
                    </form>

                    <!-- Display Existing Goals -->
                    @if(isset($goals) && count($goals) > 0)
                        <h5 class="mt-4">Your Goals</h5>
                        @foreach($goals as $goal)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $goal->goal }}</h6>
                                    <p class="card-text">Hobby: {{ $goal->hobby }}</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Deadline: {{ $goal->deadline }}</small>
                                        <span class="badge bg-{{ $goal->status == 'completed' ? 'success' : 'primary' }}">
                                            {{ $goal->status }}
                                        </span>
                                    </div>
                                    <div class="progress mt-2 mb-2">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $goal->progress }}%"
                                             aria-valuenow="{{ $goal->progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $goal->progress }}%
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Update Form -->
                                    <form method="POST" action="{{ route('goals.update-progress', $goal) }}" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="input-group">
                                            <input type="number" name="progress" class="form-control" 
                                                   min="0" max="100" value="{{ $goal->progress }}"
                                                   placeholder="Update progress">
                                            <button type="submit" class="btn btn-outline-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Footer Links -->
                    <div class="text-center mt-3">
                        <a href="#">Terms of Service</a> |
                        <a href="#">Privacy Policy</a> |
                        <a href="#">Help</a> |
                        <a href="#">Contact Us</a> |
                        <a href="#">About Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection