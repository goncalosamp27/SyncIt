<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Admin;
use App\Models\Member;
use App\Models\Restriction;

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

    public function createMember(){
        return view('auth.register');
    }

    public function applyRestriction(Request $request)
    {

        $data = $request->validate([
            'member_id' => 'required|exists:member,member_id',
            'admin_id' => 'required|exists:admin,admin_id',
            'start' => 'required|date',
            'type' => 'required|in:Ban,Suspension',
            'duration' => 'required_if:type,Suspension|nullable|integer|min:0',
        ]);

        try {
            $member = Member::findOrFail($data['member_id']);

            // Handle restriction type logic
            if ($data['type'] === 'Ban') {
                // Anonymize member data
                $member->update([
                    'username' => 'anonymous_' . $member->member_id,
                    'display_name' => 'Anonymous',
                    'bio' => null,
                    'profile_pic_url' => 'default_user.png',
                    'member_status' => 'Banned',
                ]);
                $data['duration'] = 0; // Ban is permanent
            } else {
                // Mark member as suspended
                $member->update(['member_status' => 'Suspended']);
            }
            
            Restriction::create([
                'member_id' => $data['member_id'],
                'admin_id' => $data['admin_id'],
                'start' => $data['start'],
                'duration' => $data['duration'],
                'type' => $data['type'],
            ]);

            return redirect()->route('home')->with('success', 'Restriction successfully applied.');
        } catch (\Exception $e) {
            \Log::error("Failed to apply restriction: {$e->getMessage()}");
            return redirect()->route('home')->with('error', 'An error occurred while applying the restriction.');
        }        
    }
}