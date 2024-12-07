<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Restriction;

class RestrictionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function isBanned(?Member $auth, Member $member): bool
    {
        //dd($member->member_id);
        return( Restriction::where('member_id', $member->member_id)
            ->where('type', 'Ban')
            ->exists());
    }
}
