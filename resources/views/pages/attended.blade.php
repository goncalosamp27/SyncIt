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
			Showing Events you attended.
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">
			@php
				$now = new \DateTime();
				$validTickets = $member->tickets->filter(function ($ticket) use ($now) {
					return new \DateTime($ticket->event->event_date) <= $now;
				});
			@endphp
			@if ($validTickets->isEmpty())
				<p class="no-tickets">You did not attend an event yet.</p>
			@else
				@foreach ($validTickets as $ticket_)
					@include('partials.ticket-card', ['ticket' => $ticket_])
				@endforeach
			@endif
		</div>

		<div class="pagination-container">
            {{ $tickets->links('pagination::bootstrap-4') }}
        </div>
	</div>
@endsection

