@extends('layouts.app')

@section('content')

<div class="events-search-bar">
  <form method="GET" action="{{ route('artists.search') }}" class="search-bar">
      <button type="submit" class="search-button">🔍</button>

      <input type="text" name="search" placeholder="Search for names or usernames" value="{{ request('search') }}">
      <button class="search-btn" type="submit">Search</button>
  </form>
</div>

{{-- Artists Grid --}}
<div id="events-grid" class="events-grid">
  @foreach ($artists as $artist)
    @include('partials.artist-card', ['artist' => $artist])
  @endforeach
</div>
@endsection