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
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    @include('partials.header')

    <div class="content">
        @yield('content')
    </div>

    @include('partials.footer')
</body>
</html>
