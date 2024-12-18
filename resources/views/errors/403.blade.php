@extends('layouts.app')

@section('content')

<div class="error-page">
    <h1>403</h1>
    <h2>Forbidden :(</h2>
    <p>We're sorry, but the access to this resource was denied.</p>
    <div class ="errr-btn"> <a href="{{ url()->previous() }}" class="go-back-btn">Go Back</a></div>
</div>

@endsection