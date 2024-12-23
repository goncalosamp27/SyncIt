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
    //render comments
    public function index($event_id)
    {
            $comments = Comment::where('event_id', $event_id)
                ->with('member') // Load related member data
                ->orderBy('comment_date', 'desc') // Explicitly order by comment_date
                ->get();

            return view('partials.comment-list', compact('comments'))->render();
        
    }
    //save somment in db
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'event_id' => 'required|exists:event,event_id',
            'text' => 'required|string|max:500',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov'
        ]);

        $comment = new Comment();
        $comment->text = $request->text;
        $comment->event_id = $request->event_id;
        $comment->comment_date = now();
        $comment->member_id = Auth::id();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            // Generate a unique file name
            $fileName = $file->hashName();
            
            // Store the file in the public/event folder
            $path = $file->storeAs('comment_images', $fileName, 'Tutorial02');
            
            // Save the file name in your database (or any other operation you want)
            $comment->file_path = $fileName;
        }

        $comment->save();
        $comments = Comment::where('event_id', $request->event_id)->with('member')->get();
        $comments_html = view('partials.comment-list', compact('comments'))->render();

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }
    
    //update comment
    public function update(Request $request, $commentId)
    {
        $request->validate([
            'text' => 'required|string|max:500',
            'comment_date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $comment = Comment::findOrFail($commentId);

            $comment->text = $request->input('text');
            $comment->comment_date = $request->input('comment_date');
            $comment->save();  

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.',
                'comment' => $comment,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update the comment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //Delete comment
    public function destroy($comment_id)
{
    try {
        $comment = Comment::findOrFail($comment_id);

        // Check if the authenticated user is the owner of the comment
        if (Auth::id() !== $comment->member_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Delete the comment
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
    } catch (\Exception $e) {
        \Log::error('Error deleting comment: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to delete comment'], 500);
    }
}
}
