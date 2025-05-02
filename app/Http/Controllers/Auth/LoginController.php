<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //To display the login form and redirect to the login page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    //To authenticate the user and redirect to the appropriate dashboard
    public function login(Request $request)
    {
        //Validate the request to ensure the email, password and role are present
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:student,lecturer,coordinator']
        ]);

        // Get role from request
        $role = $request->role;

        // Attempt to authenticate
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            // Check if authenticated user's role matches selected role
            if (Auth::user()->role !== $role) {
                Auth::logout();
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'role' => 'The selected role does not match your account type.'
                    ]);
            }

            $request->session()->regenerate();

            // Redirect based on role
            return redirect()->intended($this->redirectTo());
        }

        //If the authentication fails, redirect back to the login page with the email and an error message
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }

    //To redirect the user to the appropriate dashboard based on their role
    protected function redirectTo()
    {
        if (Auth::user()->isStudent()) {
            return route('student.dashboard', ['user_id' => Auth::user()->id]);
        } elseif (Auth::user()->isLecturer()) {
            return route('lecturer.dashboard', ['user_id' => Auth::user()->id]);
        } elseif (Auth::user()->isCoordinator()) {
            return route('coordinator.dashboard');
        }

        return RouteServiceProvider::HOME;
    }

    //To redirect the user to the appropriate dashboard based on their role
    protected function authenticated(Request $request, $user)
    {
        if ($user->isStudent()) {
            return redirect()->route('student.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isLecturer()) {
            return redirect()->route('lecturer.dashboard', ['user_id' => $user->id]);
        } elseif ($user->isCoordinator()) {
            return redirect()->route('coordinator.dashboard');
        }

        return redirect('/');
    }

    //To display the change password form
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    //To change the password of the user
    public function changePassword(Request $request)
    {
        //Validate the request to ensure the current password and new password are present
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}
