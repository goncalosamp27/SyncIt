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
            <span class="event-capacity"> {{ 1 }}/{{ $event["capacity"] }} </span>
            <span class="event-price">
                @if ($event["price"] == 0)
                    <span class="event-free">FREE</span>
                @else
                    {{ $event["price"] }}€
                @endif
            </span>
        <div class="event-card-tags">
            @foreach ($event["tags"]->take(3) as $tag)
                <span class="tag-button" style="
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
                    {{ $tag["tag_name"] }}</span>
            @endforeach
        </div>
    </div>
</a>