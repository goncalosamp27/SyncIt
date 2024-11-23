<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Artist;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $pastEvents = Event::pastEvents();
            $futureEvents = Event::upcomingEvents();
            $artists = Artist::all();

            
            return view('pages.home', [
                'pastEvents' => $pastEvents,
                'futureEvents' => $futureEvents,
                'artists' => $artists,
            ]);
        } catch (\Exception $e) {
            // Log the error and show a user-friendly message
            \Log::error('Error fetching data in HomeController: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }
}