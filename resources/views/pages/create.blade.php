@extends('layouts.app')

@section('content')
<div class="create-page">
<script>
    const musicTags = @json($musicTags);
    const danceTags = @json($danceTags);
</script>

<!-- Include the JavaScript file -->
<script src="{{ asset('js/createEvent.js') }}"></script>
<div class="create-event">
    <div class="create-event-title">
        <h1>Create your own Event </h1>
    </div>
    <div class="purple-line2"></div>
</div>

<div class="create-event-form">
    <form method="POST" action="{{ route('create.store') }}" enctype="multipart/form-data">
        @csrf
        <!-- Event Name -->
        <div class="create-event-input">
            <label for="event_name" class="form-label">Event Name</label>
            <input type="text" id="event_name" name="event_name" placeholder="Enter a name for your event"
                class="form-control" value="{{ old('event_name') }}" required>
            @error('event_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Event Date -->
        <div class="create-event-input">
            <label for="event_date" class="form-label">Event Date</label>
            <input type="date" id="event_date" name="event_date" placeholder="Enter a date for your event"
                class="form-control" value="{{ old('event_date') }}" required>
            @error('event_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <!-- Event Time -->
        <div class="create-event-input">
            <label for="event_time" class="form-label">Event Time:</label>
            <input type="time" id="event_time" name="event_time"
                value="{{ old('event_time', isset($event) ? date('H:i', strtotime($event->event_date)) : '') }}"
                required class="form-control" />
            @error('event_time')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Location -->
        <div class="create-event-input">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" placeholder="Enter a location for your event"
                class="form-control" value="{{ old('location') }}" required>
            @error('location')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="create-event-input">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" placeholder="Enter a description for your event"
                class="form-control" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Capacity -->
        <div class="create-event-input">
            <label for="capacity" class="form-label">Capacity</label>
            <input type="number" id="price" name="capacity" placeholder="Enter a capacity for your event"
                class="form-control" value="{{ old('price') }}" min="0" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Price -->
        <div class="create-event-input">
            <label for="price" class="form-label">Price</label>
            <input type="number" id="price" name="price" placeholder="Enter a price for your event"
                class="form-control" value="{{ old('price') }}" min="0" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Refund -->
        <div class="create-event-input">
            <label for="refund" class="form-label">Refund (%)</label>
            <input type="number" id="refund" name="refund" placeholder="Enter a refund percentage for your event"
                class="form-control" value="{{ old('refund') }}" min="0" max="100" required>
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
                <option value="Music" {{ old('genre') == 'Music' ? 'selected' : '' }}>Music</option>
                <option value="Dance" {{ old('genre') == 'Dance' ? 'selected' : '' }}>Dance</option>
            </select>
            @error('genre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <!-- Subgenres -->
        <div class="create-event-input-inline">
            <div>
                    <label for="music-dance" id="music-dance-label" class="form-label">Type</label>
                    <select id="music-dance" name="music-dance" class="form-control" required>
                        <option value="" disabled selected>Select</option>
                        <!-- Options will be dynamically populated via JavaScript -->
                    </select>
                    @error('music-dance')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
            </div>
            <!-- Mood Dropdown -->
            <div>
                <label for="mood" class="form-label">Mood</label>
                <select id="mood" name="mood" class="form-control" required>
                    <option value="" disabled selected>Select</option>
                    @foreach ($moodTags as $tag)
                        <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
                    @endforeach
                </select>
                @error('mood')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Settings Dropdown -->
            <div>
                <label for="setting" class="form-label">Setting</label>
                <select id="setting" name="setting" class="form-control" required>
                    <option value="" disabled selected>Select</option>
                    @foreach ($settingsTags as $tag)
                        <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
                    @endforeach
                </select>
                @error('setting')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <!-- File Upload -->
    
        <div class="create-event-input">
            <label for="event_files" class="form-label">Upload Media</label>
            <input type="file" id="event_files" name="event_files">
            <div id="file-error" class="text-danger" style="display: none;">
                You can only upload 1 image.
            </div>
            @error('event_files')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>


        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Event</button>
    </form>
</div>
@include('partials.go-back')
</div>
@endsection