<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Tag;
use App\Models\Event;

class TagController extends Controller
{
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
}
