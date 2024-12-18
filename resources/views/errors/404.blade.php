@extends('layouts.app')

@section('content')

<div class="error-page">
    <h1>404</h1>
    <h2>Page Not Found :(</h2>
    <p>We're sorry, but the page you were looking for doesn't exist.</p>
    <div class ="errr-btn"> <a href="{{ url()->previous() }}" class="go-back-btn">Go Back</a></div>
</div>

@endsection