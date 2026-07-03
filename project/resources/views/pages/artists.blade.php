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

<script>
    // Get the search parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchTerm = urlParams.get('search'); // If search exists, it'll be assigned to searchTerm, otherwise null

    // Check if there is no search parameter in the URL
    if (!searchTerm) {
        let isLoading = false; // Prevent multiple requests
        let currentPage = 1; // Start at page 1

        const addArtists = (html) => {
            const loader = document.getElementById("events-grid");
            loader.insertAdjacentHTML('beforeend', html); // Append rendered HTML to the grid
        };

        const fetchArtists = async (pageIndex) => {
            try {
                isLoading = true;

                const response = await fetch(`/load-more-artists?page=${pageIndex}`);
                const data = await response.json();

                if (data.html) {
                    addArtists(data.html); // Insert rendered HTML into the grid
                    currentPage = pageIndex;
                }

                if (!data.next_page) {
                    window.removeEventListener("scroll", debouncedHandleInfiniteScroll);
                }
            } catch (error) {
                console.error('Error fetching artists:', error);
            } finally {
                isLoading = false;
            }
        };

        const handleInfiniteScroll = () => {
            const threshold = 200; // Trigger 200px before reaching the bottom
            const endOfPage = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - threshold;

            if (endOfPage && !isLoading) {
                fetchArtists(currentPage + 1);
            }
        };

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
        fetchArtists(currentPage);
    }
</script>


@endsection