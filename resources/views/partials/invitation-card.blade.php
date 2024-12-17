<div class="invitation-card">
	<div class="invitation-data">
		<div class = "invitation-username">
		 <span class = "invitation-username">@</span>{{ $invitation->invitor->username }}
		</div>
		<div class="invitation-date">
			{{ date('d/m/Y - h:i A', strtotime($invitation->event->event_date)) }}
		</div>
		<div class="invitation-text">
            @if ($invitation->invitor->member_id === $invitation->event->artist->member->member_id)
                Invited you to their event!
            @else
                Invited you to
                <span class="invitation-username"> {{'@'}}{{$invitation->event->artist->member->username}}'s</span> event!
            @endif
        </div>
		<div class ="margin-delete">
		<form action="{{ route('delete-invitation', ['invitation_id' => $invitation->invitation_id]) }}" method="POST">
			@csrf
			<button class="delete-notification-button" type="submit">
				Delete Invitation
			</button>
		</form>
		</div>
	</div>
	<div class="invitation-event-card">
		@include('partials.event-card', ['event' => $invitation->event])
	</div>
</div> 