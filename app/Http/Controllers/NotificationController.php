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

    public function deleteNotification(string $notification_id)
    {
        try {
            // Fetch the event
            $notification = Notification::findOrFail($notification_id);

            // Debug: Check if event is valid
            if (!$notification) {
                return redirect()->route('notifications')->with('error', "Notification not found.");
            }

            // Attempt to delete the event
            $notification->delete();

            return redirect()->route('notifications')->with('success', "Notification #{$notification_id} deleted successfully!");
        } 
        catch (\Exception $e) 
        {
            // Log the actual error
            dd($e->getMessage());
            \Log::error("Failed to delete notification: {$e->getMessage()}");

            return redirect()->route('notifications')->with('error', "Failed to delete the notification.");
        }
    }
}