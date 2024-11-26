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
			Showing Your Events:
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">
			@if ($events->isEmpty())
    			<p class="no-tickets">You do not own any events.</p>
				<a class="take-me-button-member" href="{{ url('/create') }}">Create your first Event!</a>
			@else
			<div class = "your-events">
    			@foreach ($events as $event)
						<div class = "your-single-event">
        					@include('partials.event-card', ['events' => $event])
							<form action="{{ route('delete-event', ['event_id' => $event->event_id]) }}" method="POST" class="delete-button-form">
								@csrf
								<button type="submit" class="delete-button">🗑️</button>
							</form>
						</div>
    			@endforeach
			</div>	
			@endif
        </div>
	</div>
@endsection