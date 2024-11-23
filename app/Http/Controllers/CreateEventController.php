<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Event;
use App\Models\Tag;
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
    // Validate the form data
    $validatedData = $request->validate([
        'event_name' => 'required|string|max:100',
        'event_date' => 'required|date|after_or_equal:today',
        'location' => 'required|string|max:100',
        'description' => 'required|string',
        'refund' => 'required|numeric|between:0,100',
        'price' => 'required|numeric|min:0',
        'type_of_event' => 'required|in:Public,Private',
        'capacity' => 'required|numeric|min:10',
    ]);
    $defaultImage = 'default_event.png';
    $member = Auth::user(); 
    try {
        // Create a new Event instance
        $event = new Event();
        // Assign validated data to the event's attributes
        $event->event_name = $validatedData['event_name'];
        $event->event_date = $validatedData['event_date'];
        $event->location = $validatedData['location'];
        $event->description = $validatedData['description'];
        $event->refund = $validatedData['refund'];
        $event->price = $validatedData['price'];
        $event->type_of_event = $validatedData['type_of_event'];
        $event->capacity = $validatedData['capacity'];
        $event->event_media = 'default_event.png'; // Default image for the event
        $event->rating = 0; // Default rating for the event
        $event->artist_id = $member->member_id; // Default rating for the event
        $event-> event_media = $defaultImage;

        $event->save();

        return redirect()->route('home')->with('success', 'Event created successfully!');
    } catch (Exception $e) {
        // Log the error for debugging purposes
        \Log::error('Error creating event: ' . $e->getMessage());

        // Return an error message to the user
        return redirect()->back()->withErrors(['error' => 'An error occurred while creating the event. Please try again.']);
    }
}



}
