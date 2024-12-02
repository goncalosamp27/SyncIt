<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CommentVote extends Model
{
    use HasFactory;

    protected $table = 'comment_vote';

    protected $primaryKey = 'vote_id';

    public $timestamps = false;

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'comment_id' => 'required|exists:comment,comment_id',
            'member_id' => 'required|exists:member,member_id',
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        return $validator;
    }

    // Relationships
    // Each vote belongs to one comment
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }

    // Each vote is cast by one member
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
