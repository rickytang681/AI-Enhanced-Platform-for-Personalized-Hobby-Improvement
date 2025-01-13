@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6"> <!-- Adjusted column width to match design -->
            <div class="card">
                <div class="card-header text-center">{{ __('Login') }}</div> <!-- Centered header -->

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Username/Email Input --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-12 col-form-label">{{ __('Username/Email:') }}</label>
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
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
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100"> <!-- Full-width button -->
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>

                        {{-- Forgot Password --}}
                        @if (Route::has('password.request'))
                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>

                    {{-- Footer Links --}}
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
</div>
@endsection
