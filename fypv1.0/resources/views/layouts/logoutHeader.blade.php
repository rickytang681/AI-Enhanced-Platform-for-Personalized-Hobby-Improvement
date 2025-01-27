<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hobby Improvement') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <!-- Left Side - Brand -->
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <i class="bi bi-lightbulb"></i> {{ config('app.name', 'Hobby Improvement') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            <!-- No links for guests -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="/profile">
                                            <i class="bi bi-person"></i> Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Navigation Buttons -->
        <div class="container button-container">
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="home" class="btn btn-outline-primary">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <a href="recommendation" class="btn btn-outline-primary">
                    <i class="bi bi-stars"></i> Recommendations
                </a>
                <a href="goal" class="btn btn-outline-primary">
                    <i class="bi bi-flag"></i> Goals
                </a>
                <a href="library" class="btn btn-outline-primary">
                    <i class="bi bi-bookshelf"></i> Resource Library
                </a>
                <a href="progressTracking" class="btn btn-outline-primary">
                    <i class="bi bi-bar-chart-line"></i> Progress Tracking
                </a>
                <a href="milestone" class="btn btn-outline-primary">
                    <i class="bi bi-award"></i> Milestones
                </a>
                <a href="community" class="btn btn-outline-primary">
                    <i class="bi bi-people"></i> Community
                </a>
                @if (auth()->user()->isAdmin())
                    <a href="system" class="btn btn-outline-primary">
                        <i class="bi bi-gear"></i> System Administration
                    </a>
                @endif
            </div>
        </div>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS (including Popper.js for dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
