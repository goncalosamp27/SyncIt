<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;
use App\Models\Member;
use App\Models\Admin;
use App\Models\Restriction;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Notification;
use App\Models\EventNotification;
use Carbon\Carbon;


class LoginController extends Controller
{
    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check() || Auth::guard('admin')->check()) {
            return redirect()->route('home');
        } 
        return view('auth.login');

    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        // Validate incoming request
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to authenticate as Admin (use email only)
        if (filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)) {
            $admin = Admin::where('email', $credentials['login'])->first();
    
            if ($admin && Auth::guard('admin')->attempt(['email' => $credentials['login'], 'password' => $credentials['password']], $request->filled('remember'))) {
                $request->session()->regenerate();
    
                return redirect()->intended('/home');
            }
        }
            

        $member = Member::where($loginField, $credentials['login'])->first();

        if ($member) {
            switch ($member->member_status) {
                case 'Banned':
                    return back()->withErrors([
                        'login' => 'Your account is banned. Please contact support for assistance.',
                    ]);
                case 'Suspended':
                    $activeRestriction = Restriction::where('member_id', $member->member_id)
                        ->where('type', 'Suspension')
                        ->get()
                        ->filter(function ($restriction) {
                            $end = Carbon::parse($restriction->start)->addDays($restriction->duration);
                            return $end->greaterThan(now());
                        })
                        ->first();

                    if ($activeRestriction) {
                        $end = Carbon::parse($activeRestriction->start)->addDays($activeRestriction->duration);
                        $timeLeft = $end->diffForHumans(now(), true); // Get a human-readable difference (e.g., "3 days", "2 hours")
                    
                        return back()->withErrors([
                            'login' => "Your account is suspended. Time remaining: $timeLeft.",
                        ]);
                    }

                    else{
                        $member->restrictions()->delete();
                    }

                case 'Active':
                    if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->filled('remember'))) {
                        $request->session()->regenerate();

                        $this->sendEventNotifications($member->member_id);

                        return redirect()->intended('/home');
                    }
                    break;
            }
        }
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    private function sendEventNotifications($memberId)
    {
        $now = now();
        $nextDay = now()->addDay(); 
    
        $events = Ticket::with('event') // Load the related event
            ->where('member_id', $memberId)
            ->whereHas('event', function ($query) use ($now, $nextDay) {
                $query->whereBetween('event_date', [$now, $nextDay])
                      ->where('event_status', 'Active');
            })
            ->whereDoesntHave('event.notifications', function ($query) use ($memberId) {
                $query->where('member_id', $memberId);
            })
            ->select('event_id') 
            ->distinct() 
            ->get();
    
        foreach ($events as $ticket) {
            $event = $ticket->event;
    
            $notification = Notification::create([
                'notification_message' => "\"{$event->event_name}\" is happening soon! Make sure you don't miss it!",
                'notification_date' => now(),
                'member_id' => $memberId,
            ]);    
            EventNotification::create([
                'notification_id' => $notification->notification_id,
                'event_id' => $event->event_id,
            ]);
        }
    }
    

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')
        ->with('success', 'You have logged out successfully!');
    }
    
}
