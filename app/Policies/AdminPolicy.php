<?php

namespace App\Policies;

use App\Models\Member;

class AdminPolicy
{
    public function beAdmin(User $user)
    {
        return auth('admin')->check();
    }
}
