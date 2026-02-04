<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForcePasswordController
{
    /**
     * Show force password change screen
     */
    public function edit()
    {
        return view('auth.force-password-change');
    }

    /**
     * Update password and release user
     */
    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password'              => Hash::make($request->password),
            'force_password_change' => 0, // 🔥 CRITICAL FIX
        ]);

        // 🔐 Prevent session fixation
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
