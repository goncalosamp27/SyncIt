document.addEventListener('DOMContentLoaded', function () {
    const applyFiltersButton = document.getElementById('apply-filters');

    applyFiltersButton.addEventListener('click', function () {
        console.log("Button was clicked");

        // Utility function to get selected values for a given name
        const getSelectedValues = (name) => {
            return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(input => input.value);
        };

        // Collect selected tags for each category
        const selectedTags = {
            dance_tags: getSelectedValues('dance_tag'),
            music_tags: getSelectedValues('music_tag'),
            mood_tags: getSelectedValues('mood_tag'),
            setting_tags: getSelectedValues('setting_tag'),
        };

        console.log('Selected Tags:', selectedTags);

        // Step 1: Fetch filtered event IDs based on selected tags
        fetch(filterEventsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(selectedTags) // Send the selected tags to the server
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch filtered events');
                }
                return response.json();
            })
            .then(data => {
                console.log("Filtered events: ", data);

                if (data.success && data.event_ids && data.event_ids.length > 0) {
                    const eventIds = data.event_ids;
                    console.log("Event IDs to update: ", eventIds);

                    // Step 2: Fetch updated event data based on the event IDs
                    fetch(updateEventsUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                                // Dynamically update the events grid
                                const eventsGrid = document.querySelector('.events-grid');
                                eventsGrid.innerHTML = ''; // Clear the grid before adding new cards

                                // Loop through each event and generate HTML
                                data.events.forEach(event => {
                                    const eventCardHTML = createEventCardHTML(event);
                                    eventsGrid.innerHTML += eventCardHTML; // Append each card to the grid
                                });
                            } else {
                                console.error("No events found based on selected filters.");
                                const eventsGrid = document.querySelector('.events-grid');
                                eventsGrid.innerHTML = '<p>No events found for the selected filters.</p>';
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching updated events:", error);
                        });
                } else {
                    console.log("No events found for the selected filters.");
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
    const eventRoute = "event/" + event.event_id;  // Fallback to '#' if route is undefined
    const eventImageUrl = "storage/events/" + event.event_media || 'storage/events/default-image.jpg';
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
