<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('profile');
    }

    public function update(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'hobbies' => 'required|string',
            'experience' => 'required|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image file
        ]);
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                \Storage::delete('public/' . $user->profile_picture);
            }
    
            // Store new profile picture
            $filePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $filePath;
        }
    
        // Update user fields
        $user->name = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->phone = $validatedData['phone'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->hobbies = $validatedData['hobbies'];
        $user->experience = $validatedData['experience'];
    
        // Save the changes
        $user->save();
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    

    public function editProfile()
    {
        $user = auth()->user(); // Get the authenticated user
        return view('profile', compact('user'));
    }

}
