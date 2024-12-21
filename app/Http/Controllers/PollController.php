<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Event;

class PollController extends Controller
{
    public function showCreatePoll(string $event_id)
    {
        $event = Event::findOrFail($event_id);
        return view(
            'pages.create-poll',
            [
                'event' => $event
            ]
        );
    }
    public function storePoll(Request $request, string $event_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $poll = Poll::create([
            'event_id' => $event_id, 
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Create options for the poll
        foreach ($request->options as $option) {
            $poll->options()->create(['name' => $option]);
        }

        $event = Event::findOrFail($event_id);
        return view('event', [
            'event' => $event,
            'poll' => $poll,

        ]);
    }
}
