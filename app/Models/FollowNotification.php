<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowNotification extends Model
{
    use HasFactory;

    protected $table = 'follow_notification';

    protected $primaryKey = 'notification_id';

    public $timestamps = false;

    
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'notification_id');
    }

    
    public function follower()
    {
        return $this->belongsTo(Member::class, 'follower_id', 'member_id');
    }
}
