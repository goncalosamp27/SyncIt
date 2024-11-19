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
    //associations
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }


    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    
    public function parentComment()
    {
        return $this->belongsTo(Comment::class, 'response_comment_id', 'comment_id');
    }

    
    public function replies()
    {
        return $this->hasMany(Comment::class, 'response_comment_id', 'comment_id');
    }

    
}
