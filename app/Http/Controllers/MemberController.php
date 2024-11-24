<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

class MemberController extends Controller
{
    public function edit()
    {
        $member = Auth::user(); 

        return view('pages.profile-edit', compact('member'));
    }
}
