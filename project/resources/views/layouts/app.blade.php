<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="auth" content="{{ auth()->check() ? 'true' : 'false' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SyncIt!</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> 
    <link rel="icon" href="{{ asset('storage/syncit2.svg')}}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    @include('partials.header')

    <div id="confirmationModal3" class="new-modal" style="display: none;">
        <div class="new-modal-content">
            <span class="close-btn" onclick="closeModal(3)">×</span>
            <div class = "delete-form">
                <h2>Delete Account</h2>
                <p class = "delete-text">To delete your account, please confirm your password and type "I want to delete my account" in the box below.</p>
                <form method="POST" action="{{ route('account.delete') }}">
                    @csrf
                    <!--
                    <div class="form-group2">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required autocomplete="new-password">
                    </div>
-->
                    <div class="form-group2">
                        <label for="confirmation">Confirmation Message:</label>
                        <input type="text" name="confirmation" id="confirmation" placeholder="I want to delete my account" required>
                    </div>
                    
                    <button type="submit" class="delete-btn">Delete Account</button>
                </form>
            </div>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    @include('partials.footer')
</body>
</html>
