<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    protected $primaryKey = 'comment_id';

    public $timestamps = false;

    protected $fillable = [
        'file_path',

    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'text' => 'required|string',
            'comment_date' => 'required|date|after_or_equal:today',
            'event_id' => 'required|exists:event,event_id',
            'member_id' => 'required|exists:member,member_id',
            'response_comment_id' => 'nullable|exists:comment,comment_id',
        ]);

        return $validator;
    }
    //Relatiosnhips
    // Many comments to one event (each comment belongs to one event)
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    // Many comments to one member (each comment belongs to one member)
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    // Self association: a comment may have a parent comment (one comment can respond to another)
    public function parentComment()
    {
        return $this->belongsTo(Comment::class, 'response_comment_id', 'comment_id');
    }

    // Self association: a comment can have many replies (children)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'response_comment_id', 'comment_id');
    }

    public function votes()
    {
        return $this->hasMany(CommentVote::class, 'comment_id', 'comment_id');
    }

    public function upvotes()
    {
        return $this->hasMany(CommentVote::class, 'comment_id', 'comment_id')
                    ->where('vote', true); // Only upvotes
    }

    // Fetch all downvotes (votes where vote is 2)
    public function downvotes()
    {
        return $this->hasMany(CommentVote::class, 'comment_id', 'comment_id')
                    ->where('vote', false); // Only downvotes
    }
}
