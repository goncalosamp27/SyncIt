<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Event;

class InvitationController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'event_id' => 'required|exists:event,event_id',
            'invitor_id' => 'required|exists:member,member_id',
        ]);

        $member = Member::where('username', $request->input('username'))->first();
		
		if (!$member) { return redirect()->back()->with('error', "Please insert a valid username.");}

		if (in_array($member->member_status, ['Suspended', 'Banned'])) {
			return redirect()->back()->with('error', "This member cannot be invited because their account is {$member->member_status}.");
		}

		$event = Event::findOrFail($request->input('event_id'));
        $loggedMemberId = Auth::user()->member_id;

        if ($member->member_id === $loggedMemberId) {
            return redirect()->back()->with('error', "You cannot invite yourself to the event.");
        }
        if ($member->member_id === $event->artist->member->member_id) {
            return redirect()->back()->with('error', "You cannot invite the owner of the event.");
        }
		$existingInvitation = Invitation::where('event_id', $request->input('event_id'))
        ->where('member_id', $member->member_id)
        ->where('invitor_id', Auth::user()->member_id) // Ensure invitor_id matches the logged-in user
        ->first();

		if ($existingInvitation) {
			return redirect()->back()->with('error', "You have already invited this member to this event.");
		}

        $invitation = new Invitation();
        $invitation->invitation_message = $request->input('message') ?? ($invitation->invitor_id === $event->artist->member->member_id 
            ? "Come to my event!" 
            : "Join me in this event!");
        $invitation->invitation_date = now(); // Example: set today's date
        $invitation->invitor_id = $request->input('invitor_id');
        $invitation->event_id = $request->input('event_id');
        $invitation->member_id = $member->member_id;
        $invitation->save();

        return redirect()->back()->with('success', 'Invitation sent successfully!');
    }

    public function create2(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:member,member_id',
            'event_id' => 'required|exists:event,event_id',
            'invitor_id' => 'required|exists:member,member_id',
        ]);

        $member = Member::findOrFail($request->input('member_id'));
		
		if (!$member) { return redirect()->back()->with('error', "Not a valid member.");}

		if (in_array($member->member_status, ['Suspended', 'Banned'])) {
			return redirect()->back()->with('error', "This member's account is {$member->member_status}.");
		}

		$event = Event::findOrFail($request->input('event_id'));

        $existingInvitation = Invitation::where('event_id', $request->input('event_id'))
        ->where('member_id', $member->member_id)
        ->where('invitor_id', Auth::user()->member_id) // Ensure invitor_id matches the logged-in user
        ->first();

		if ($existingInvitation) {
			return redirect()->back()->with('error', "You have already invited this member to this event.");
		}

        $invitation = new Invitation();
        $invitation->invitation_message = "I accepted your Request! Join my Event!";
        $invitation->invitation_date = now(); 
        $invitation->event_id = $request->input('event_id');
        $invitation->invitor_id = $request->input('invitor_id');
        $invitation->member_id = $request->input('member_id');
        $invitation->save();

        return redirect()->back()->with('success', 'Join request accepted and invitation sent successfully!');
    }

    public function memberinvitations()
    {	
        $now = now();
		$member = Auth::user()->load('invitations');
        $validinvitations = Invitation::where('member_id', Auth::id())
            ->whereHas('event', function ($query) use ($now) {$query->where('event_date', '>', $now);})
            ->paginate(3); 

        return view('pages.invitations', [
            'validinvitations' => $validinvitations, 
            'member' => $member,
        ]);
    }

    public function deleteInvitation(string $invitation_id)
    {
        try {
            // Fetch the event
            $invitation = Invitation::findOrFail($invitation_id);

            // Debug: Check if event is valid
            if (!$invitation) {
                return redirect()->route('invitations')->with('error', "Invitation not found.");
            }

            // Attempt to delete the event
            $invitation->delete();

            return redirect()->route('invitations')->with('success', "Invitation #{$invitation_id} deleted successfully!");
        } 
        catch (\Exception $e) 
        {
            // Log the actual error
            dd($e->getMessage());
            \Log::error("Failed to delete invitation: {$e->getMessage()}");

            return redirect()->route('invitations')->with('error', "Failed to delete the invitation.");
        }
    }
}
