<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\JoinRequest;
use App\Models\EventTag;

use Illuminate\Support\Facades\DB;
use DateTime;


class EventController extends Controller
{
    public function show(string $event_id): View
    {   
        if (!is_numeric($event_id)) {
            abort(404, 'Invalid event identifier');
        }
        // Get the event card.
        $event = Event::findOrFail($event_id);
        $comments = $event->comments ?: collect();
        return view('pages.event', [
            'event' => $event,
            'comments' => $comments 
        ]);
    }

    public function refundTicket(string $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();
            return redirect()->route('tickets')->with('success', "Ticket #'{$ticket_id}' refunded successfully!");
        } catch (\Exception $e) {
            return redirect()->route('tickets')->with('error', "Failed to refund the ticket.");
        }
    }


    public function deleteEvent(string $event_id)
    {
        try {
            // Fetch the event
            $event = Event::findOrFail($event_id);

            // Debug: Check if event is valid
            if (!$event) {
                return redirect()->route('your-events')->with('error', "Event not found.");
            }

            // Validate event date
            if (!$event->event_date || strtotime($event->event_date) < time()) {
                return redirect()->route('your-events')->with('error', "Cannot delete past events.");
            }

            // Attempt to delete the event
            $event->delete();

            return redirect()->route('your-events')->with('success', "Event #{$event_id} deleted successfully!");
        } catch (\Exception $e) {
            // Log the actual error
            dd($e->getMessage());
            \Log::error("Failed to delete event: {$e->getMessage()}");

            return redirect()->route('your-events')->with('error', "Failed to delete the event.");
        }
    }

    public function member_events()
    {
        $member = Auth::user();
        $events = Event::where('artist_id', $member->member_id)->get();
        return view('pages.your-events', [
            'events' => $events,
            'member' => $member,
        ]);
    }

    public function editEvent(string $event_id): View
    {
        $event = Event::findOrFail($event_id);

        $this->authorize('edit', $event);

        return view('pages.edit-event', [
            'event' => $event
        ]);
    }

    public function tickets($event_id)
    {
        $event = Event::findOrFail($event_id);
        $tickets = $event->tickets();
        $requests = $event->requests();

        $requests = $event->requests()->with('member')->get();
        $tickets = $event->tickets()->with('member')->get();
        return view('pages.manage-participants', [
            'event' => $event,
            'requests' => $requests,
            'tickets' => $tickets
        ]);
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

        $data = $request->only([
            'event_name',
            'event_date',
            'location',
            'description',
            'refund',
            'price',
            'type_of_event',
            'artist_id',
        ]);
        $data['event_status'] = 'Active'; 

        $event = Event::create($data);
        
        // Attach tags (if any are selected)
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search'); // Search term from the user input

        $tagsMusic = Tag::type(['Music'])->get();
        $tagsDance = Tag::type(['Dance'])->get();
        $tagsMood = Tag::type(['Mood'])->get();
        $tagsSettings = Tag::type(['Settings'])->get();

        // Handle the search query using PostgreSQL full-text search
        $events = Event::select('event.*') // Select from the event table (not events)
            ->whereRaw("to_tsvector('english', COALESCE(event_name, '')) @@ to_tsquery('english', ?)", [$searchTerm])
            ->orWhereRaw("to_tsvector('english', COALESCE(location, '')) @@ to_tsquery('english', ?)", [$searchTerm])
            ->get();

        return view('pages.events', [
            'events' => $events,
            'tagsMusic' => $tagsMusic,
            'tagsDance' => $tagsDance,
            'tagsMood' => $tagsMood,
            'tagsSettings' => $tagsSettings,
        ]);
    }

    public function deleteParticipant($event_id, $ticket_id)
    {
        try 
        {
            $ticket = Ticket::findOrFail($ticket_id);
            $member = $ticket->member; 
            $ticket->delete();
            return redirect()->route('event', ['event_id' => $event_id ])->with('success', "'{$member->username}'s Ticket #'{$ticket_id}' deleted successfully!");
        }

        catch (\Exception $e) 
        {
            return redirect()->route('event', ['event_id' => $event_id ])->with('error', "Failed to delete '{$member->username}'s ticket.");
        }   
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
    public function showTagsPerTypeFuture(Request $request)
    {
        // Fetch tags of different types
        $tagsMusic = Tag::type(['Music'])->get();
		$tagsDance = Tag::type(['Dance'])->get();
		$tagsMood = Tag::type(['Mood'])->get();
		$tagsSettings = Tag::type(['Settings'])->get();
        $events = Event::where('event_date', '>', now())->get();

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

    //function to filter events 
    public function filterEvents(Request $request)
    {
        $tagsMusic = Tag::type(['Music'])->get();
        $tagsDance = Tag::type(['Dance'])->get();
        $tagsMood = Tag::type(['Mood'])->get();
        $tagsSettings = Tag::type(['Settings'])->get();

        $tagIds = $request->input('tags', []);

        $events = Event::getEventsByTags($tagIds);
        
        return response()->json(
            [
                'events' => $events,
                'tagsMusic' => $tagsMusic,
                'tagsDance' => $tagsDance,
                'tagsMood' => $tagsMood,
                'tagsSettings' => $tagsSettings
            ]
        );
    }


    public function updateFutureEventsPage(Request $request)
    {
        // If the request contains specific event IDs to filter
        if ($request->has('event_ids') && !empty($request->input('event_ids'))) {
            $eventIds = $request->input('event_ids');

            // Get filtered events by IDs
            $events = Event::whereIn('event_id', $eventIds)->get();
        }

        return response()->json([
            'success' => true,
            'events' => $events,
        ]);
    }

    public function cancelEvent(Request $request, string $event_id)
    {
        try {
            // Find the event
            $event = Event::findOrFail($event_id);

            // Check if the event is currently active
            if ($event->event_status !== 'Active') {
                return redirect()->back()->with('error', "Only active events can be cancelled.");
            }

            // Update the event's status to 'Cancelled'
            $event->update(['event_status' => 'Cancelled']);

            // Return a success message
            return redirect()->route('your-events')->with('success', "Event #{$event_id} has been successfully cancelled.");
        } catch (\Exception $e) {
            // Log the error and return an error message
            \Log::error("Failed to cancel event: {$e->getMessage()}");

            return redirect()->back()->with('error', "Failed to cancel the event.");
        }
    }

    /*
    public function getEventCards(Request $request)
    {
        // Accept an array of event objects directly
        $events = $request->input('events'); // This will now be an array of event objects

        if (empty($events)) {
            return response()->json(['success' => false, 'message' => 'No events found.']);
        }

        // Return the rendered event cards as HTML
        $html = view('partials.event-cards', compact('events'))->render(); // Renders the Blade partial
        dd($html);

        return response()->json(['success' => true, 'html' => $html]);
    }
        */
}
