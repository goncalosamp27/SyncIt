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
			Showing Your Notifications:
		</div>
		<div class ="new-purple-line"></div>
		<div class="tickets-list">
			@if ($member->notifications->isEmpty())
    			<p class="no-tickets">You do not have any notifications.</p>
			@else
					@foreach ($notifications as $notification)
						@include('partials.notification-card', ['notifications' => $notification])
					@endforeach
			@endif
        </div>
		
        <div class="pagination-container">
           {{-- {{ $notifications->links() --}}
			{{ $notifications->links('pagination::bootstrap-4') }}
        </div>
	</div>
@endsection