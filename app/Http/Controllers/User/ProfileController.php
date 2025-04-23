<?php

// app/Http/Controllers/User/ProfileController.php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user'));
    }
    
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);
        
        // Update user
        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);
        
        return redirect()->route('user.profile.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }
    
    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Update password
        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('user.profile.index')
            ->with('success', 'Password berhasil diperbarui!');
    }
}