<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;
use App\Models\Tag;
use App\Models\Member;
use App\Models\Artist;
use App\Models\EventTag;
use Exception;
use Carbon\Carbon;

class CreateEventController extends Controller
{
    public function show(): View
    {
        $musicTags = Tag::getMusicTags();
        $danceTags = Tag::getDanceTags();
        $moodTags = Tag::getMoodTags();
        $settingsTags = Tag::getSettingsTags();
        return view('pages.create', [
            'musicTags' => $musicTags,
            'danceTags' => $danceTags,
            'moodTags' => $moodTags,
            'settingsTags' => $settingsTags
        ]);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type_of_event' => 'required|string|max:100',
            'refund' => 'required|numeric|between:0,100',
            'price' => 'required|numeric|min:0',
            'event_files' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'capacity' => 'required|integer|min:10',
            'music-dance' => 'nullable|numeric',
            'mood' => 'nullable|numeric',
            'setting' => 'nullable|numeric',
        ]);
    
        // Extract data from the request
        $eventData = $request->only([
            'event_name',
            'event_date',
            'location',
            'description',
            'type_of_event',
            'refund',
            'price',
            'capacity',
        ]);
    
        $eventTags = $request->only(['music-dance', 'mood', 'setting']);
    
        // Combine event_date and event_time into a DateTime object
        $eventDate = $request->input('event_date');
        $eventTime = $request->input('event_time');
        $eventDateTime = "$eventDate $eventTime";
    
        // Authenticate the member
        $member = Auth::user();
        if (!$member) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }
    
        // Check if the member is an artist
        if (!Member::isArtist($member->member_id)) {
            // If not, create an artist profile for the member
            $artistResponse = Artist::createArtist([
                'member_id' => $member->member_id,
                'rating' => 0,
            ]);
    
            if (!$artistResponse['success'] ?? true) {
                return response()->json(['error' => 'Failed to create artist'], 500);
            }
        }
    
        // Fetch the artist ID
        $artistId = Artist::getArtistIdByMemberId($member->member_id);
    
        // Create the event
        try {
            $event = new Event($eventData);
            $event->event_media = 'default_event.png';
            // Handle the file upload if provided
            if ($request->hasFile('event_files')) {
                $path = $request->file('event_files')->store('profiles', 'public');
                $event->event_media = basename($path);
            }
    
            // Assign event-specific data
            $event->event_date = $eventDateTime;
            $event->rating = 0;
            $event->artist_id = $artistId;
            $event->save();
    
            // Add tags to the event
            foreach ($eventTags as $tag) {
                if ($tag) {
                    EventTag::createEventTag($event->event_id, $tag);
                }
            }
    
            return redirect()->route('event', ['event_id' => $event->event_id])
                ->with('message', 'Event created successfully.');
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create event: ' . $e->getMessage()], 500);
        }
    }
    

}
