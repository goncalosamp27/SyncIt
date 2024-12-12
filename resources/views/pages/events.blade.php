@extends('layouts.app')

@section('content')

<script src="{{ asset('js/events.js') }}"></script>
<script>
  const filterEventsUrl = @json(route('events.filter'));
  const updateEventsUrl = @json(route('future-events-update'));
  const getEventCardsUrl = @json(route('get-cards'));
</script>

<div class="filter-bar">
  {{-- Dance Dropdown --}}
  <div class="dropdown">
    <button class="dropdown-button" type="button">Dance</button>
    <div class="dropdown-menu">
      @foreach ($tagsDance as $tag)
      <div>
      <input type="checkbox" id="dance-tag-{{ $tag->tag_id }}" name="dance_tag[]" value="{{ $tag->tag_id }}"
        class="filter-checkbox">
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
      <input type="checkbox" id="music-tag-{{ $tag->tag_id }}" name="music_tag[]" value="{{ $tag->tag_id }}"
        class="filter-checkbox">
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
      <input type="checkbox" id="mood-tag-{{ $tag->tag_id }}" name="mood_tag[]" value="{{ $tag->tag_id }}"
        class="filter-checkbox">
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
      <input type="checkbox" id="setting-tag-{{ $tag->tag_id }}" name="setting_tag[]" value="{{ $tag->tag_id }}"
        class="filter-checkbox">
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
      @if ($event->event_status !== 'Cancelled')
          @include('partials.event-card', ['event' => $event])
      @endif
  @endforeach
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dropdownButtons = document.querySelectorAll('.dropdown-button');
    const applyFiltersButton = document.getElementById('apply-filters');

    // Toggle dropdown visibility
    dropdownButtons.forEach(button => {
      button.addEventListener('click', function () {
        const menu = this.nextElementSibling;
        menu.classList.toggle('show');
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (event) {
      if (!event.target.matches('.dropdown-button') && !event.target.closest('.dropdown-menu')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
      }
    });

    // Apply Filters Button
    applyFiltersButton.addEventListener('click', function () {
      console.log("Button was clicked");

      // Function to get selected values
      const getSelectedValues = (name) => {
        return Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(input => input.value);
      };

      const selectedTags = {
        dance_tags: getSelectedValues('dance_tag'),
        music_tags: getSelectedValues('music_tag'),
        mood_tags: getSelectedValues('mood_tag'),
        setting_tags: getSelectedValues('setting_tag'),
      };

      console.log('Selected Tags:', selectedTags);

      // TODO: Send `selectedTags` to the backend via an AJAX request or form submission
    });
  });
</script>

@endsection