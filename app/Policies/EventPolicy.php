<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventPolicy {
    public function __construct() {}
    public function edit(Member $member, Event $event) {
        return $event->event_status === 'Active' && ($event->artist->member->member_id === $member->member_id || Auth::guard('admin')->check()) && $event->event_date > now() ;
    }
    public function delete(Member $member, Event $event) {
        return $event->event_status === 'Cancelled' && $event->artist->member->member_id === $member->member_id;
    }
    public function seeParticipants(Member $member, Event $event) {
        return $event->event_status !== 'Cancelled';
    }
    public function canInvite(Member $member, Event $event) {
        if ($event->artist->member->member_id === $member->member_id && $event->event_date > now()) {
            return true;
        }    
        if ($event->type_of_event === 'Public' && $event->event_date > now()) {
            return true;
        }
        return false;
    }
}
