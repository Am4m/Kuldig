<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function dashboard(string $name): View
    {
        $user = User::where('name', $name)->firstOrFail();
        
        return view('profile.profile', [
            'user' => $user,
        ]);
    }

    public function indexJson(string $name): JsonResponse
    {
        $user = User::where('name', $name)->firstOrFail();
        return response()->json([
            'user' => $user,
        ]);
    }

     public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'max:1024'], // 1MB max
        ]);
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }
        
        // Update user information
        $user->email = $validated['email'];
        $user->bio = $validated['bio'] ?? null;
        $user->location = $validated['location'] ?? null;
        $user->save();
    
        return redirect()->route('profile.dashboard')->with('status', 'profile-updated');
    }
}