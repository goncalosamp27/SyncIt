@extends('layouts.app')

@section('content')
    <div class="edit-page">
        <h1>Edit Event</h1>
        <form action="{{ route('edit.event', ['event_id' => $event->event_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- For updating -->

            <!-- Event Name -->
            <div class="form-group">
                <label for="event_name">Event Name:</label>
                <input 
                    type="text" 
                    id="event_name" 
                    name="event_name" 
                    value="{{ $event->event_name }}" 
                    required 
                />
            </div>

            <!-- Event Date -->
            <div class="form-group">
                <label for="event_date">Event Date:</label>
                <input 
                    type="date" 
                    id="event_date" 
                    name="event_date" 
                    value="{{ date('Y-m-d', strtotime($event->event_date)) }}"
                    required 
                />
            </div>


            <!-- Event Time -->
            <div class="form-group">
                <label for="event_time">Event Time:</label>
                <input 
                    type="time" 
                    id="event_time" 
                    name="event_time" 
                    value="{{ date('H:i', strtotime($event->event_date)) }}"
                    required 
                />
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="location">Location:</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    value="{{ $event->location }}" 
                    required 
                />
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea 
                    id="description" 
                    name="description" 
                    required>{{ $event->description }}</textarea>
            </div>

            <!-- Refund Percentage -->
            <div class="form-group">
                <label for="refund">Refund Percentage:</label>
                <input 
                    type="number" 
                    id="refund" 
                    name="refund" 
                    value="{{ $event->refund }}" 
                    min="0" 
                    max="100" 
                    required 
                />
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price:</label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    value="{{ $event->price }}" 
                    min="0" 
                    required 
                />
            </div>

            <!-- Type of Event -->
            <div class="form-group">
                <label for="type_of_event">Type of Event:</label>
                <select id="type_of_event" name="type_of_event" required>
                    <option value="Public" {{ $event->type_of_event == 'Public' ? 'selected' : '' }}>Public</option>
                    <option value="Private" {{ $event->type_of_event == 'Private' ? 'selected' : '' }}>Private</option>
                </select>
            </div>

            <!-- Rating -->
            <div class="form-group">
                <label for="rating">Rating (0 to 5):</label>
                <input 
                    type="number" 
                    id="rating" 
                    name="rating" 
                    value="{{ $event->rating }}" 
                    min="0" 
                    max="5" 
                    step="0.1" 
                    required 
                />
            </div>

            <!-- Capacity -->
            <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input 
                    type="number" 
                    id="capacity" 
                    name="capacity" 
                    value="{{ $event->capacity }}" 
                    min="10" 
                    required 
                />
            </div>

            <!-- Event Media -->
            <div class="form-group">
                <label for="event_media">Event Media:</label>
                <input 
                    type="file" 
                    id="event_media" 
                    name="event_media" 
                />
            </div>

            <!-- Submit -->
            <button type="submit" class="save-button">Save Changes</button>

            <!-- Discard Changes Button -->
            <a href="{{ route('event', ['event_id' => $event->event_id]) }}" class="discard-button">Discard Changes</a>

        </form>
        @include('partials.go-back')
    </div>
@endsection
