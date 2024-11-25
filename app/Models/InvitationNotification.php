<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationNotification extends Model
{
    use HasFactory;

    protected $table = 'invitation_notification';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    public function invitation()
    {
        return $this->belongsTo(Invitation::class, 'invitation_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
