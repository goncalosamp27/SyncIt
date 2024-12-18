<a href="{{ route('artist', ['artist_id' => $artist->artist_id]) }}" class="artist-card" style="text-decoration: none; color: inherit;">
  <div class="artist-image">
    @can('isBanned', $artist->member)  
      <img src="{{ asset('storage/profile/default_user.png') }}" alt="Artist Image">
    @else
      <img src="{{ $artist->member->getProfileImage() }}" alt="Artist Image">
    @endcan 
  </div>
  <div class="artist-name">
    @can('isBanned', $artist->member)  
      Anonymous
    @else
      {{ $artist->member->display_name }}
    @endcan   
  </div>
</a>
