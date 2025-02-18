<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hobby Improvement') }}</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
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

                <!-- Bottom Row - Main Navigation -->
                <div class="w-100">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}">
                                    <i class="bi bi-house-door"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-info-circle"></i> About
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-envelope"></i> Contact
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #ffffff;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .nav-item a {
            font-weight: 500;
        }
        .btn-outline-primary {
            transition: all 0.2s ease-in-out;
        }
        .btn-outline-primary:hover {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .button-container {
            padding: 10px 0;
        }
        .profile-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            object-fit: cover;
        }
        .profile-image-small {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
        }
        .vertical-divider {
            width: 1px;
            height: 24px;
            background-color: #dee2e6;
            margin: 0 15px;
        }
        .nav-link {
            color: #495057;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #0d6efd;
        }
        .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
        }
    </style>

    @include('layouts.footer')
</body>
</html>
