@if ($notification->invitationNotification && $notification->invitationNotification->invitation)

    <div class="invitation-card">
        <div class="invitation-data">
            <div class="invitation-date">
                {{ date('d/m/Y - h:i A', strtotime($notification->notification_date)) }}
            </div>
            <div class = "invitation-username">
             <span class = "invitation-username">@</span>{{ $notification->invitationNotification->invitation->event->artist->member->username }}
            </div>
            <div class="invitation-text">
                {{ $notification->notification_message }}
            </div>
            <form action="{{ route('delete-notification', ['notification_id' => $notification->notification_id]) }}" method="POST">
                @csrf
                <button class="delete-notification-button" type="submit">
                    Delete Notification
                </button>
            </form>
        </div>

        <div class="invitation-event-card">
            @include('partials.event-card', ['event' => $notification->invitationNotification->invitation->event])
        </div>
    </div>    

@elseif ($notification -> eventNotification)
    <div class="invitation-card">
        <div class="invitation-data">
            <div class="invitation-date">
                {{ date('d/m/Y - h:i A', strtotime($notification->notification_date)) }}
            </div>
            </div>
            <div class="invitation-text">
                {{ $notification->notification_message }}
            </div>
            <form action="{{ route('delete-notification', ['notification_id' => $notification->notification_id]) }}" method="POST">
                @csrf
                <button class="delete-notification-button" type="submit">
                    Delete Notification
                </button>
            </form>
        </div>

        <div class="invitation-event-card">
            @include('partials.event-card', ['event' => $notification->eventNotification->event])
        </div>
    </div>    
    
@else 
    <h1>You have notifications but none of them are invitations.</h1>
    <h2>Wait for the next update :D</h2>
@endif
