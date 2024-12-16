<div class="invitation-card">
	<div class="invitation-data">
		<div class = "invitation-username">
		 <span class = "invitation-username">@</span>{{ $invitation->event->artist->member->username }}
		</div>
		<div class="invitation-text">
			invited you to their event!
		</div>
		<div class="invitation-date">
			{{ date('d/m/Y - h:i A', strtotime($invitation->event->event_date)) }}
		</div>
		<form action="{{ route('delete-invitation', ['invitation_id' => $invitation->invitation_id]) }}" method="POST">
			@csrf
			<button class="delete-notification-button" type="submit">
				Delete Invitation
			</button>
		</form>
	</div>
	<div class="invitation-event-card">
		@include('partials.event-card', ['event' => $invitation->event])
	</div>
</div> 