document.addEventListener('DOMContentLoaded', function () {
    const applyFiltersButton = document.getElementById('apply-filters');

    applyFiltersButton.addEventListener('click', function () {
        console.log("Button was clicked");

        // Capture the selected tags when the button is clicked
        const selectedTags = {
            dance_tag: document.querySelector('input[name="dance_tag"]:checked')?.value,
            music_tag: document.querySelector('input[name="music_tag"]:checked')?.value,
            mood_tag: document.querySelector('input[name="mood_tag"]:checked')?.value,
            setting_tag: document.querySelector('input[name="setting_tag"]:checked')?.value,
        };

        // Step 1: Fetch filtered event IDs based on selected tags
        fetch(filterEventsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ tags: selectedTags }) // Send the selected tags to filter events
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch filtered events');
            }
            return response.json();
        })
        .then(data => {
            console.log("Filtered events: ", data);

            if (data.success && data.event_ids.length > 0) {
                const eventIds = data.event_ids;
                console.log(eventIds);

                // Step 2: Fetch updated event data based on the event IDs
                fetch(updateEventsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ event_ids: eventIds }) // Send the filtered event IDs to update events
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch updated events');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Updated events: ", data);

                    if (data.success) {
                        // Step 3: Dynamically create the event cards and append to the grid
                        const eventsGrid = document.querySelector('.events-grid');
                        eventsGrid.innerHTML = ''; // Clear the current grid before adding new cards

                        // Loop through each event and generate HTML
                        data.events.forEach(event => {
                            const eventCardHTML = createEventCardHTML(event);
                            eventsGrid.innerHTML += eventCardHTML; // Append each generated card to the grid
                        });

                        // Optionally, log the events data
                        console.log(data.events);
                    } else {
                        console.error("No events found based on selected filters.");
                        const eventsGrid = document.querySelector('.events-grid');
                        eventsGrid.innerHTML = '<p>No events found for the selected filters.</p>';
                    }
                })
            } else {
                console.error("No events found based on selected filters.");
                const eventsGrid = document.querySelector('.events-grid');
                eventsGrid.innerHTML = '<p>No events found for the selected filters.</p>';
            }
        })
        .catch(error => {
            console.error("Error fetching filtered events:", error);
        });
    });
});

// Function to generate event card HTML
function createEventCardHTML(event) {
    // Check if the essential event data is defined
    const eventRoute = "event/" + event.event_id ;  // Fallback to '#' if route is undefined
    const eventImageUrl = event.event_media || '/default-image.jpg';  // Fallback to default image
    const eventName = event.event_name || 'Unknown Event';
    const eventLocation = event.location || 'Location not provided';
    const eventDate = formatEventDate(event.event_date);
    const eventCapacity = event.ticket_count || '0';
    const eventTotalCapacity = event.capacity || 'N/A';
    const eventPrice = event.price === 0 ? '<span class="event-free">FREE</span>' : `${event.price}€`;

    // Generate the event card HTML structure using event object properties
    return `
        <a href="${eventRoute}" class="event-card">
            <div class="event-image">
                <img src="${eventImageUrl}" alt="Event Image">
            </div>
            <div class="event-details">
                <h3 class="event-title">${eventName}</h3>
                <p>📍 ${eventLocation}</p>
                <p>📅 ${eventDate}</p>
                <p class="event-price-cap"> 
                    <span class="event-capacity">${eventCapacity}/${eventTotalCapacity}</span>
                    <span class="event-price">${eventPrice}</span>
                </p>
                <div class="event-card-tags">
                    ${generateTagsHTML(event.tags)}  <!-- This calls the updated tags function, which handles undefined tags -->
                </div>
            </div>
        </a>
    `;
}

// Function to format the event date
function formatEventDate(eventDate) {
    if (!eventDate) return 'Date not available';
    
    const date = new Date(eventDate);
    return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} - ${date.getHours()}:${date.getMinutes()} ${date.getHours() >= 12 ? 'PM' : 'AM'}`;
}

// Function to generate the event tags HTML
function generateTagsHTML(tags) {
    if (!tags || !Array.isArray(tags)) {
        console.warn('No tags available for this event');
        return ''; // Return an empty string if no tags are provided
    }

    return tags.slice(0, 3).map(tag => {
        return `
            <span class="tag-button" style="background: #${tag.color}; color: #fff;">
                ${tag.tag_name}
            </span>
        `;
    }).join('');
}
