<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommentVote;
use Illuminate\Support\Facades\Auth;

class CommentVoteController extends Controller
{
    public function vote(Request $request, $comment_id)
{
    $request->validate([
        'vote' => 'required|in:up,down',
    ]);

    $comment = Comment::findOrFail($comment_id);

    if ($request->vote === 'up') {
        $comment->increment('upvotes');
    } else {
        $comment->increment('downvotes');
    }

    return response()->json([
        'success' => true,
        'upvotes' => $comment->upvotes,
        'downvotes' => $comment->downvotes,
    ]);
}

}