<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('user.profileEdit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'           => 'nullable|string|max:15',
            'address'         => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($user->profile_picture && file_exists(public_path('storage/' . $user->profile_picture))) {
                unlink(public_path('storage/' . $user->profile_picture));
            }

            // Store the new profile picture
            $path                  = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // Update user data
        $user->update($request->only('name', 'email', 'phone', 'address'));

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
