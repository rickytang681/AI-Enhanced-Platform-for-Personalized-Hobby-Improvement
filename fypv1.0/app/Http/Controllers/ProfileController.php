<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20048'
        ]);

        // Update basic info
        $user->name = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Handle password update
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && file_exists(public_path('storage/' . $user->profile_picture))) {
                unlink(public_path('storage/' . $user->profile_picture));
            }
            
            // Create directory if it doesn't exist
            $path = public_path('storage/profile-pictures');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Store the new image
            $fileName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move($path, $fileName);
            $user->profile_picture = 'profile-pictures/' . $fileName;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    

    public function editProfile()
    {
        $user = auth()->user(); // Get the authenticated user
        return view('profile', compact('user'));
    }

}
