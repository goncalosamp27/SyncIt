<div class="ticket-card">
    <div class="ticket-data">
        <span class="ticket-owner">{{ $member->display_name }}'s</span> ticket to:
        <span class="ticket-id">Event #{{ $ticket->event->event_id }}</span>
        <span class="ticket-event-name">{{ $ticket->event->event_name }}</span>
        <div class="ticket-meta">
            {{--<span class="ticket-bought-date">Date: {{ $ticket->ticket_date->format('Y-m-d') }}</span>--}}
            <span class="ticket-price">Price: {{ $ticket->event->price }}€</span>
            <span class="ticket-refund">Refund: {{ $ticket->event->refund }}%</span>
        </div>
        <form action="{{ route('refund-ticket') }}" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket -> ticket_id }}">
            <button class="refund-button" type="submit">
                Click to refund
            </button>
        </form>    
    </div>

    <div class="ticket-event-card">
         @include('partials.event-card', ['event' => $ticket->event])  
    </div>
</div>
