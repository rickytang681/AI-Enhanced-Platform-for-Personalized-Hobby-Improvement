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
<body class="auth-layout">
    <div id="app">
        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container">
                <!-- Top Row -->
                <div class="w-100 d-flex justify-content-between align-items-center mb-2">
                    <!-- Left Side - Brand and User Info -->
                    <div class="d-flex align-items-center">
                        <a class="navbar-brand" href="{{ url('/dashboard') }}">
                            <i class="bi bi-lightbulb-fill text-primary"></i> 
                            <span class="brand-text">{{ config('app.name', 'Hobby Improvement') }}</span>
                        </a>
                        @auth
                            <div class="user-info ms-3 d-flex align-items-center">
                                <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}" 
                                     class="profile-image" 
                                     alt="Profile Picture">
                                <span class="ms-2 user-name">{{ Auth::user()->name }}</span>
                            </div>
                        @endauth
                    </div>

                    <!-- Right Side - Navigation Links -->
                    <div class="navbar-nav">
                        <div class="d-flex align-items-center">
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
                        </div>
                    </div>
                </div>

                <!-- Bottom Row - Main Navigation -->
                <div class="w-100">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-house-door"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('goals*') ? 'active' : '' }}" href="{{ route('goals.index') }}">
                                    <i class="bi bi-trophy"></i> Goals
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('library') ? 'active' : '' }}" href="{{ route('library') }}">
                                    <i class="bi bi-book"></i> Library
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('community*') ? 'active' : '' }}" href="{{ route('community.index') }}">
                                    <i class="bi bi-people"></i> Community
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('recommendation') ? 'active' : '' }}" href="{{ route('recommendation') }}">
                                    <i class="bi bi-stars"></i> Recommendations
                                </a>
                            </li>
                            @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('system') ? 'active' : '' }}" href="{{ route('system') }}">
                                    <i class="bi bi-gear"></i> System
                                </a>
                            </li>
                            @endif
                        </ul>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @stack('scripts')
</body>
</html>
