<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\JoinRequest;
use App\Models\Event;
use App\Models\Member;
use App\Models\Invitation;

class JoinRequestController extends Controller
{
	public function requestAccess(Request $request)
    {	
		$member = Auth::user();
		$event = Event::findOrFail($request->input('event_id'));

		$existingInvitation = Invitation::where('event_id', $request->input('event_id'))
        ->where('member_id', $member->member_id)
        ->first();

		if ($existingInvitation) {
			return redirect()->back()->with('error', "You have already been invited to this event.");
		}

		$joinRequest = new JoinRequest(); 
		$joinRequest->event_id = $event->event_id;
		$joinRequest->member_id = $member->member_id;
		$joinRequest->request_date = now();
		$joinRequest->save();

        return redirect()->route('event', ['event_id' => $event->event_id ])->with('success', 'Join request to this event was sent successfully!');
    }
}