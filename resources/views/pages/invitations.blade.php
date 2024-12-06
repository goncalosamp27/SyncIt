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
			Showing your Invitations for upcoming events:
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">

			@php
				$now = new \DateTime();
				$validInvitations = $member->invitations->filter(function ($invitations) use ($now) {
					return new \DateTime($invitations->event->event_date) > $now;
				});
			@endphp

			@if ($validInvitations->isEmpty())
				<p class="no-tickets">You were not invited for any upcoming events.</p>
			@else
				@foreach ($validInvitations as $invitation)
					@include('partials.invitation-card', ['invitation' => $invitation])
				@endforeach
			@endif
		</div>
	</div>
@endsection