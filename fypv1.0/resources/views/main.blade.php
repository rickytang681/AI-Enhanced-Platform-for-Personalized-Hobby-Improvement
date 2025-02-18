@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hobby Improvement Platform</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* Additional custom styles can go here */
        body {
            background-color: #f8f9fa; /* Example background color */
        }
        .card {
            background-color: #fff; /* Example card background color */
        }
        .btn-custom {
            background-color: #007bff; /* Example button color */
            border-color: #007bff;
        }
        .btn-custom:hover {
            background-color: #0056b3; /* Example hover color */
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <!-- Main Content -->
        <div class="row align-items-center">
            <!-- Left Section -->
            <div class="col-md-6 text-center">
                <img src="https://via.placeholder.com/400x200" alt="Banner" class="img-fluid rounded mb-3">
                <p class="welcome-text">
                    Welcome to Hobby Improvement Platform, your personal AI-enhanced platform for hobby improvement. 
                    Track your progress, set goals, and receive personalized recommendations to enhance your skills 
                    and connect with like-minded individuals.
                </p>
            </div>

            <!-- Right Section (Login Form) -->
            <div class="col-md-6">
                <div class="card shadow p-4">
                    <h5 class="card-title text-center">Login</h5>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf <!-- Laravel CSRF protection -->

                        {{-- Username/Email Input --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your username/email" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Password Input --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-custom w-100">Login</button>
                    </form>

                    {{-- Links for Register and Forgot Password --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-decoration-none">Register</a> | 
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection