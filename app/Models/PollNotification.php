<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollNotification extends Model
{
    use HasFactory;

    protected $table = 'poll_notification';

    protected $primaryKey = 'notification_id';

    public $timestamps = false;

    
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'notification_id');
    }

    
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'poll_id');
    }
}
