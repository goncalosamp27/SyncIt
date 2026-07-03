<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Admin;
use App\Models\Member;
use App\Models\Restriction;

class AdminController extends Controller
{

    public function getMembersByStatus($type = 'active')
    {
        if ($type == 'active') {
            // Members who do not have active restrictions (not banned or suspended)
            $members = Member::whereDoesntHave('restrictions', function ($query) {
                $query->where(function ($q) {
                    $q->where('type', 'Suspension')
                      ->orWhere('type', 'Ban');
                })
                ->where(function ($q) {
                    $q->whereRaw('NOW() <= (start + interval \'1 day\' * duration)')
                      ->orWhere('type', 'Ban'); // Bans have no end date
                });
            })->paginate(5);
        } elseif ($type == 'banned') {
            // Members with active bans
            $members = Member::whereHas('restrictions', function ($query) {
                $query->where('type', 'Ban');
            })->paginate(5);
        } elseif ($type == 'suspended') {
            // Members with active suspensions
            $members = Member::whereHas('restrictions', function ($query) {
                $query->where('type', 'Suspension')
                      ->whereRaw('NOW() <= (start + interval \'1 day\' * duration)');
            })->paginate(5);
        }
    
        return view('pages.admin', compact('members'));
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
            $path = $request->file('profile_pic_url')->store('profile', 'public');
            $member->profile_pic_url = $path;
            $member->save();
        }
        
        return redirect()->route('admin', ['status' => 'active'])->with('success', 'Member updated successfully!');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        if (empty($searchTerm)) {
            $members = Member::paginate(10);
        } 

        else{
            $members =  Member::select('member.*')
                ->whereRaw("fts_username @@ plainto_tsquery('english', ?)", [$searchTerm])
                ->orWhereRaw("fts_display_name @@ plainto_tsquery('english', ?)", [$searchTerm])
                ->paginate(10);
        }

    
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
            
            $data['duration'] = $data['duration'] ?? 0;

            Restriction::create([
                'member_id' => $data['member_id'],
                'admin_id' => $data['admin_id'],
                'start' => $data['start'],
                'duration' => $data['duration'],
                'type' => $data['type'],
            ]);

            return redirect()->route('admin', ['status' => 'active'])->with('success', 'Restriction successfully applied.');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('admin', ['status' => 'active'])->with('error', 'An error occurred while applying the restriction.');
        }        
    }

    public function removeRestriction(Request $request, $memberId)
    {
        // Find the member by ID
        $member = Member::find($memberId);
    
        if ($member) {
            // Remove all restrictions for the member
            $member->restrictions()->delete();
    
            return redirect()->back()->with('status', 'Restriction removed for the member.');
        }
    
        return redirect()->back()->with('error', 'Member not found.');
    }
    
}