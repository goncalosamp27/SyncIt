<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'event_id',
    ];

    protected $table = 'event_notification';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
