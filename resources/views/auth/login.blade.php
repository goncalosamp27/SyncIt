@extends('layouts.app')

@section('content')
<div class="login-wrapper">
    <form class="login-form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <label for="login">E-mail/Username</label>
        <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
            placeholder="e.g: example@example.com or username123">
        @if ($errors->has('login'))
            <span class="error" style="color: red;">
              {{ $errors->first('login') }}
            </span>
        @endif

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required placeholder="e.g: YourSecurePassword123!">
        @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
        @endif

        <button type="submit">Login</button>
        <a class="button button-outline" href="{{ route('register') }}">Register</a>
        @if (session('success'))
            <p class="success">
                {{ session('success') }}
            </p>
        @endif
        <!-- Reset Password link -->
        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
        @if (session('success'))
            <p class="success">
                {{ session('success') }}
        @endif
    </form>
</div>
@endsection