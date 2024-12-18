<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class CommentVote extends Model
{
    use HasFactory;

    protected $table = 'vote_comment'; 

    protected $primaryKey = 'vote_comment_id';

    public $timestamps = false; 

    protected $fillable = [
        'comment_id',
        'member_id',
        'vote'
    ];

    public static function validate($data)
    {
        return Validator::make($data, [
            'comment_id' => 'required|exists:comment,comment_id',
            'member_id' => 'required|exists:member,member_id',
            'vote' => 'required|boolean', 
        ]);
    }

    // Relationships
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
