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
			Your events
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">

			@if ($events->isEmpty())
                @include('partials.empty')
				<a href="{{ route('events.create') }}" class="refresh-button">Create your first event!</button>
			@else

			
			@endif
        </div>
		<div id="events-grid" class="events-grid">
			@foreach ($events as $event)
					<div class = "your-single-event">
						@include('partials.event-card', ['events' => $event])
						@can('delete', $event)
							<form action="{{ route('delete-event', ['event_id' => $event->event_id]) }}" method="POST" class="delete-button-form">
								@csrf
								<button type="submit" class="delete-button">🗑️</button>
							</form>
						@endcan
					</div>
			@endforeach
		</div>	
		<div class="pagination-container">
            {{ $events->links('pagination::bootstrap-4') }}
        </div>
	</div>
@endsection