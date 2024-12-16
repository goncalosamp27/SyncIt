<div class="ticket-card">
    <div class="ticket-data">
        <span class="ticket-owner">{{ $member->display_name }}'s</span> ticket to:
        <span class="ticket-id">Event #{{ $ticket->event->event_id }}</span>
        <span class="ticket-id">Ticket #{{ $ticket->ticket_id }}</span>
        <span class="ticket-event-name">{{ $ticket->event->event_name }}</span>
        <div class="ticket-meta">
            <span class="ticket-price">Price: {{ $ticket->event->price }}€</span>
            <span class="ticket-refund">Refund: {{ $ticket->event->refund }}%</span>
            <span class= "ticket-refund">Refund Value: {{$ticket->event->price * $ticket->event->refund / 100}}
        </div>

        @php
            $now = date('Y-m-d H:i:s'); // Current date and time
            $nowPlus24Hours = date('Y-m-d H:i:s', strtotime('+24 hours')); // 24 hours from now
        @endphp

        @if ($ticket->event->event_date >= $nowPlus24Hours)
            <!-- Event is more than 24 hours away, refund available -->
            <form action="{{ route('refund-ticket', ['ticket_id' => $ticket->ticket_id]) }}" method="POST">
                @csrf
                <button class="refund-button" type="submit">Click to refund</button>
            </form>  
        @elseif ($ticket->event->event_date < $now)
            <button class="no-refund-button-attended" disabled>Event Attended</button>
        @else
            <button class="no-refund-button" disabled>Refund unavailable</button>
        @endif
    </div>

    <div class="ticket-event-card">
         @include('partials.event-card', ['event' => $ticket->event])  
    </div>
</div>
