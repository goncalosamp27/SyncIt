<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentNotification extends Model
{
    use HasFactory;

    protected $table = 'comment_notification';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id'); // Correct
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
