<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\Member;

class TicketController extends Controller {
	public function ticketAndEventData()
    {	
		$member = Auth::user()->load('tickets');
        $tickets = Ticket::where('member_id', Auth::id());

        return view('pages.tickets', [
            'tickets' => $tickets, 
            'member' => $member,
        ]);
    }

    public function refundTicket(string $ticket_id)
    {   
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();
            return redirect()->route('tickets')->withSuccess('success', "Ticket #'{$ticket_id}' refunded successfully!");
        }
        catch (\Exception $e) {
            return redirect()->route('tickets')->withErrors('error', "Failed to refund the ticket.");
        }   
    }

    public function buyTicket(Request $request)
    {
        $member = Auth::user();
        $event = Event::findOrFail($request->event_id);
        if ($event->event_date <= now()) {
            return response()->json(['message' => 'This event has already expired.'], 400);
        }
        $memberTicketCount = Ticket::where('event_id', $event->event_id)
            ->where('member_id', $member->member_id)
            ->count();
        if ($memberTicketCount >= 10) {
            return response()->json(['message' => 'You cannot purchase more than 10 tickets for this event.'], 400);
        }
        $ticket = new Ticket();
        $ticket->event_id = $event->event_id;
        $ticket->ticket_date = now();
        $ticket->member_id = $member->member_id;
        $ticket->save();
        return redirect()->route('tickets')->with('success', "Ticket to '{$event->event_name}' purchased successfully!");
    }
}