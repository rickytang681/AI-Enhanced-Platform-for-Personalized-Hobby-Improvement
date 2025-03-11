@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center align-items-center">
        <!-- Left Section -->
        <div class="col-md-6">
            <div class="card p-4 text-center">
                <img src="{{ asset('storage/banner/hobby-improvement-banner.jpg') }}" alt="Banner" class="img-fluid rounded mb-4">
                <h2 class="brand-text mb-3">Welcome to Hobby Improvement</h2>
                <p class="lead mb-4">
                    Your personal AI-enhanced platform for hobby improvement. 
                    Track your progress, set goals, and receive personalized recommendations 
                    to enhance your skills and connect with like-minded individuals.
                </p>
            </div>
        </div>

        <!-- Right Section (Login Form) -->
        <div class="col-md-6">
            <div class="card p-4">
                <h4 class="text-center mb-4">Login</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required>
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember" 
                               name="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </a>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-muted">
                                Forgot Password?
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
