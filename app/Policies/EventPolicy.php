<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Event;

class EventPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function edit(Member $member, Event $event)
    {
        return $event->artist->member->member_id === $member->member_id;
    }


}
