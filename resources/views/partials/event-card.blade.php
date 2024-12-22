<a href="{{ route('event', ['event_id' => $event["event_id"]]) }}" class="event-card">
    <div class="event-image">
        <img src="{{ asset('storage/event/' . $event["event_media"]) }}" alt="Event Image">

    </div>
    <div class="event-details">

        @if ($event["event_status"] === 'Cancelled')
            <h3 class="event-title2">[Cancelled] - <span
                    style="text-decoration: line-through;">{{ $event["event_name"]}}</span></h3>
        @else
            <h3 class="event-title">{{ $event["event_name"] }}</h3>
        @endif

        <p>📍 {{ $event["location"] }} </p>
        <p>📅 {{ date('d/m/Y - h:i A', strtotime($event["event_date"])) }}</p>
        <p class="event-price-cap">
            <span class="event-price">
                @if ($event["price"] == 0)
                    <span class="event-free">FREE</span>
                @else
                    {{ $event["price"] }}€
                @endif
            </span>
        <div class="event-card-tags">
            @foreach ($event["tags"]->take(3) as $tag)
                <span class="tag-button" style="background: #{{ $tag->color }};">
                    {{ $tag["tag_name"] }}</span>
            @endforeach
        </div>
    </div>
</a>