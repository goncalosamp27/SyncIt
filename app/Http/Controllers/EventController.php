<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;
use App\Models\Tag;
use Illuminate\Support\Carbon;

class EventController extends Controller
{   
    public function show(string $event_id): View 
	{
        // Get the event card.
        $event = Event::findOrFail($event_id);
        
        return view('pages.event', [
            'event' => $event
        ]);
    }

    public function editEvent(string $event_id): View 
	{
        $event = Event::findOrFail($event_id);
        
        return view('pages.edit-event', [
            'event' => $event
        ]);
    }

    public function participants($event_id)
    {
        // Retrieve the event by its ID
        $event = Event::findOrFail($event_id);
    
        // Retrieve the participants (members) of the event
        $participants = $event->tickets->map(function ($ticket) {
            return $ticket->member;  // Retrieve the associated member for each ticket
        });
    
        // Return a view with the participants
        return view('pages.manage-participants', [
            'participants' => $participants
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

    public function showTagsPerType()
    {
        // Fetch tags where tag_name is 'Music' or 'Dance' (Genres)
        $tagsMusic = Tag::type(['Music'])->get();
		$tagsDance = Tag::type(['Dance'])->get();
		$tagsMood = Tag::type(['Mood'])->get();
		$tagsSettings = Tag::type(['Settings'])->get();
        $events = Event::all();
        return view('pages.events', [
            'events' => $events,
            'tagsMusic' => $tagsMusic,
            'tagsDance' => $tagsDance,
			'tagsMood' => $tagsMood,
			'tagsSettings' => $tagsSettings,
        ]);
    }
    public function showTagsPerTypePast()
    {
        // Fetch tags where tag_name is 'Music' or 'Dance' (Genres)
        $tagsMusic = Tag::type(['Music'])->get();
		$tagsDance = Tag::type(['Dance'])->get();
		$tagsMood = Tag::type(['Mood'])->get();
		$tagsSettings = Tag::type(['Settings'])->get();
        $events = Event::where('event_date', '<', Carbon::now())->get();

        return view('pages.events', [
            'events' => $events,
            'tagsMusic' => $tagsMusic,
            'tagsDance' => $tagsDance,
			'tagsMood' => $tagsMood,
			'tagsSettings' => $tagsSettings,
        ]);
    }
    public function showTagsPerTypeFuture()
    {
        // Fetch tags where tag_name is 'Music' or 'Dance' (Genres)
        $tagsMusic = Tag::type(['Music'])->get();
		$tagsDance = Tag::type(['Dance'])->get();
		$tagsMood = Tag::type(['Mood'])->get();
		$tagsSettings = Tag::type(['Settings'])->get();
        $events = Event::where('event_date', '>', Carbon::now())->get();

        return view('pages.events', [
            'events' => $events,
            'tagsMusic' => $tagsMusic,
            'tagsDance' => $tagsDance,
			'tagsMood' => $tagsMood,
			'tagsSettings' => $tagsSettings,
        ]);
    }

    public function selectTickets(Event $event)
    {
        // Example logic to display ticket selection page
        $userTicketCount = $event->tickets()
            ->where('member_id', auth()->id())
            ->count();

        return view('events.select-tickets', [
            'event' => $event,
            'userTicketCount' => $userTicketCount,
        ]);
    }
}
