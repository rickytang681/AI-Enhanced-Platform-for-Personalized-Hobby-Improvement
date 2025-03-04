@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email Input --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-12 col-form-label">{{ __('Email Address:') }}</label>
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="row mb-3">
                            <label for="password" class="col-md-12 col-form-label">{{ __('Password:') }}</label>
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" 
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Login Button --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>

                        {{-- Forgot Password --}}
                        @if (Route::has('password.request'))
                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-size: 1.25rem;
    padding: 1rem;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    padding: 0.5rem 0;
    font-size: 1.1rem;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.form-control {
    padding: 0.5rem 0.75rem;
}

.form-check-label {
    color: #6c757d;
}

.text-center a {
    color: #6c757d;
    text-decoration: none;
    font-size: 0.9rem;
}

.text-center a:hover {
    color: #0d6efd;
}

.btn-link {
    color: #6c757d;
    text-decoration: none;
}

.btn-link:hover {
    color: #0d6efd;
}

/* Additional styles for login page */
.form-check {
    margin-top: 0.5rem;
}

.invalid-feedback {
    font-size: 0.875rem;
}
</style>
@endsection
