<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

class MemberController extends Controller
{
    public function edit()
    {
        $member = Auth::user(); 

        return view('pages.profile-edit', compact('member'));
    }

    public function updateMember(Request $request)
    {   
        $member = Auth::user();

        // Validate inputs
        $validated = $request->validate([
            'display_name' => 'required|string|max:50',
            'username' => 'required|string|max:50',
            'email' => 'required|email',
            'bio' => 'nullable|string',
            'profile_pic_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $member->update($validated);
        
        
        if ($request->hasFile('profile_pic_url')) {
            $path = $request->file('profile_pic_url')->store('profile', 'public');
            $member->profile_pic_url = basename($path);
            $member->save();
        }
        
        return redirect()->route('home')->with('success', "Your profile was updated successfully!");
    }

    public function delete(Request $request)
    {
        // Validate the input
        $request->validate([
            'password' => 'required',
            'confirmation' => 'required',
        ]);

        // Get the currently authenticated member
        $member = Auth::user();

        // Use the deleteAccount method from the Member model
        $result = $member->deleteAccount($request->password, $request->confirmation);

        // Handle the result
        if ($result['status']) {
            Auth::logout();
            return redirect('/home')->with('success', "Your account was deleted.");
        } else {
            // Return with an error message
            return back()->with('error', $result['message']);
        }
    }
}
