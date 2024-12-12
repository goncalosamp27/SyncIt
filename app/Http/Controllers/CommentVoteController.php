<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentVoteController extends Controller
{
    // Upvote a comment
    public function upvote($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $comment->upvotes()->attach(Auth::id());
        
        return response()->json(['message' => 'Comment upvoted successfully']);
    }

    // Downvote a comment
    public function downvote($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $comment->downvotes()->attach(Auth::id());
        
        return response()->json(['message' => 'Comment downvoted successfully']);
    }

    // Remove vote from a comment
    public function removeVote($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $comment->votes()->detach(Auth::id());
        
        return response()->json(['message' => 'Vote removed successfully']);
    }
}
