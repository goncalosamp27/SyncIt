@extends('layouts.app')

@section('content')
<script>
    const resetPasswordUrl = @json(route('password.reset.submit'));
</script>
<script src="{{ asset('js/reset-password.js') }}" defer></script>

<div class="form-container reset-password">
    <div class="form-header">
        <h2>Reset Password</h2>
    </div>
    <form class="form-body" method="POST">
        @csrf

        <!-- Include the token in a hidden input field -->
        <input type="hidden" name="token" value="{{ request('token') }}">
        <input type="hidden" name="email" value="{{ request('email') }}">

        <!-- New Password Input -->
        <div class="form-group">
            <label for="password">New Password</label>
            <input id="new_password" type="password" name="password" required placeholder="Enter new password">
            @if ($errors->has('password'))
                <span class="error" style="color: red;">
                    {{ $errors->first('password') }}
                </span>
            @endif
        </div>

        <!-- Confirm Password Input -->
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                placeholder="Confirm your new password">
            @if ($errors->has('password_confirmation'))
                <span class="error" style="color: red;">
                    {{ $errors->first('password_confirmation') }}
                </span>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button type="submit" class="submit-btn">Reset Password</button>
        </div>
    </form>
</div>
@endsection