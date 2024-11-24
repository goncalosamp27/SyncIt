<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Member;

class MemberController extends Controller
{
    public function edit($member_id)
    {
        // Fetch the user by ID
        $member = Member::find($member_id);

        // Ensure the logged-in user is editing their own profile
        if (!$member || auth()->id() !== (int) $member_id) {
            return redirect()->route('profile.edit', ['member_id' => auth()->id()])->with('error', "You are only allowed to edit your own profile.");
        }

        // Return the edit profile view with the user's data
        return view('pages.profile-edit', compact('member'));
    }
}
