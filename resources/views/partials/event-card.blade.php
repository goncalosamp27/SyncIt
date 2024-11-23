<a href="{{ url('event/' . $event->event_id) }}" class="event-card">
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
            @foreach ($event->tags->take(3) as $tag)
                <span class="tag-button"
                style="
                        background: #{{ $tag->color }};
                        color: #fff;
                        border-radius: 12px;
                        padding: 8px 16px;
                        display: inline-block;
                        font-weight: bold;
                        font-size: 14px;
                        text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.2);
                        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                        ">
                {{ $tag->tag_name }}</span>
            @endforeach
        </div>
    </div>
</a>