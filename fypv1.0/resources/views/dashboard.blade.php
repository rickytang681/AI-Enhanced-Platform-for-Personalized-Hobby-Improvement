@extends('layouts.logoutHeader')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Login Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <!-- Navigation Bar -->
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div class="logo">
                <h2>Logo</h2>
            </div>
            <div class="profile-dropdown">
                <img src="https://via.placeholder.com/50" alt="User Profile Picture" class="rounded-circle">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link p-0">Logout</button>
                </form>
            </div>
        </header>

        <!-- Navigation Buttons -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="home" class="btn btn-outline-primary">Dashboard</a>
            <a href="recommendation" class="btn btn-outline-primary">Recommendations</a>
            <a href="goal" class="btn btn-outline-primary">Goals</a>
            <a href="library" class="btn btn-outline-primary">Resource Library</a>
            <a href="progresdTracking" class="btn btn-outline-primary">Progress Tracking</a>
            <a href="milestone" class="btn btn-outline-primary">Milestones</a>
            <a href="Community" class="btn btn-outline-primary">Community</a>
            @if (auth()->user()->isAdmin())
                <a href="system" class="btn btn-outline-primary">System Administration</a>
            @endif
        </div>


        <!-- Dashboard Sections -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Recent Activities</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Progress Snapshot</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Recommendations</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Community Highlights</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-4 text-center">
            <a href="#" class="text-decoration-none me-3">Terms of Service</a>
            <a href="#" class="text-decoration-none me-3">Privacy Policy</a>
            <a href="#" class="text-decoration-none me-3">Help</a>
            <a href="#" class="text-decoration-none">Contact Us</a>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection