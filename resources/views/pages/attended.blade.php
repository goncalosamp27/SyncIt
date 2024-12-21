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
			Attended events 
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
				@include('partials.empty')
				<a href="{{ route('events') }}" class="refresh-button">Buy your first ticket!</button>			               
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

