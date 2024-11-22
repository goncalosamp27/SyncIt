@extends('layouts.app')

@section('content')
	<div class = "create-event">
		<div class="create-event-title">
			<h1>Create your own Event: </h1>
		</div>
		<div class="purple-line"></div>
	</div>

<div class="create-event-form">
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <!-- Event Name -->
        <div class="create-event-input">
            <label for="event_name" class="form-label">Event Name</label>
            <input type="text" id="event_name" name="event_name" placeholder="Enter a name for your event:"  class="form-control" value="{{ old('event_name') }}" required>
            @error('event_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Event Date -->
        <div class="create-event-input">
            <label for="event_date" class="form-label">Event Date</label>
            <input type="date" id="event_date" name="event_date" placeholder="Enter a date for your event:" class="form-control" value="{{ old('event_date') }}" required>
            @error('event_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Location -->
        <div class="create-event-input">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" placeholder="Enter a location for your event:" class="form-control" value="{{ old('location') }}" required>
            @error('location')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="create-event-input">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" placeholder="Enter a description for your event:" class="form-control" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

         <!-- Price -->
         <div class="create-event-input">
            <label for="price" class="form-label">Price</label>
            <input type="number" id="price" name="price" placeholder="Enter a Price for your event:" class="form-control" value="{{ old('price') }}" min="0" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Refund -->
        <div class="create-event-input">
            <label for="refund" class="form-label">Refund (%)</label>
            <input type="number" id="refund" name="refund" placeholder="Enter a refund % for your event:" class="form-control" value="{{ old('refund') }}" min="0" max="100" required>
            @error('refund')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Type of Event -->
        <div class="create-event-input">
            <label for="type_of_event" class="form-label">Type of Event</label>
            <select id="type_of_event" name="type_of_event" class="form-control" required>
                <option value="Public" {{ old('type_of_event') == 'Public' ? 'selected' : '' }}>Public</option>
                <option value="Private" {{ old('type_of_event') == 'Private' ? 'selected' : '' }}>Private</option>
            </select>
            @error('type_of_event')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Genre -->
        <div class="create-event-input">
            <label for="genre" class="form-label">Main Tag</label>
            <select id="genre" name="genre" class="form-control" required>
                <option value="" disabled selected>Select a genre</option>
                <option value="Music">Music</option>
                <option value="Dance">Dance</option>
            </select>
            @error('genre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Subgenres -->
        <div class="create-event-input">
            <label for="subgenres" class="form-label">Tags</label>
            <select id="subgenres" name="subgenres[]" class="form-control" multiple required>
                <!-- Options will be dynamically populated -->
            </select>
            @error('subgenres')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

                <!-- File Upload -->
        <div class="create-event-input">
            <label for="event_files" class="form-label">Upload Media</label>
            <input type="file" id="event_files" name="event_files[]" class="form-control" multiple 
                accept="image/*,video/*">
            <small class="form-text text-muted">
                You can upload up to 3 files (images only).
            </small>
            <div id="file-error" class="text-danger" style="display: none;">
                You can upload a maximum of 3 files and only images are allowed.
            </div>
            @error('event_files')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            @error('event_files.*')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Event</button>
    </form>
</div>
@endsection