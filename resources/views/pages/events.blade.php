@extends('layouts.app')

@section('content')

<script src="{{ asset('js/events.js') }}"></script>
<script>
  const filterEventsUrl = @json(route('events.filter'));
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="filter-bar">
  {{-- Public/Private Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button" type="button">Type</button>
    <div class="dropdown-menu">
      <div class="dropdown-item">
        <input type="checkbox" id="event-type-public" name="event_type" value="public" class="filter-radio">
        <label for="event-type-public">Public</label>
      </div>
      <div class="dropdown-item">
        <input type="checkbox" id="event-type-private" name="event_type" value="private" class="filter-radio">
        <label for="event-type-private">Private</label>
      </div>
    </div>
  </div>
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
  <button id="apply-filters" class="filter-button" type="submit">Apply</button>
  {{-- Reset Filters Button --}}
  <button id="reset-filters" class="filter-button" type="button">Reset</button>
</div>
<div class="events-search-bar">
  <form method="GET" action="{{ route('events.search') }}" class="search-bar">
      <button type="submit" class="search-button">🔍</button>

      <input type="text" name="search" placeholder="Search for events, locations or artists..." value="{{ request('search') }}">
      <button class="search-btn" type="submit">Search</button>
  </form>
</div>

{{-- Events Grid --}}
<div id="events-grid" class="events-grid">
  @foreach ($events as $event)
    @include('partials.event-card', ['event' => $event])
  @endforeach
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const searchTerm = urlParams.get('search'); // If search exists, it'll be assigned to searchTerm, otherwise null

    // Check if there is no search parameter in the URL
    if (!searchTerm) {
      let isLoading = false; // Prevent multiple requests
      let currentPage = 1; // Start at page 1

      const addEvents = (html) => {
      const loader = document.getElementById("events-grid");
      loader.insertAdjacentHTML('beforeend', html); // Append HTML directly
  };

  const fetchEvents = async (pageIndex) => {
      try {
          isLoading = true;

          const response = await fetch(`/load-more-events?page=${pageIndex}`);
          const data = await response.json();

          if (data.html) {
              addEvents(data.html); // Insert rendered HTML into the grid
              currentPage = pageIndex;
          }

          if (!data.next_page) {
              window.removeEventListener("scroll", debouncedHandleInfiniteScroll);
          }
      } catch (error) {
          console.error('Error fetching events:', error);
      } finally {
          isLoading = false;
      }
  };
      const handleInfiniteScroll = () => {
          const threshold = 200; // Trigger 200px before reaching the bottom
          const endOfPage = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - threshold;

          if (endOfPage && !isLoading) {
              fetchEvents(currentPage + 1);
          }
      };

      // Debounce to limit how often scroll handler runs
      const debounce = (func, delay = 200) => {
          let timeout;
          return (...args) => {
              clearTimeout(timeout);
              timeout = setTimeout(() => func(...args), delay);
          };
      };

      const debouncedHandleInfiniteScroll = debounce(handleInfiniteScroll, 200);

      // Attach debounced handler to the scroll event
      window.addEventListener("scroll", debouncedHandleInfiniteScroll);

      // Initial fetch
      fetchEvents(currentPage);
    }

</script>

@endsection