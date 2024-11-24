<?php

namespace App\Policies;

use App\Models\Member;

class AdminPolicy
{
    public function accessAdmin(User $user)
    {
        return $user->role === 'admin';
    }
}
