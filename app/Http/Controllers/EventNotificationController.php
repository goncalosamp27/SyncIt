<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\EventNotification;
use App\Models\Event;

class EventNotificationController extends Controller
{
    public function event()
	{
    	return $this->belongsTo(Event::class, 'event_id', 'event_id');
	}
}
