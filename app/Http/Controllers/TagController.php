<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Tag;

class TagController extends Controller
{
    public function showTagsPerType()
    {
        // Fetch tags where tag_name is 'Music' or 'Dance' (Genres)
        $tagsMusic = Tag::type(['Music'])->get();
		$tagsDance = Tag::type(['Dance'])->get();
		$tagsMood = Tag::type(['Mood'])->get();
		$tagsSettings = Tag::type(['Settings'])->get();

        return view('pages.events', [
            'tagsMusic' => $tagsMusic,
            'tagsDance' => $tagsDance,
			'tagsMood' => $tagsMood,
			'tagsSettings' => $tagsSettings,
        ]);
    }
}
