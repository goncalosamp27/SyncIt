@extends('layouts.app')

@section('content')
<script src="{{ asset('js/create-poll.js') }}"></script>
<script>
    const pollUrl = @json(route('poll.store', ['event_id' => $event->event_id]));
</script>
<div class="container-poll">
    <h1>Create a Poll</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" id="create-poll-form">
        @csrf
        <!-- Poll Title -->
        <div class="mb-3">
            <label for="title" class="form-label">Poll Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Poll Start Date -->
        <div class="mb-3">
            <label for="start_date" class="form-label">Poll Start Date</label>
            <input type="datetime-local" name="start_date" id="start_date" class="form-control"
                value="{{ old('start_date') }}" required>
            @error('start_date')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Poll End Date -->
        <div class="mb-3">
            <label for="end_date" class="form-label">Poll End Date</label>
            <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                value="{{ old('end_date') }}" required>
            @error('end_date')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Poll Options -->
        <div id="poll-options">
            <label for="options" class="form-label">Poll Options</label>
            <div id="first-option" class="mb-2">
                <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
            </div>
            <div class="mb-2">
                <input type="text" name="options[]" class="form-control" placeholder="Option 2" required>
            </div>
        </div>

        <!-- Add Another Option Button -->
        <button type="button" class="btn btn-secondary" onclick="addOption()">Add Option</button>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary mt-3">Create Poll</button>
    </form>
</div>

@endsection