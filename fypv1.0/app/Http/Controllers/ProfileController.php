<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
    return view('profile');
    }

    public function update(Request $request)
    {
        // Logic to update profile details
        return back()->with('success', 'Profile updated successfully!');
    }

}
