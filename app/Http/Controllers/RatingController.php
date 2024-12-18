<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function rateEvent($ticket_id, Request $request)
    {
        // Validate the request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        try {
            // Find the ticket
            $ticket = Ticket::findOrFail($ticket_id);
            
            // Check if  ticket belongs to this member
            if ($ticket->member_id !== Auth::id()) {
                return redirect()->back()->with('error', 'You are not authorized to rate this event.');
            }

            // Check if event  happened
            if ($ticket->event->event_date >= now()) {
                return redirect()->back()->with('error', 'You can only rate past events.');
            }

            // Check if the event is already rated by this user
            $existingRating = Rating::where('event_id', $ticket->event_id)
                ->where('member_id', Auth::id())
                ->first();

            if ($existingRating) {
                return redirect()->back()->with('error', 'You have already rated this event.');
            }

            // Create new rating
            Rating::create([
                'event_id' => $ticket->event_id,
                'member_id' => Auth::id(),
                'rating' => $request->input('rating')
            ]);


            return redirect()->back()->with('success', 'Event rated successfully!');
        } catch (\Exception $e) {
            // Log the error and return a generic error message
            dd('Error rating event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while rating the event.');
        }
    }
}