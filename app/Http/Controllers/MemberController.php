<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Member;

class MemberController extends Controller
{
    public function display_members()
    {
        $members = Member::all();

        return view('pages.admin', ['members' => $members]);
    }
}