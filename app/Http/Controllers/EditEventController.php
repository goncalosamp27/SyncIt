<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;
use App\Models\Tag;
use App\Models\Ticket;
use Illuminate\Support\Carbon;

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
            'rating' => 'required|numeric|between:0,5',
            'capacity' => 'required|numeric|min:10',
            'event_media' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $defaultImage = 'default_event.png';

        $event->update($validated);
        $eventDate = $request->input('event_date');
        $eventTime = $request->input('event_time');
        $eventDateTime = Carbon::createFromFormat('Y-m-d H:i', "$eventDate $eventTime");

        if ($request->hasFile('event_media')) {
            $path = $request->file('event_media')->store('events', 'public');
            $event->event_media= $path;
            $filename = basename($path);
            $event->event_media = $filename;
        }
        else {
            $event->event_media= $defaultImage;
        }
        $event->event_date = $eventDateTime;
            $event->save();
        return redirect()->route('event', ['event_id' => $event_id])->with('success', 'Member updated successfully!');
    }

    public function tickets($event_id)
    {
        $event = Event::findOrFail($event_id);
        $tickets = $event->tickets();

        $tickets = $event->tickets()->with('member')->get();
        return view('pages.manage-participants', [
            'event' => $event,
            'tickets' => $tickets
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
