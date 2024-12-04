<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Comment;
use App\Models\Event;
use Exception;

class CommentController extends Controller
{
    //Display comments related to a specific event.
    public function showComments($event_id)
    {
        try {
            $event = Event::findOrFail($event_id);
            $comments = Comment::where('event_id', $event_id)->with('member')->get();

            return view('pages.comments', [
                'event' => $event,
                'comments' => $comments,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve comments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load comments.');
        }
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'text' => 'required|string|max:500',
        ]);

        // Get the event by ID
        $event = Event::findOrFail($request->event_id);

        $comment = new Comment();
        $comment->text = $request->input('text');
        $comment->event_id = $request->input('event_id');
        $comment->member_id = auth()->id();  
        $comment->save();

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }
 
    // Delete a comment.
    public function destroy($comment_id)
    {
        try {
            $comment = Comment::findOrFail($comment_id);

            if ($comment->member_id != Auth::id()) {
                return redirect()->back()->with('error', 'You do not have permission to delete this comment.');
            }

            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete comment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete comment.');
        }
    }

    //Update a comment.
    public function update(Request $request, $comment_id)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        try {
            $comment = Comment::findOrFail($comment_id);

            if ($comment->member_id != Auth::id()) {
                return redirect()->back()->with('error', 'You do not have permission to edit this comment.');
            }

            $comment->text = $validated['text'];
            $comment->save();

            return redirect()->back()->with('success', 'Comment updated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to update comment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update comment.');
        }
    }
}
