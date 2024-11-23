<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Artist;

class ArtistController extends Controller
{
    public function show($artistId){
        $artist = Artist::with('events')->find($artistId);
    
        if (!$artist) {
            abort(404, 'Artist not found');
        }

        $followersCount = $artist->getFollowersCount();
        return view('pages.artist', ['artist' => $artist, 'followersCount' => $followersCount]);
    }

    public function getArtistEvents($artist_id)
    {
    // Find the artist by ID
    $artist = Artist::find($artist_id);

    if (!$artist) {
        return response()->json(['error' => 'Artist not found'], 404);
    }

        // Retrieve all events related to the artist
        $events = $artist->events;

        return response()->json($events);
    }

    public function display_artists()
    {
        // Retrieve all events
        $artists = Artist::all(); 

        // Return the view and pass the events data to the view
        return view('pages.home', ['events' => $events]);
    }
}