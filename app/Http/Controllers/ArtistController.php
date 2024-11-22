<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Artist;

class ArtistController extends Controller
{
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
}