<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Artist;

class ArtistController extends Controller
{
    public function show($artistId){
        if (!is_numeric($artistId)) {
            abort(404, 'Invalid event identifier');
        }

        $artist = Artist::with('events')->find($artistId);
        if (!$artist) {
            abort(404, 'Artist not found');
        }

        if ($artist->artist_id == 1) {
            abort(404, 'This page is not available.');
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
        $artists = Artist::paginate(20); 

        // Return the view and pass the events data to the view
        return view('pages.artists', ['artists' => $artists]);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search'); // Search term from the user input

        // Handle the search query using PostgreSQL full-text search

        if (empty($searchTerm)) {
            $artists = Artist::all();
        } 
        else{
            $artists = Artist::select('artist.*')
            ->whereRaw("fts_artist @@ plainto_tsquery('english', ?)", [$searchTerm])
            ->get(); 
        }

        return view('pages.artists', [
            'artists' => $artists,
        ]);
    }
}