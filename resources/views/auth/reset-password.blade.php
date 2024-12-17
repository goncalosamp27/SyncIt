@extends('layouts.app')

@section('content')
<div class="forgot-password-wrapper">
    <form class="forgot-password-form" method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Include the token in a hidden input field -->
        <input type="hidden" name="token" value="{{ request('token') }}">
        <input type="hidden" name="email" value="{{ request('email') }}">

        <h2>Reset Password</h2>

        <!-- New Password Input -->
        <label for="password">New Password</label>
        <input id="password" type="password" name="password" required placeholder="Enter new password">
        @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
        @endif

        <!-- Confirm Password Input -->
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm your new password">
        @if ($errors->has('password_confirmation'))
            <span class="error">
                {{ $errors->first('password_confirmation') }}
            </span>
        @endif

        <!-- Submit Button -->
        <button type="submit">Reset Password</button>
    </form>
</div>
@endsection
