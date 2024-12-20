@extends('layouts.app')

@section('content')
<div class="forgot-password-wrapper">
    <form class="forgot-password-form" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <h2>Forgot Password</h2>
        <p>Enter your email address, and we will send you a link to reset your password.</p>

        <label for="email">E-mail Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
            placeholder="e.g: example@example.com">
        @if ($errors->has('email'))
            <span class="error" id="email-error" style="color: red; transition: opacity 0.5s;">
                {{ $errors->first('email') }}
            </span>
        @endif

        <button type="submit">Send Password Reset Link</button>

        @if (session('status'))
            <p class="success">
                {{ session('status') }}
            </p>
        @endif
    </form>
</div>
@endsection