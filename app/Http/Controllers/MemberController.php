<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Member;

class MemberController extends Controller
{
    public function edit($member_id)
{
    // Fetch the user by ID
    $member = Member::findOrFail($member_id);

    // Ensure the logged-in user is editing their own profile
    if (auth()->id() !== (int) $member_id) {
        abort(403, 'Unauthorized action.');
    }

    // Return the edit profile view with the user's data
    return view('pages.profile-edit', compact('member'));
}

}