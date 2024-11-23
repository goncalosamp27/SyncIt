<div class="artist-card">
  <div class="artist-image">
    <img src=" {{ asset('storage/profiles/' . $artist->member->profile_pic_url) }}" alt="Artist Image">
  </div>
  <div class="artist-name"> {{ $artist->member->display_name }}</div>
</div>
