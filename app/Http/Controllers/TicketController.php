<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\Member;

class TicketController extends Controller {
	public function ticketAndEventData()
    {	
		$member = auth()->user()->load('tickets');
        $tickets = Ticket::where('member_id', auth()->id())->get();

        $events = $tickets->map(function ($ticket) {
            return $ticket->event;
        })->filter();

        return view('pages.tickets', [
            'tickets' => $tickets, 
            'events' => $events,
			'member' => $member,
        ]);
    }
    
    public function buyTicket(Request $request, Event $event)
    {
        // Validate the event and user conditions
        $user = auth()->user(); // Get the authenticated user
        $userTicketCount = Ticket::where('event_id', $event->id)
            ->where('member_id', $user->id)
            ->count();

        // Check if the user has reached the ticket limit
        if ($userTicketCount >= 10) {
            return response()->json([
                'message' => 'You cannot purchase more than 10 tickets for this event.'
            ], 400); // HTTP 400 Bad Request
        }

        // Ensure the event is not expired
        if ($event->event_date <= now()) {
            return response()->json([
                'message' => 'This event has already expired.'
            ], 400);
        }

        // Insert the ticket into the database
        $ticket = new Ticket();
        $ticket->event_id = $event->id;
        $ticket->ticket_date = now();
        $ticket->member_id = $user->id;
        $ticket->save();

        return response()->json([
            'message' => 'Ticket purchased successfully!',
            'ticket_id' => $ticket->ticket_id
        ], 201); // HTTP 201 Created
    }
}