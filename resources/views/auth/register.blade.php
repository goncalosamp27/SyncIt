@extends('layouts.app')

@section('content')
<div class="registration-wrapper">
  <form class="registration-form" method="POST" action="{{ route('register') }}">
    {{ csrf_field() }}

    <!-- Username -->
    <label for="username">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username') }}" required>
    @if ($errors->has('username'))
    <span class="error">
      {{ $errors->first('username') }}
    </span>
  @endif

    <!-- Display Name -->
    <label for="display_name">Display Name</label>
    <input id="display_name" type="text" name="display_name" value="{{ old('display_name') }}" required>
    @if ($errors->has('display_name'))
    <span class="error">
      {{ $errors->first('display_name') }}
    </span>
  @endif

    <!-- Email -->
    <label for="email">E-Mail Address</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    @if ($errors->has('email'))
    <span class="error">
      {{ $errors->first('email') }}
    </span>
  @endif

    <!-- Password -->
    <label for="password">Password</label>
    <input id="password" type="password" name="password" required>
    @if ($errors->has('password'))
    <span class="error">
      {{ $errors->first('password') }}
    </span>
  @endif

    <!-- Confirm Password -->
    <label for="password_confirmation">Confirm Password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" required>
    @if ($errors->has('password_confirmation'))
    <span class="error">
      {{ $errors->first('password_confirmation') }}
    </span>
  @endif


    <!-- Bio -->
    <label for="bio">Bio (Optional)</label>
    <textarea id="bio" name="bio">{{ old('bio') }}</textarea>
    @if ($errors->has('bio'))
    <span class="error">
      {{ $errors->first('bio') }}
    </span>
  @endif

    <button type="submit">
      Register
    </button>

    <a class="button button-outline" href="{{ route('login') }}">Login</a>
  </form>
</div>
@endsection