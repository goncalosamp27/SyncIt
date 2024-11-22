<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;
use App\Models\Tag;

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

    public function display_events()
    {
        // Retrieve all events
        $events = Event::all(); 

        // Return the view and pass the events data to the view
        return view('pages.events', ['events' => $events]);
    }
}
