@extends('layouts.app')

@section('content')

<div class="filter-bar">
  <div class="dropdown">
    <button class="dropdown-button">Dance</button>
    <div class="dropdown-menu">
      @foreach ($tagsDance as $tag)
        <span>{{ $tag->tag_name }}</span>
      @endforeach
    </div>
  </div>
  <div class="dropdown">
    <button class="dropdown-button">Music</button>
    <div class="dropdown-menu">
      @foreach ($tagsMusic as $tag)
        <span>{{ $tag->tag_name }}</span>
      @endforeach
    </div>
  </div>
  <div class="dropdown">
    <button class="dropdown-button">Mood</button>
    <div class="dropdown-menu">
      @foreach ($tagsMood as $tag)
        <span>{{ $tag->tag_name }}</span>
      @endforeach
    </div>
  </div>
  <div class="dropdown">
    <button class="dropdown-button">Setting</button>
    <div class="dropdown-menu">
      @foreach ($tagsSettings as $tag)
        <span>{{ $tag->tag_name }}</span>
      @endforeach
    </div>
  </div>
</div>


<div class="events-grid">
    @foreach ($events as $event)
        @include('partials.event-card', ['event' => $event])
    @endforeach
</div>

@endsection
