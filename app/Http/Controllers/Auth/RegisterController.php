<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\Member;

class RegisterController extends Controller
{
    /**
     * Display a login form.
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
            'email' => 'required|email|unique:member,email',
            'password' => 'required|min:8|max:100',
            'bio' => 'nullable|regex:/^[A-Za-z0-9_.,?!\s]*$/|max:200',
            'profile_pic_url' => 'nullable|url|max:200',
        ]);

        // Check if the email is already taken
        $existingMember = Member::where('email', $validatedData['email'])->first();
        if ($existingMember) {
            return back()->withErrors(['email' => 'The email address is already taken.'])->withInput();
        }

        // Prepare the new member data
        $newMemberData = [
            'username' => $validatedData['username'],
            'display_name' => $validatedData['display_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'bio' => $validatedData['bio'],
            'profile_pic_url' => $validatedData['profile_pic_url'] ?? null,
            'member_status' => 'Active',
        ];

        try {
            $result = Member::createMember($newMemberData);

            if ($result instanceof \Illuminate\Support\MessageBag) {
                return back()->withErrors($result)->withInput();
            }

            Auth::attempt($request->only('email', 'password'));
            $request->session()->regenerate();

            return redirect()->route('home')->withSuccess('You have successfully registered & logged in!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.'])->withInput();
        }
    }
}
