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
}