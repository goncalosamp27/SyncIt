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
        $eventData = $request->only([
            'event_name',
            'event_date',
            'location',
            'description',
            'refund',
            'price',
            'type_of_event',
            'capacity',
            'genre',
        ]);

        $defaultImage = 'default_event.png';
        $member = Auth::user();
        if (!$member) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }
        if (Member::isArtist($member->member_id)) {
            try {
                $event = new Event($eventData);
                $event->event_media = $defaultImage;
                $event->rating = 0;
                $event->artist_id = Artist::getArtistIdByMemberId($member->member_id);

                // Save the event to the database
                $event->save();
                return redirect()->route('event.show', ['id' => $event->event_id])
                    ->with('message', 'Event created successfully');

            } catch (Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()])
                    ->withInput(); 
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
            $event = new Event($eventData);
            $event->event_media = $defaultImage;
            $event->rating = 0;
            $event->artist_id = $artist->artist_id;

            $event->save();
            return redirect()->route('event.show', ['id' => $event->event_id])
                ->with('message', 'Event created successfully');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create event: ' . $e->getMessage()])
                    ->withInput(); 
        }

    }


}
