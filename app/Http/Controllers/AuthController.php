<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status !== 'Active') {
                Auth::logout();
                return back()->with('error', 'Your account is deactivated. Please contact the administrator.');
            }

            $user->update(['last_login_at' => now()]);

            ActivityLog::record(
                'Login',
                'Authentication',
                $user->id,
                "User {$user->name} logged in successfully."
            );

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', "Welcome back, {$user->name}!");
        }

        return back()->with('error', 'Invalid email or password. Please try again.')->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::record(
                'Logout',
                'Authentication',
                Auth::id(),
                "User " . Auth::user()->name . " logged out."
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been safely signed out.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        ActivityLog::record('Update', 'Users', $user->id, "User {$user->name} updated their profile.");

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password does not match.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record('Update Password', 'Users', $user->id, "User {$user->name} changed their password.");

        return back()->with('success', 'Password updated successfully.');
    }
}
