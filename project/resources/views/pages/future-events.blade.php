
<!-- 
@extends('layouts.app')

@section('content')

<div class="filter-bar">
  {{-- Dance Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button">Dance</button>
    <div class="dropdown-menu">
      @foreach ($tagsDance as $tag)
        <div>
          <input type="checkbox" id="dance-tag-{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" class="filter-checkbox">
          <label for="dance-tag-{{ $tag->id }}">{{ $tag->tag_name }}</label>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Music Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button">Music</button>
    <div class="dropdown-menu">
      @foreach ($tagsMusic as $tag)
        <div>
          <input type="checkbox" id="music-tag-{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" class="filter-checkbox">
          <label for="music-tag-{{ $tag->id }}">{{ $tag->tag_name }}</label>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Mood Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button">Mood</button>
    <div class="dropdown-menu">
      @foreach ($tagsMood as $tag)
        <div>
          <input type="checkbox" id="mood-tag-{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" class="filter-checkbox">
          <label for="mood-tag-{{ $tag->id }}">{{ $tag->tag_name }}</label>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Settings Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button">Setting</button>
    <div class="dropdown-menu">
      @foreach ($tagsSettings as $tag)
        <div>
          <input type="checkbox" id="setting-tag-{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" class="filter-checkbox">
          <label for="setting-tag-{{ $tag->id }}">{{ $tag->tag_name }}</label>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Apply Filters Button --}}
  <button id="apply-filters" class="filter-button">Apply Filters</button>
</div>

{{-- Events Grid --}}
<div class="events-grid">
  @if ($events->isEmpty())
    <p>No events found for the selected tags.</p>
  @else
    @foreach ($futureevents as $event)
      @include('partials.event-card', ['event' => $event])
    @endforeach
  @endif
</div>

@endsection
-->