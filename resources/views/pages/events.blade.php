@extends('layouts.app')

@section('content')

<div class="filter-bar">
  <div class="dropdown">
    <button class="dropdown-button">Dance</button>
    <div class="dropdown-menu">
      <span>Ballet</span>
      <span>Hip Hop</span>
      <span>Salsa</span>
      <span>Tango</span>
      <span>Contemporary</span>
      <span>Jazz Dance</span>
      <span>Tap</span>
      <span>Breakdance</span>
      <span>Waltz</span>
      <span>Folk Dance</span>
    </div>
  </div>
  <div class="dropdown">
    <button class="dropdown-button">Music</button>
    <div class="dropdown-menu">
      <span>Rock</span>
      <span>Pop</span>
      <span>Jazz</span>
      <span>Classical</span>
      <span>Hip Hop</span>
      <span>Country</span>
      <span>Blues</span>
      <span>Electronic</span>
      <span>R&B</span>
      <span>Metal</span>
    </div>
  </div>
  <div class="dropdown">
    <button class="dropdown-button">All</button>
    <div class="dropdown-menu">
      <span>All Events</span>
    </div>
  </div>
</div>


<div class="events-grid">
    @foreach ($events as $event)
        @include('partials.event-card', ['event' => $event])
    @endforeach
</div>


@endsection