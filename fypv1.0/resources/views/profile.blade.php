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
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="text-center">
                <div class="profile-picture">
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                         alt="Profile Picture" class="rounded-circle" width="120" height="120">
                </div>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture:</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                @error('profile_picture')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ $user->name }}">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number:</label>
                <div class="input-group">
                    <span class="input-group-text">+60</span>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}">
                <small class="text-muted">Leave empty if you don't want to change the password.</small>
            </div>
            <div class="mb-3">
                <label for="hobbies" class="form-label">Hobbies:</label>
                <select class="form-select" id="hobbies" name="hobbies">
                    <option value="Reading" {{ $user->hobbies == 'Reading' ? 'selected' : '' }}>Reading</option>
                    <option value="Gaming" {{ $user->hobbies == 'Gaming' ? 'selected' : '' }}>Gaming</option>
                    <option value="Cooking" {{ $user->hobbies == 'Cooking' ? 'selected' : '' }}>Cooking</option>
                    <option value="Traveling" {{ $user->hobbies == 'Traveling' ? 'selected' : '' }}>Traveling</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="experience" class="form-label">Experience Level:</label>
                <select class="form-select" id="experience" name="experience">
                    <option value="Beginner" {{ $user->experience == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="Intermediate" {{ $user->experience == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Advanced" {{ $user->experience == 'Advanced' ? 'selected' : '' }}>Advanced</option>
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
            <a href="{{ route('logout') }}">Logout</a>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection
