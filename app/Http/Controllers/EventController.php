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

	public function create()
    {
        // Fetch all tags to populate the dropdown
        $tags = Tag::all();
        return view('events.create', compact('tags'));
    }

    public function store(Request $request)
    {
        // Use validation from the Event model
        $validator = Event::validate($request->all());
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the event
        $event = Event::create($request->only([
            'event_name', 'event_date', 'location', 'description', 'refund', 'price', 'type_of_event', 'rating', 'artist_id',
        ]));

        // Attach tags (if any are selected)
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

	public function list_past_events()
    {
        $pastEvents = Event::where('event_date', '<', now())
		->orderBy('event_date', 'desc')
		->get();

        return view('pages.past_events', [
			'pastEvents' => $pastEvents
		]);
    }

	public function list_future_events()
    {
        $futureEvents = Event::where('event_date', '>=', now())
		->orderBy('event_date', 'desc')
		->get();

        return view('pages.future_events', [
			'futureEvents' => $futureEvents
		]);
    }
	
	public function list_artist_past_events($artistId)
	{
		$userPastEvents = Event::where('user_id', $artistId)
        ->where('event_date', '<', now())
        ->orderBy('event_date', 'desc')
        ->get();

		return view('pages.user_events', [
			'userPastEvents' => $artistPastEvents
		]);
	}

	public function list_artist_future_events($artistId)
	{
		$userPastEvents = Event::where('user_id', $artistId)
        ->where('event_date', '>=', now())
        ->orderBy('event_date', 'desc')
        ->get();

		return view('pages.user_events', [
			'userFutureEvents' => $artistFutureEvents
		]);
	}

	// Might be useful, might not : //
	public function list_all_events() 
	{
		$pastEvents = Event::where('event_date', '<', now())
		->orderBy('event_date', 'desc')
		->get();

		$futureEvents = Event::where('event_date', '>=', now())
		->orderBy('event_date', 'desc')
		->get();

		return view('pages.all_events', [
			'pastEvents' => $pastEvents,
			'futureEvents' => $futureEvents
		]);
	}
}
