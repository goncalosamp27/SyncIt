<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\Member;
use App\Models\Admin;

class RegisterController extends Controller
{
    /**
     * Display a registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'username' => 'required|alpha_num|min:3|max:50',
            'display_name' => 'required|regex:/^[A-Za-z0-9_ ]+$/|min:3|max:50',
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (Member::where('email', $value)->exists() || Admin::where('email', $value)->exists()) {
                        $fail('The email address is already taken.');
                    }
                },
            ],
            'password' => 'required|min:8|max:100|confirmed',
            'bio' => 'nullable|regex:/^[A-Za-z0-9_.,?!\s]*$/|max:200',
            'profile_pic_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check if the email is already taken
        if (Member::where('email', $validatedData['email'])->exists()) {
            return back()->withErrors(['email' => 'The email address is already taken.'])->withInput();
        }

        // Add the hashed password and default values
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['profile_pic_url'] = 'default_user.png';

        try {
            
            if ($request->hasFile('profile_pic_url')) {
                $path = $request->file('profile_pic_url')->store('profile', 'public');
                $validatedData['profile_pic_url'] = basename($path);
            }

            $result = Member::createMember($validatedData);

            if ($result instanceof \Illuminate\Support\MessageBag) {
                return back()->withErrors($result)->withInput();
            }


            if(!(Auth::guard('admin')->check())) {
                Auth::attempt($request->only('email', 'password'));
                Auth::login($result);
            }
            
            if (Auth::check()) return redirect()->route('home')->withSuccess('You have successfully registered & logged in!');
            else if (Auth::guard('admin')->check()) return redirect()->route('admin')->withSuccess('You have successfully registered a new member!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.'])->withInput();
        }
    }
}
