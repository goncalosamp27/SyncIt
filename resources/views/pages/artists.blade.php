@extends('layouts.app')

@section('content')

{{-- Artists Grid --}}
<div id="events-grid" class="events-grid">
  @foreach ($artists as $artist)
    @include('partials.artist-card', ['artist' => $artist])
  @endforeach
</div>
@endsection