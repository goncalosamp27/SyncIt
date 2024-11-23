@extends('layouts.app')

@section('content')
	<div class="tickets-div">
		<div class="tickets-title">
			Showing Your Tickets:
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">
			@if (!$member->relationLoaded('tickets') || $member->tickets->isEmpty())
    			<p class="no-tickets">You do not own any tickets.</p>
			@else
    			@foreach ($member->tickets as $ticket_)
        			@include('partials.ticket-card', ['ticket' => $ticket_])
    			@endforeach
			@endif
        </div>
	</div>
@endsection