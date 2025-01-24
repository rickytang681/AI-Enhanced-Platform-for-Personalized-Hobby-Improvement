@extends('layouts.logoutHeader')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container profile-container">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div class="text-center">
                <div class="profile-picture">Profile Picture</div>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="XXX XXXXX">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="xxxxxxx@gmail.com">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number:</label>
                <div class="input-group">
                    <span class="input-group-text">+60</span>
                    <input type="text" class="form-control" id="phone" name="phone" value="XXX XXXX XXXX">
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" value="XXXXXXXX">
            </div>
            <div class="mb-3">
                <label for="hobbies" class="form-label">Hobbies:</label>
                <select class="form-select" id="hobbies" name="hobbies">
                    <option value="Reading" selected>Reading</option>
                    <option value="Gaming">Gaming</option>
                    <option value="Cooking">Cooking</option>
                    <option value="Traveling">Traveling</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="experience" class="form-label">Experience Level:</label>
                <select class="form-select" id="experience" name="experience">
                    <option value="Beginner" selected>Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
            <button type="submit" class="btn btn-save w-100">Save and Change</button>
        </form>
        <footer>
            <a href="#">Terms of Service</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Help</a>
            <a href="#">Contact Us</a>
            <a href="#">About Us</a>
            <a href="#">Logout</a>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection