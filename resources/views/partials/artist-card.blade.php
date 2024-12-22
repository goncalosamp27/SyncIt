<a href="{{ route('artist', ['artist_id' => $artist->artist_id]) }}" class="artist-card" style="text-decoration: none; color: inherit;">
  <div class="artist-image">
    @can('isBanned', $artist->member)  
      <img src="{{ asset('profile/default_user.png') }}" alt="Artist Image">
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

  <!-- Artist Username -->
  @can('isBanned', $artist->member)  
    <div class="artist-username">Anonymous</div>
  @else
    <div class="artist-username">{{ '@' . $artist->member->username }}</div>
  @endcan
  
  <!-- Rating in stars -->
  <div class="artist-rating">
    @php
      $fullStars = floor($artist->rating); // Get the full stars
      $halfStar = $artist->rating - $fullStars >= 0.5; // Check if there's a half-star
    @endphp
    <!-- Full Stars -->
    @for ($i = 0; $i < $fullStars; $i++)
      <span class="star">&#9733;</span> <!-- Full star -->
    @endfor

    <!-- Half Star -->
    @if ($halfStar)
      <span class="star-half">&#9733;</span> <!-- Half star -->
    @endif

    <!-- Empty Stars (5 stars max) -->
    @for ($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++)
      <span class="star-empty">&#9734;</span> <!-- Empty star -->
    @endfor
</div>

</a>
