document.addEventListener('DOMContentLoaded', function () {
    const applyFiltersButton = document.getElementById('apply-filters');

    applyFiltersButton.addEventListener('click', function () {
        console.log("Button was clicked");

        // Utility function to get selected values for a given name
        const getSelectedValues = () => {
            // Get all checked inputs from all categories and return their values in a single list
            return Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
                .map(input => input.value)
                .filter(value => value !== '0' && value !== ''); // Ensure no empty or '0' values
        };

        // Get all selected values into one list
        const allTags = getSelectedValues();

        // Debug: Log the combined list of selected tags
        console.log('All Tags:', allTags);

        // Step 1: Fetch filtered event IDs based on selected tags
        fetch(filterEventsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ tags: allTags }) // Send the selected tags inside an object
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch filtered events');
                }
                return response.json();
            })
            .then(data => {
                //console.log(data.eventIds); // Log the event IDs from the response
                console.log(data.tags);
                if (data.success) {
                    const events = data.events;
                    const tags = data.tags;
                    const eventsGrid = document.querySelector('.events-grid');
                    eventsGrid.innerHTML = '';  // Clear the current grid
                    events.forEach(event => {
                        // Map the tags for each event
                        const mappedTags = tags.map(event =>
                            event.tags.map(tag => ({
                                tag_name: tag.tag_name,  // The tag name
                                color: tag.color         // The tag color
                            }))
                        ).flat();;
                        console.log(mappedTags);
                        console.log(typeof mappedTags);  // Outputs the type of mappedTags
                        //const tagsHTML = generateTagsHTML(mappedTags); // Call your function to get the HTML
                        const eventCardHTML = createEventCardHTML(event, mappedTags);
                        eventsGrid.innerHTML += eventCardHTML;
                    });
                    if (events && events.length > 0) {
                        events.forEach(event => {
                            console.log("Event:", event);
                        });
                    }
                    else {
                        console.log('No events found with the selected filters');
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching filtered events:", error);
            });
    });
});

// Function to generate event card HTML
function createEventCardHTML(event, tagsHtml) {
    // Check if the essential event data is defined
    const eventRoute = "event/" + event.event_id;  // Fallback to '#' if route is undefined
    const eventImageUrl = "storage/events/" + event.event_media || 'storage/events/default-image.jpg';
    const eventName = event.event_name || 'Unknown Event';
    const eventLocation = event.location || 'Location not provided';
    const eventDate = formatEventDate(event.event_date);
    const eventCapacity = event.ticket_count || '0';
    const eventTotalCapacity = event.capacity || 'N/A';
    const eventPrice = event.price === 0 ? '<span class="event-free">FREE</span>' : `${event.price}€`;
    const eventTags = generateTagsHTML(tagsHtml);
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
                    ${eventTags}
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

function generateTagsHTML(tags) {
    console.log("Tags passed to generateTagsHTML:", tags);  // Log the tags to check their structure

    if (!tags || !Array.isArray(tags) || tags.length === 0) {
        console.warn('No tags available for this event');
        return '';  // Return an empty string if no tags are provided
    }

    // Limit the tags to the first 3
    const limitedTags = tags.slice(0, 3);

    let html = '';  // Initialize an empty string to hold the HTML

    // Iterate over the limited tags array with forEach
    limitedTags.forEach(tag => {
        // Check the structure of each tag object
        if (tag && tag.tag_name && tag.color) {
            // Append each tag's HTML with custom styles
            html += `
                <span class="tag-button" style="background: #${tag.color}; color: #fff; 
                        border-radius: 12px; padding: 8px 16px; display: inline-block; 
                        font-weight: bold; font-size: 14px; text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.2); 
                        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    ${tag.tag_name}
                </span>
            `;
        } else {
            console.warn("Invalid tag object:", tag);  // Log if any tag object is malformed
        }
    });

    return html;
}

