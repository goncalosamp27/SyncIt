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
        
        // Get valid tickets (upcoming events) and paginate them
        $tickets = Ticket::where('member_id', Auth::id())
            ->whereHas('event', function ($query) {
                $query->where('event_date', '>', now()); // Only upcoming events
            })
            ->with('event') // Eager load the event data
            ->paginate(3); // Limit to 3 tickets per page
        
        return view('pages.tickets', [
            'tickets' => $tickets, 
            'member' => $member,
        ]);
    }

    public function ticketAndEventData2()
    {	
        $member = Auth::user();
        
        $tickets = Ticket::where('member_id', $member->member_id)
            ->whereHas('event', function ($query) {
                $query->where('event_date', '<=', now()); // Only past events
            })
            ->with('event') // Eager load the event data
            ->paginate(3); // Limit to 3 tickets per page

        return view('pages.attended', [
            'tickets' => $tickets, 
            'member' => $member,
        ]);
    }


    public function refundTicket(string $ticket_id)
    {   
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();
            return redirect()->route('tickets')->with('success', "Ticket #'{$ticket_id}' refunded successfully!");
        }
        catch (\Exception $e) {
            return redirect()->route('tickets')->with('error', "Failed to refund the ticket.");
        }   
    }

    public function buyTicket(Request $request)
    {
        $member = Auth::user();
        $event = Event::findOrFail($request->event_id);

        // Check if the event has already expired
        if ($event->event_date <= now()) {
            return redirect()->back()
                ->with('error', 'This event has already expired.');
        }

        // Validate the number of tickets being purchased
        $ticketCount = $request->input('ticket_count', 1); // Default to 1 if no ticket count is specified

        if ($ticketCount < 1 || $ticketCount > 10) {
            return redirect()->back()
                ->with('error', 'You can only buy between 1 and 10 tickets at a time.');
        }

        // Check how many tickets the user already has for this event
        $memberTicketCount = Ticket::where('event_id', $event->event_id)
            ->where('member_id', $member->member_id)
            ->count();

        // Validate if the user has already purchased 10 tickets
        if ($memberTicketCount + $ticketCount > 10) {
            return redirect()->back()
                ->with('error', 'You cannot purchase more than 10 tickets for this event.');
        }

        // Create the tickets for the specified quantity
        for ($i = 0; $i < $ticketCount; $i++) {
            $ticket = new Ticket();
            $ticket->event_id = $event->event_id;
            $ticket->ticket_date = now();
            $ticket->member_id = $member->member_id;
            $ticket->save();
        }
        
        // Redirect back to the event page with a success message
        return redirect()->route('tickets')
            ->with('success', "{$ticketCount} ticket(s) to '{$event->event_name}' purchased successfully!");
    }
}