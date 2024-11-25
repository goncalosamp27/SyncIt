<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Event;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'event_id' => 'required|exists:event,event_id',
        ]);

        $member = Member::where('username', $request->input('username'))->first();
		
		if (!$member) { return redirect()->back()->with('error', "Please insert a valid username.");}

		if (in_array($member->member_status, ['Suspended', 'Banned'])) {
			return redirect()->back()->with('error', "This member cannot be invited because their account is {$member->member_status}.");
		}

		$event = Event::findOrFail($request->input('event_id'));

		$existingInvitation = Invitation::where('event_id', $request->input('event_id'))
        ->where('member_id', $member->member_id)
        ->first();

		if ($existingInvitation) {
			return redirect()->back()->with('error', "This member has already been invited to this event.");
		}

        $invitation = new Invitation();
        $invitation->invitation_message = $request->input('message') ?? "Come to my event!";
        $invitation->invitation_date = now(); // Example: set today's date
        $invitation->event_id = $request->input('event_id');
        $invitation->member_id = $member->member_id;
        $invitation->save();

        return redirect()->back()->with('success', 'Invitation sent successfully!');
    }
}
