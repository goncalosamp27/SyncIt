@extends('layouts.app')

@section('content')
	<div class="tickets-div">
		@if (session('success'))
			<div class = "success">
				{{ session('success') }}
			</div>
		@endif
		@if (session('error'))
			<div class="error">
				{{ session('error') }}
			</div>
		@endif

		<div class="tickets-title">
			Showing your Invitations for upcoming shows:
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">
			@php
				$now = new \DateTime();
				$validInvitations = $member->invitations->filter(function ($invitations) use ($now) {
					return new \DateTime($invitation->event->event_date) > $now;
				});
			@endphp
			@if ($validInvitations->isEmpty())
				<p class="no-tickets">You do not own any tickets for upcoming events.</p>
			@else
				@foreach ($validInvitations as $invitation)
					<h1>crazy</h1>
				@endforeach
			@endif
		</div>
	</div>
@endsection