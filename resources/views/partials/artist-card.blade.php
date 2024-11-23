<a href="{{ route('artist', ['artist_id' => $artist->artist_id]) }}" class="artist-card" style="text-decoration: none; color: inherit;">
  <div class="artist-image">
    <img src="{{ asset('storage/profiles/' . $artist->member->profile_pic_url) }}" alt="Artist Image">
  </div>
  <div class="artist-name">{{ $artist->member->display_name }}</div>
</a>
