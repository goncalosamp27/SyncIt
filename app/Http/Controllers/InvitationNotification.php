<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\Request;

use App\Models\InvitationNotification;
use App\Models\Event;

class InvitationNotificationController extends Controller
{
    public function event()
	{
    	return $this->belongsTo(Event::class, 'event_id', 'event_id');
	}
}
