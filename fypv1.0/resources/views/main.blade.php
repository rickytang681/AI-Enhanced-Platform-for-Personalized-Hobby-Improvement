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
</head>
<body>
    <div class="container main-container">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
            <a class="navbar-brand header-logo" href="#">Hobby Improvement</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
            </div>
        </nav>

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
                    <form>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter your username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter your password">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">Register</a> | 
                        <a href="#" class="text-decoration-none">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-4">
            <a href="#" class="text-decoration-none me-3">Terms of Service</a>
            <a href="#" class="text-decoration-none me-3">Privacy Policy</a>
            <a href="#" class="text-decoration-none">Help</a>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
