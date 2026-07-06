<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * show login form
     */
    public function showLoginForm()
    {
        // redirect if already logged in
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.auth.login');
    }

    /**
     * handle login
     */
    public function login(Request $request)
    {
        // redirect if already logged in (safety check)
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'please enter your email',
            'password.required' => 'please enter your password',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            return redirect()->route('home')
                ->with('success', "welcome back, {$user->name}!");
        }

        return back()
            ->withErrors(['email' => 'incorrect email or password.'])
            ->withInput($request->only('email'));
    }

    /**
     * logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'logged out successfully.');
    }
}
