<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect to dashboard profile if coming from dashboard
        if ($request->has('dashboard') || ($request->header('referer') && str_contains($request->header('referer'), 'dashboard'))) {
            return redirect()->route('dashboard.profile')->with('success', 'Password updated successfully!');
        }

        return back()->with('status', 'password-updated');
    }
}
