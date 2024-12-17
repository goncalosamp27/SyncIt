@if ($notification->invitationNotification && $notification->invitationNotification->invitation)

    <div class="invitation-card">
        <div class="invitation-data">
            <div class="invitation-date">
                {{ date('d/m/Y - h:i A', strtotime($notification->notification_date)) }}
            </div>
            <div class = "invitation-username">
                <span class="invitation-username">@</span>{{ $notification->invitationNotification->invitation->invitor->username }}
                @if ($notification->invitationNotification->invitation->invitor->member_id === $notification->invitationNotification->invitation->event->artist->member->member_id)
                    <p>Invited you to their event!</p>
                @else
                    <div class="invitation-message">
                        <p>Invited you to</p>
                        <span class="invitation-username">
                            {{'@'}}{{ $notification->invitationNotification->invitation->event->artist->member->username }}'s 
                        </span>event!
                    </div>
            @endif
            </div>
            <div class="invitation-text">
                {{ $notification->notification_message }}
            </div>
            <div class ="margin-delete">
            <form action="{{ route('delete-notification', ['notification_id' => $notification->notification_id]) }}" method="POST">
                @csrf
                <button class="delete-notification-button" type="submit">
                    Delete Notification
                </button>
            </form>
            </div>
        </div>

        <div class="invitation-event-card">
            @include('partials.event-card', ['event' => $notification->invitationNotification->invitation->event])
        </div>
    </div>    

@elseif ($notification -> eventNotification && $notification->eventNotification->event)

    <div class="invitation-card">
        <div class="invitation-data">
            <div class="invitation-date">
                {{ date('d/m/Y - h:i A', strtotime($notification->notification_date)) }}
            </div>
            @if ($notification->notification_message !== "The event has been cancelled. Your ticket has been refunded.")
                <div class = "invitation-username">
                    <span class = "invitation-username">@</span>{{ $notification->eventNotification->event->artist->member->username }}<span class = "invitation-username"></span>
                </div>
                <div class="invitation-text">
                    {{ $notification->notification_message }}
                </div>
            @else 
                <div class="invitation-text">
                   The event has been cancelled.
                </div>
                <div class="invitation-text">
                    Your ticket has been refunded.
                </div>
            @endif  
            <div class ="margin-delete">  
            <form action="{{ route('delete-notification', ['notification_id' => $notification->notification_id]) }}" method="POST">
                @csrf
                <button class="delete-notification-button" type="submit">
                    Delete Notification
                </button>
            </form>
            </div>
        </div>
        <div class="invitation-event-card">
            @include('partials.event-card', ['event' => $notification->eventNotification->event])
        </div>
    </div>   
@elseif ($notification -> commentNotification && $notification->commentNotification->comment)

    <div class="invitation-card">
        <div class="invitation-data">
            <div class="invitation-date">
                {{ date('d/m/Y - h:i A', strtotime($notification->notification_date)) }}
            </div>
            <div class = "invitation-username">
            <span class = "invitation-username">@</span>{{ $notification->commentNotification->comment->member->username }}
            </div>
            <div class="invitation-text">
                {{ $notification->notification_message }}
            </div>
            <div class="invitation-text" style="font-style: italic; color: var(--hover-color);">
                "{{ $notification->commentNotification->comment->text }}"
            </div>
            <div class ="margin-delete">
            <form action="{{ route('delete-notification', ['notification_id' => $notification->notification_id]) }}" method="POST">
                @csrf
                <button class="delete-notification-button" type="submit" style="margin-top: 5rem;">
                    Delete Notification
                </button>
            </form>
            </div>
        </div>

        <div class="invitation-event-card">
            @include('partials.event-card', ['event' => $notification->commentNotification->comment->event])
        </div>
    </div>    
@endif
