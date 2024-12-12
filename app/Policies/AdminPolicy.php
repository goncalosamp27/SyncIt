<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Artist;

class AdminPolicy
{
    public function beAdmin(User $user)
    {
        return auth('admin')->check();
    }

    public function isRestricted(?Member $auth, Member $member)
    {
        return in_array($member->member_status, ['Banned', 'Suspended']);
    }

    public function viewProfile(?Member $auth, Member $member){
        return Artist::where('artist_id', $member->member_id)->exists();
    }
}
