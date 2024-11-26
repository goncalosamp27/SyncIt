<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;
use App\Models\Member;
use App\Models\Admin;


class LoginController extends Controller
{

    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/login');
        } else {
            return view('auth.login');
        }
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        // Validate incoming request
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to authenticate as Admin (use email only)
        if (filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)) {
            $admin = Admin::where('email', $credentials['login'])->first();
    
            if ($admin && Auth::guard('admin')->attempt(['email' => $credentials['login'], 'password' => $credentials['password']], $request->filled('remember'))) {
                $request->session()->regenerate();
    
                return redirect()->intended('/home');
            }
        }
            

        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }


    /**
     * Log out the user from application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')
            ->withSuccess('You have logged out successfully!');
    }
    
}
