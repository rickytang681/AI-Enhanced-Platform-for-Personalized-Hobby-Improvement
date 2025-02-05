@extends('layouts.logoutHeader')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goal Setting Section</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
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

                        <!-- Hobby Input (Multiple Entries) -->
                        <div class="mb-3">
                            <label class="form-label">Hobbies & Experience Level:</label>
                            <div id="hobby-container">
                                <div class="hobby-entry mb-2 d-flex gap-2">
                                    <input type="text" name="hobbies[]" class="form-control" placeholder="Enter Hobby" required>
                                    <select name="experience[]" class="form-select" required>
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Expert">Expert</option>
                                    </select>
                                    <button type="button" class="btn btn-danger remove-hobby">X</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" id="add-hobby">+ Add Hobby</button>
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
                                    <p class="card-text">Hobbies:</p>
                                    <ul>
                                        @foreach(json_decode($goal->hobbies, true) as $hobby)
                                            <li>{{ $hobby['name'] }} - <strong>{{ $hobby['experience'] }}</strong></li>
                                        @endforeach
                                    </ul>
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
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Adding and Removing Hobbies -->
    <script>
        document.getElementById('add-hobby').addEventListener('click', function() {
            let container = document.getElementById('hobby-container');
            let entry = document.createElement('div');
            entry.classList.add('hobby-entry', 'mb-2', 'd-flex', 'gap-2');

            entry.innerHTML = `
                <input type="text" name="hobbies[]" class="form-control" placeholder="Enter Hobby" required>
                <select name="experience[]" class="form-select" required>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Expert">Expert</option>
                </select>
                <button type="button" class="btn btn-danger remove-hobby">X</button>
            `;
            
            container.appendChild(entry);

            entry.querySelector('.remove-hobby').addEventListener('click', function() {
                entry.remove();
            });
        });

        document.querySelectorAll('.remove-hobby').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.hobby-entry').remove();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection
