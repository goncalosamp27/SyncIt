<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;

class EventController extends Controller
{   
	/* Show the event for a given event id */
    public function show(string $event_id): View 
	{
        // Get the event card.
        $event = Event::findOrFail($event_id);

        return view('pages.event', [
            'event' => $event
        ]);
    }

	public function list_past_events()
    {
        $pastEvents = Event::where('event_date', '<', now())
		->orderBy('event_date', 'desc')
		->where('type_of_event', 'Public')
		->get();

        return view('pages.past_events', [
			'pastEvents' => $pastEvents
		]);
    }

	public function list_future_events()
    {
        $futureEvents = Event::where('event_date', '>=', now())
		->orderBy('event_date', 'desc')
		->where('type_of_event', 'Public')
		->get();

        return view('pages.future_events', [
			'futureEvents' => $futureEvents
		]);
    }
	
	public function list_user_past_events($userId)
	{
		$userPastEvents = Event::where('user_id', $userId)
        ->where('event_date', '<', now())
		->where('type_of_event', 'Public')
        ->orderBy('event_date', 'desc')
        ->get();

		return view('pages.user_events', [
			'userPastEvents' => $userPastEvents
		]);
	}

	public function list_user_future_events($userId)
	{
		$userPastEvents = Event::where('user_id', $userId)
        ->where('event_date', '>=', now())
		->where('type_of_event', 'Public')
        ->orderBy('event_date', 'desc')
        ->get();

		return view('pages.user_events', [
			'userFutureEvents' => $userFutureEvents
		]);
	}

	// Might be useful, might not : //
	public function list_all_events() 
	{
		$pastEvents = Event::where('event_date', '<', now())
		->where('type_of_event', 'Public')
		->orderBy('event_date', 'desc')
		->get();

		$futureEvents = Event::where('event_date', '>=', now())
		->where('type_of_event', 'Public')
		->orderBy('event_date', 'desc')
		->get();

		return view('pages.all_events', [
			'pastEvents' => $pastEvents,
			'futureEvents' => $futureEvents
		]);
	}
}
