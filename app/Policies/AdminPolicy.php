<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Artist;
use App\Models\Restriction;

class AdminPolicy
{
    public function beAdmin(User $user)
    {
        return auth('admin')->check();
    }
    public function isRestricted(?Member $auth, Member $member): bool
    {
        return( Restriction::where('member_id', $member->member_id)
            ->exists());
    }

    public function viewProfile(?Member $auth, Member $member){
        return Artist::where('artist_id', $member->member_id)->exists();
    }
}
