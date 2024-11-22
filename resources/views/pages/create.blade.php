@extends('layouts.app')

@section('content')
	<div class = "create-event">
		<div class="create-event-title">
			<h1>Create your own Event: </h1>
		</div>
		<div class="purple-line"></div>
	</div>

	@section('content')
<div class="container">
    <h1>Create Event</h1>
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <!-- Event Name -->
        <div class="mb-3">
            <label for="event_name" class="form-label">Event Name</label>
            <input type="text" id="event_name" name="event_name" class="form-control" value="{{ old('event_name') }}" required>
            @error('event_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Event Date -->
        <div class="mb-3">
            <label for="event_date" class="form-label">Event Date</label>
            <input type="date" id="event_date" name="event_date" class="form-control" value="{{ old('event_date') }}" required>
            @error('event_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Location -->
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}" required>
            @error('location')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Refund -->
        <div class="mb-3">
            <label for="refund" class="form-label">Refund (%)</label>
            <input type="number" id="refund" name="refund" class="form-control" value="{{ old('refund') }}" min="0" max="100" required>
            @error('refund')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" min="0" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Type of Event -->
        <div class="mb-3">
            <label for="type_of_event" class="form-label">Type of Event</label>
            <select id="type_of_event" name="type_of_event" class="form-control" required>
                <option value="Public" {{ old('type_of_event') == 'Public' ? 'selected' : '' }}>Public</option>
                <option value="Private" {{ old('type_of_event') == 'Private' ? 'selected' : '' }}>Private</option>
            </select>
            @error('type_of_event')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Event</button>
    </form>
</div>
@endsection