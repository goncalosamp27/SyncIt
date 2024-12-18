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
        return response()->json([
            'success' => true
        ]);
        $request->validate([
            'vote' => 'required|boolean',
        ]);

        $comment = Comment::findOrFail($comment_id);
        $member_id = Auth::id();

        CommentVote::updateOrCreate(
            ['comment_id' => $comment_id, 'member_id' => $member_id],
            ['vote' => $request->vote]
        );

        $upvotes = CommentVote::where('comment_id', $comment_id)->where('vote', true)->count();
        $downvotes = CommentVote::where('comment_id', $comment_id)->where('vote', false)->count();

        return response()->json([
            'success' => true,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
        ]);
    }
}

