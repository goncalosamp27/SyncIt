<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;
use App\Models\Tag;
use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class EditEventController extends Controller
{   
    public function show(string $event_id): View 
	{
        // Get the event card.
        $event = Event::findOrFail($event_id);
        
        return view('pages.edit-event', [
            'event' => $event
        ]);
    }

    public function editEvent(Request $request, string $event_id) 
	{
        $event = Event::findOrFail($event_id);
        // Validate inputs
        $validated = $request->validate([
            'event_name' => 'required|string|max:100',
            'event_date' => 'required|date|after_or_equal:tomorrow',
            'event_time' => 'required|date_format:H:i',  
            'location' => 'required|string|max:100',
            'description' => 'required|string',
            'refund' => 'required|numeric|between:0,100',  
            'price' => 'required|numeric|min:0',  
            'type_of_event' => 'required|in:Public,Private',  
            'capacity' => 'required|numeric|min:10',
            'event_media' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $defaultImage = 'default_event.png';

        $event->update($validated);
        $eventDate = $request->input('event_date');
        $eventTime = $request->input('event_time');
        $eventDateTime = $eventDate . ' ' . $eventTime;

        if ($request->hasFile('event_media')) {
            $path = $request->file('event_media')->store('event', 'public');
            $event->event_media= $path;
            $filename = basename($path);
            $event->event_media = $filename;
        }
        else {
            $event->event_media= $defaultImage;
        }
        $event->event_date = $eventDateTime;
            $event->save();
        return redirect()->route('event', ['event_id' => $event_id])->with('success', 'Event updated successfully!');
    }

    public function tickets($event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('seeParticipants', $event);

        // Fetch join requests
        $requests = $event->requests()->with('member')->get();

        // Group tickets by member and count tickets per member
        $ticketsGrouped = $event->tickets()
            ->with('member')
            ->get()
            ->groupBy('member_id')
            ->map(function ($tickets) {
                $member = $tickets->first()->member;
                return [
                    'member' => $member,
                    'ticket_count' => $tickets->count(),
                ];
            });

        $perPageRequests = 5; // Number of requests per page
        $pageRequests = request()->input('page_requests', 1); // Current page for requests
        $paginatedRequests = new LengthAwarePaginator(
            $requests->forPage($pageRequests, $perPageRequests),
            $requests->count(),
            $perPageRequests,
            $pageRequests,
            ['path' => request()->url(), 'query' => request()->query()] // Keep query parameters
        );

        // Paginate the grouped tickets
        $perPage = 5;
        $page = request()->input('page', 1); // Get the current page
        $paginatedTickets = new LengthAwarePaginator(
            $ticketsGrouped->forPage($page, $perPage), // Slice the collection for the current page
            $ticketsGrouped->count(), // Total number of items
            $perPage, // Items per page
            $page, // Current page
            ['path' => request()->url(), 'query' => request()->query()] // Keep existing query parameters
        );

        return view('pages.participants', [
            'event' => $event,
            'ticketsGrouped' => $paginatedTickets, // Pass the paginated tickets
            'requests' => $paginatedRequests,
        ]);
    }

    public function deleteParticipant($event_id, $member_id)
    {
        try {
            $event = Event::findOrFail($event_id);

            // Find all tickets for this member within the event and delete them
            $tickets = $event->tickets()->where('member_id', $member_id)->get();
            $member = $tickets->first()->member; // Get member details for confirmation

            foreach ($tickets as $ticket) {
                $ticket->delete();
            }

            return redirect()->route('participants', ['event_id' => $event_id])
                ->with('success', "@{$member->username}'s tickets have been deleted successfully!");
        } catch (\Exception $e) {
            dd("Error occurred", $e->getMessage()); // Debug: Output error message
            return redirect()->route('participants', ['event_id' => $event_id])
                ->with('error', "Failed to delete @{$member->username}'s tickets.");
        }
    }

	public function create()
    {
        // Fetch all tags to populate the dropdown
        $tagsMusic = Tag::type(['Music'])->get();
		$tagsDance = Tag::type(['Dance'])->get();
		$tagsMood = Tag::type(['Mood'])->get();
		$tagsSettings = Tag::type(['Settings'])->get();

        return view('pages.create', [
            'musicTags' => $tagsMusic,
            'danceTags' => $tagsDance,
			'moodTags' => $tagsMood,
			'settingsTags' => $tagsSettings,
        ]);
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
            'event_name', 'event_date', 'location', 'description', 'refund', 'price', 'type_of_event', 'artist_id',
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
        $events = Event::where('event_date', '<', now())->get();

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
        $events = Event::where('event_date', '>',  now())->get();

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
