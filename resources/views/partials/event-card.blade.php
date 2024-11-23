<div class="event-card">
    <div class="event-image">
        <img src="{{ asset('storage/events/' . $event->event_media) }}" alt="Event Image">
    </div>
    <div class="event-details">
        <h3 class="event-title">{{ $event->event_name }}</h3>
        <p>📍 {{ $event->location }} </p>
        <p>📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y - h:i A') }}</p>
        <p class="event-price-cap"> 
            <span class="event-capacity"> {{ $event->ticket_count }}/{{ $event->capacity }} </span>
            <span class="event-price">
                {{ $event->price == 0 ? '<span class="event-free">FREE</span>' : $event->price . '€' }}
            </span>
        <div class="event-card-tags">
            <span class="tag dance">Dance</span>
            <span class="tag">Tag 1</span>
            <span class="tag">Tag 2</span>
        </div>
    </div>
</div>