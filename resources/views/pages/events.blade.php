@extends('layouts.app')

@section('content')

<script src="{{ asset('js/events.js') }}"></script>
<script>
  const filterEventsUrl = @json(route('events.filter'));
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="filter-bar">
  {{-- Dance Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button" type="button">Dance</button>
    <div class="dropdown-menu">
      @foreach ($tagsDance as $tag)
      <div>
      <input type="checkbox" id="dance-tag-{{ $tag["tag_id"] }}" name="dance_tag[]" value="{{ $tag["tag_id"] }}"
        class="filter-checkbox">
      <label for="dance-tag-{{ $tag["tag_id"] }}">{{ $tag["tag_name"] }}</label>
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
      <input type="checkbox" id="music-tag-{{ $tag["tag_id"] }}" name="music_tag[]" value="{{ $tag["tag_id"] }}"
        class="filter-checkbox">
      <label for="music-tag-{{ $tag["tag_id"] }}">{{ $tag["tag_name"] }}</label>
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
      <input type="checkbox" id="mood-tag-{{ $tag["tag_id"] }}" name="mood_tag[]" value="{{ $tag["tag_id"] }}"
        class="filter-checkbox">
      <label for="mood-tag-{{ $tag["tag_id"] }}">{{ $tag["tag_name"] }}</label>
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
      <input type="checkbox" id="setting-tag-{{ $tag["tag_id"] }}" name="setting_tag[]" value="{{ $tag["tag_id"] }}"
        class="filter-checkbox">
      <label for="setting-tag-{{ $tag["tag_id"] }}">{{ $tag["tag_name"] }}</label>
      </div>
    @endforeach
    </div>
  </div>

  {{-- Apply Filters Button --}}
  <button id="apply-filters" class="filter-button" type="submit">Apply Filters</button>
</div>

{{-- Events Grid --}}
<div id="events-grid" class="events-grid">
  @foreach ($events as $event)
    @include('partials.event-card', ['event' => $event])
  @endforeach
</div>
@endsection