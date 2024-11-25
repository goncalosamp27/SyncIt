<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Admin;
use App\Models\Member;

class AdminController extends Controller
{
    public function display_members()
    {
        $members = Member::all();

        return view('pages.admin', ['members' => $members]);
    }

    public function getMember(string $member_id) 
	{
        $member = Member::findOrFail($member_id);
        
        return view('pages.admin-edit-member', [
            'member' => $member
        ]);
    }
    
    public function updateMemberAdmin(Request $request, $id)
    {   
        $member = Member::findOrFail($id);

        // Validate inputs
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'bio' => 'nullable|string',
            'profile_pic_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $member->update($validated);
        
        
        if ($request->hasFile('profile_pic_url')) {
            $path = $request->file('profile_pic_url')->store('profiles', 'public');
            $member->profile_pic_url = $path;
            $member->save();
        }
        
        return redirect()->route('admin')->with('success', 'Member updated successfully!');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $members =  Member::select('member.*')
            ->whereRaw("fts_username @@ plainto_tsquery('english', ?)", [$searchTerm])
            ->orWhereRaw("fts_display_name @@ plainto_tsquery('english', ?)", [$searchTerm])
            ->get();
    
        return view('pages.admin', [
            'members' => $members,
        ]);
    }
}