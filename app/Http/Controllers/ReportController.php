<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Report;
use App\Models\Member;
use App\Models\Event;

class ReportController extends Controller {

    public function createReport(Request $request, $event_id)
    {
        $request->event_id = (int) $request->event_id; // Ensure it is an integer

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Check if event exists using find()
        $event = Event::find($event_id);
        if (!$event) {
            return redirect()->back()->withErrors(['event_id' => 'Event not found.']);
        }

        // Check if member exists using find()
        $member = Member::find(Auth::id()); // Assuming the member is the currently authenticated user
        if (!$member) {
            return redirect()->back()->withErrors(['member_id' => 'Member not found.']);
        }
        try {

            Report::create([
                'event_id' => $event_id,
                'member_id' => Auth::id(),
                'message' => $request->input('message')
            ]);

            return redirect()->back()->with('success', 'Report sent successfully!');
        } catch (\Exception $e) {
            // Catch and log any error
            \Log::error("Report creation failed: " . $e->getMessage());
            dd($e->getMessage()); 

            return redirect()->route('home')->withErrors('An error occurred while creating the report.');
        }
    }
    
}