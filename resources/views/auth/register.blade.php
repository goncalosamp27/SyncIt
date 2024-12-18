@extends('layouts.app')

@section('content')
<div class="registration-wrapper">
  <form class="registration-form" method="POST" action="{{ route('post.register') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <!-- Username -->
    <label for="username">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username') }}" required placeholder="e.g: username123">
    @if ($errors->has('username'))
    <span class="error">
      {{ $errors->first('username') }}
    </span>
    @endif

    <!-- Display Name -->
    <label for="display_name">Display Name</label>
    <input id="display_name" type="text" name="display_name" value="{{ old('display_name') }}" required placeholder="e.g: John Doe">
    @if ($errors->has('display_name'))
    <span class="error">
      {{ $errors->first('display_name') }}
    </span>
    @endif

    <!-- Email -->
    <label for="email">E-Mail Address</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="e.g: example@example.com">
    @if ($errors->has('email'))
    <span class="error">
      {{ $errors->first('email') }}
    </span>
    @endif

    <!-- Password -->
    <label for="password">Password</label>
    <input id="password" type="password" name="password" required placeholder="e.g: YourSecurePassword123!">
    @if ($errors->has('password'))
    <span class="error">
      {{ $errors->first('password') }}
    </span>
    @endif

    <!-- Confirm Password -->
    <label for="password_confirmation">Confirm Password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter your password">
    @if ($errors->has('password_confirmation'))
    <span class="error">
      {{ $errors->first('password_confirmation') }}
    </span>
    @endif

    <!-- File Upload -->
    <div class="create-event-input">
        <label for="profile_pic_url" class="form-label">Upload Media (optional)</label>
        <input type="file" id="profile_pic_url" name="profile_pic_url">
        <small class="form-text text-muted">
            You can upload up to 1 image.
        </small>
        <div id="file-error" class="text-danger" style="display: none;">
            You can only upload 1 image.
        </div>
        @error('profile_pic_url')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Bio -->
    <label for="bio">Bio (optional)</label>
    <textarea id="bio" name="bio" placeholder="e.g: I'm John and I love dancing to HipHop beats. I'm interested in Rap Concerts.">{{ old('bio') }}</textarea>
    @if ($errors->has('bio'))
    <span class="error">
      {{ $errors->first('bio') }}
    </span>
    @endif

    <button type="submit">
      Register
    </button>

    @if(!auth()->guard('admin')->check())
    <a class="button button-outline" href="{{ route('login') }}">Login</a>
    @endif

  </form>
</div>
@endsection
