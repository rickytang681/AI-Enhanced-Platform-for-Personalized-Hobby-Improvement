@extends('layouts.logoutHeader')

@section('content')

<div class="container profile-container">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="text-center">
            <div class="profile-picture">
                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}" 
                     alt="Profile Picture" class="rounded-circle" width="105" height="140"> 
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
        <button type="submit" class="btn btn-save w-100">Save and Change</button>
    </form>
</div>
@endsection