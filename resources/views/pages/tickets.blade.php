@extends('layouts.app')

@section('content')
    <div class="tickets-div">
        @if (session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="error">
                {{ session('error') }}
            </div>
        @endif

        <div class="tickets-title">
            Showing Tickets for upcoming shows:
        </div>
        <div class="new-purple-line"></div>
        <div class="tickets-list">
            @if ($tickets->isEmpty())
                <p class="no-tickets">You do not own any tickets for upcoming events.</p>
            @else
                @foreach ($tickets as $ticket_)
                    @include('partials.ticket-card', ['ticket' => $ticket_])
                @endforeach
            @endif
        </div>

        <!-- Pagination Links -->
        <div class="pagination-container">
            {{ $tickets->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
