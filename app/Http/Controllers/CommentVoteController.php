<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\CommentVote;
use Illuminate\Support\Facades\Auth;

class CommentVoteController extends Controller
{
    public function voteComment(Request $request, $comment_id)
{
    $request->validate([
        'vote' => 'required|boolean',
    ]);

    $comment = Comment::findOrFail($comment_id);
    $member_id = Auth::id();

    // Check if a vote already exists for this member
    $existingVote = CommentVote::where('comment_id', $comment_id)
                                ->where('member_id', $member_id)
                                ->first();

    if ($existingVote) {
        // If the existing vote is different from the new vote, update it
        if ($existingVote->vote !== $request->vote) {
            $existingVote->update(['vote' => $request->vote]);
        }
    } else {
        // Create a new vote since one doesn't exist
        CommentVote::create([
            'comment_id' => $comment_id,
            'member_id' => $member_id,
            'vote' => $request->vote,
        ]);
    }

    // Recalculate upvotes and downvotes
    $upvotes = CommentVote::where('comment_id', $comment_id)->where('vote', true)->count();
    $downvotes = CommentVote::where('comment_id', $comment_id)->where('vote', false)->count();

    return response()->json([
        'success' => true,
        'upvotes' => $upvotes,
        'downvotes' => $downvotes,
    ]);
}

}

