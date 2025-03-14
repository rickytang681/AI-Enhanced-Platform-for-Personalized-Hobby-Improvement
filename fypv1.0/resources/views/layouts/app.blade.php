<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hobby Improvement') }}</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="guest-layout">
    <div id="app">
        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container">
                <!-- Top Row -->
                <div class="w-100 d-flex justify-content-between align-items-center mb-2">
                    <!-- Left Side - Brand -->
                    <div class="d-flex align-items-center">
                        <a class="navbar-brand" href="{{ url('/') }}">
                            <i class="bi bi-lightbulb-fill text-primary"></i> 
                            <span class="brand-text">{{ config('app.name', 'Hobby Improvement') }}</span>
                        </a>
                    </div>

                    <!-- Center - Main Navigation -->
                    <div class="navbar-nav mx-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                    <i class="bi bi-house-door"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('about') ? 'active' : '' }}" href="{{ url('/aboutUs') }}">
                                    <i class="bi bi-info-circle"></i> About
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="{{ url('/contactUs') }}">
                                    <i class="bi bi-envelope"></i> Contact
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Right Side - Auth Links -->
                    <div class="navbar-nav">
                        <div class="d-flex align-items-center">
                            @guest
                                @if (Route::has('login'))
                                    <a class="nav-link me-3" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Login') }}
                                    </a>
                                @endif

                                @if (Route::has('register'))
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-2"></i>{{ __('Register') }}
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('profile') }}" class="nav-link me-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}" 
                                             class="profile-image-small me-2" 
                                             alt="Profile">
                                        <span>Profile</span>
                                    </div>
                                </a>
                                <div class="vertical-divider"></div>
                                <a href="{{ route('logout') }}" 
                                   class="nav-link text-danger ms-3"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @include('layouts.footer')

    <!-- Scripts -->
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>