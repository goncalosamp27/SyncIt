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
        $validated = $request->validate([
            // Event data validation rules
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date|after_or_equal:today',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type_of_event' => 'required|string|max:100', // Specify allowed event types
            'refund' => 'required|numeric|between:0,100', 
            'price' => 'required|numeric|min:0',
            'event_files' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'capacity' => 'required|integer|min:10',
    
            // Event tags validation rules
            'music-dance' => 'nullable|numeric',
            'mood' => 'nullable|numeric',
            'setting' => 'nullable|numeric',
        ]);

        $eventData = $request->only([
            'event_name',
            'event_date',
            'location' ,
            'description',
            'type_of_event',
            'refund',
            'price' ,
            'event_media',
            'capacity',
        ]);
        $eventTags = $request->only([
            'music-dance',
            'mood',
            'setting'
        ]);
        // Extract event_date and event_time
        $eventDate = $request->input('event_date');
        $eventTime = $request->input('event_time');
        $eventDateTime = date('Y-m-d H:i:s', strtotime("$eventDate $eventTime")); // Convert Unix timestamp to MySQL-compatible datetime format
        $defaultImage = 'default_event.png';

        $member = Auth::user();
        if (!$member) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }
        if (Member::isArtist($member->member_id)) {
            try {
                // Create and save the event
                $event = new Event($eventData);
                if ($request->hasFile('event_files')) {
                    $file = $request->file('event_files');
    
                    // Generate a unique file name
                    $fileName = $file->hashName();
                    
                    // Store the file in the public/event folder
                    $path = $file->storeAs('event_images', $fileName, 'Tutorial02');
                    
                    // Save the file name in your database (or any other operation you want)
                    $event->event_media = $fileName;
                }
                else{
                    $event->event_media = $defaultImage;
                }
                $event->event_date = $eventDateTime;
                $event->rating = 0;
                $event->artist_id = Artist::getArtistIdByMemberId($member->member_id);
                $event->save();
                try {
                    // Add tags to the event
                    EventTag::createEventTag($event->event_id, $eventTags['music-dance']);
                    EventTag::createEventTag($event->event_id, $eventTags['mood']);
                    EventTag::createEventTag($event->event_id, $eventTags['setting']);

                    return redirect()->route('event', ['event_id' => $event->event_id])
                        ->with('message', 'Event created successfully.');

                } catch (Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to add tags.',
                        'error' => $e->getMessage(),
                    ], 500);
                }

            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to create event: ' . $e->getMessage()], 500);
            }
        }
        
        $artistResponse = Artist::createArtist([
            'member_id' => $member->member_id,
            'rating' => 0
        ]);

        if (isset($artistResponse['success']) && $artistResponse['success'] === false) {
            return response()->json(['error' => 'Failed to create artist'], 500);
        }

        $artist = Artist::where('artist_id', $member->member_id)->first();

        try {
            // Create and save the event
            $event = new Event($eventData); 
            if ($request->hasFile('event_files')) {
                $file = $request->file('event_files');

                // Generate a unique file name
                $fileName = $file->hashName();
                
                // Store the file in the public/event folder
                $path = $file->storeAs('event_images', $fileName, 'Tutorial02');
                
                // Save the file name in your database (or any other operation you want)
                $event->event_media = $fileName;
            }
            else{
                $event->event_media = $defaultImage;
            }
            $event->event_date = $eventDateTime;
            $event->rating = 0;
            $event->artist_id = $artist->artist_id;
            $event->save();
            try {
                // Add tags to the event
                EventTag::createEventTag($event->event_id, $eventTags['music-dance']);
                EventTag::createEventTag($event->event_id, $eventTags['mood']);
                EventTag::createEventTag($event->event_id, $eventTags['setting']);

                return redirect()->route('event', ['event_id' => $event->event_id])
                    ->with('message', 'Event created successfully.');

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add tags.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create event: ' . $e->getMessage()], 500);
        }
    }

}
