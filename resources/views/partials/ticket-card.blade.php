<div class="ticket-card">
    <div class="ticket-data">
        <span class="ticket-owner">{{ $ticket->user->name ?? 'Unknown User' }}'s</span> ticket to:
        <span class="ticket-id">#{{ $ticket->id }}</span>
        <span class="ticket-event-name">{{ $ticket->event->title ?? 'No Event Assigned' }}</span>
        <div class="ticket-meta">
            <span class="ticket-bought-date">Date: {{ $ticket->created_at->format('Y-m-d') }}</span>
            <span class="ticket-price">Price: ${{ $ticket->price }}</span>
            <span class="ticket-refund">Refund: {{ $ticket->refund_percent }}%</span>
        </div>
        <button class="refund-button">Click to refund</button>
    </div>

    <div class="ticket-event-card">
         @include('partials.event-card', ['event' => $ticket->event])  
    </div>
</div>
