<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Artist;

class HomeController extends Controller
{
    public function index()
    {
        $pastEvents = Event::past()->orderBy('event_date', 'desc')->get();
        $futureEvents = Event::future()->orderBy('event_date', 'asc')->get();

        $artists = Artist::all();

        return view('pages.home', [
            'pastEvents' => $pastEvents,
            'futureEvents' => $futureEvents,
            'artists' => $artists,
        ]);
    }
}