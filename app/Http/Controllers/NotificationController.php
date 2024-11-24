<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Models\Notification;
use App\Models\Member;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        // Get the authenticated member
        $member = Auth::user();

        // Load notifications relationship (if exists on the Member model)
        $member->load('notifications');

        // Alternatively, fetch notifications directly from the Notification model
        $notifications = Notification::where('member_id', $member->member_id)->get();

        // Pass notifications and member to the view
        return view('pages.notifications', [
            'notifications' => $notifications,
            'member' => $member,
        ]);
    }
}