@extends('layouts.app')

@section('content')

<script src="{{ asset('js/events.js') }}"></script>
<script>
    const filterEventsUrl = @json(route('events.filter'));
</script>
<script>
    const updateEventsUrl = @json(route('future-events-update'));
</script>
<script>
    const getEventCardsUrl = @json(route('get-cards'));
</script>
<div class="filter-bar">
    {{-- Dance Dropdown --}}
    <div class="dropdown">
      <button class="dropdown-button" type="button">Dance</button>
      <div class="dropdown-menu">
        @foreach ($tagsDance as $tag)
      <div>
        <input type="radio" id="dance-tag-{{ $tag->tag_id }}" name="dance_tag" value="{{ $tag->tag_id }}"
        class="filter-radio">
        <label for="dance-tag-{{ $tag->tag_id }}">{{ $tag->tag_name }}</label>
      </div>
    @endforeach
      </div>
    </div>

    {{-- Music Dropdown --}}
    <div class="dropdown">
      <button class="dropdown-button" type="button">Music</button>
      <div class="dropdown-menu">
        @foreach ($tagsMusic as $tag)
      <div>
        <input type="radio" id="music-tag-{{ $tag->tag_id }}" name="music_tag" value="{{ $tag->tag_id }}"
        class="filter-radio">
        <label for="music-tag-{{ $tag->tag_id }}">{{ $tag->tag_name }}</label>
      </div>
    @endforeach
      </div>
    </div>

    {{-- Mood Dropdown --}}
    <div class="dropdown">
      <button class="dropdown-button" type="button">Mood</button>
      <div class="dropdown-menu">
        @foreach ($tagsMood as $tag)
      <div>
        <input type="radio" id="mood-tag-{{ $tag->tag_id }}" name="mood_tag" value="{{ $tag->tag_id }}" class="filter-radio">
        <label for="mood-tag-{{ $tag->tag_id }}">{{ $tag->tag_name }}</label>
      </div>
    @endforeach
      </div>
    </div>

    {{-- Settings Dropdown --}}
    <div class="dropdown">
      <button class="dropdown-button" type="button">Setting</button>
      <div class="dropdown-menu">
        @foreach ($tagsSettings as $tag)
      <div>
        <input type="radio" id="setting-tag-{{ $tag->tag_id }}" name="setting_tag" value="{{ $tag->tag_id }}"
        class="filter-radio">
        <label for="setting-tag-{{ $tag->tag_id }}">{{ $tag->tag_name }}</label>
      </div>
    @endforeach
      </div>
    </div>

    {{-- Apply Filters Button --}}
    <button id="apply-filters" class="filter-button" type="submit">Apply Filters</button>
</div>

{{-- Events Grid --}}
<div class="events-grid">
    @foreach ($events as $event)
    @include('partials.event-card', ['event' => $event])
  @endforeach
</div>

@endsection