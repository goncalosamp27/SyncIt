document.addEventListener('DOMContentLoaded', function () {
    const applyFiltersButton = document.getElementById('apply-filters');
    const dropdownButtons = document.querySelectorAll('.dropdown-button');
    const resetFiltersButton = document.getElementById('reset-filters');

    dropdownButtons.forEach(button => {
        button.addEventListener('click', function () {
            const menu = this.nextElementSibling;
            menu.classList.toggle('show');
        });
    });

    document.addEventListener('click', function (event) {
        if (!event.target.matches('.dropdown-button') && !event.target.closest('.dropdown-menu')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
        }
    });

    applyFiltersButton.addEventListener('click', function () {
        console.log("Button was clicked");

        const getSelectedValues = (name) => {
            return Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(input => input.value);
        };

        const selectedTags = {
            dance_tags: getSelectedValues('dance_tag'),
            music_tags: getSelectedValues('music_tag'),
            mood_tags: getSelectedValues('mood_tag'),
            setting_tags: getSelectedValues('setting_tag'),
        };

        const allSelectedTags = Object.values(selectedTags)
            .flat()
            .filter(tag => tag !== '0');

        console.log('Selected Tags:', allSelectedTags);

        const eventTypes = Array.from(document.querySelectorAll('input[name="event_type"]:checked'))
            .map(input => input.value.charAt(0).toUpperCase() + input.value.slice(1)); // Capitalize the first letter

        console.log('Selected Event Types:', eventTypes);


        // Fetch filtered events
        fetch(filterEventsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ tags: allSelectedTags, event_type: eventTypes })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch filtered events');
                }
                return response.json();
            })
            .then(data => {
                console.log('Filtered Events Data:', data);

                // Now send data to render the partial view
                return fetch('/future-events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        events: data.events,
                        tagsMusic: data.tagsMusic,
                        tagsDance: data.tagsDance,
                        tagsMood: data.tagsMood,
                        tagsSettings: data.tagsSettings
                    })
                });
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to render event');
                }
                return response.text();
            })
            .then(data => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;

                const newEventsGrid = tempDiv.querySelector('#events-grid');

                if (newEventsGrid) {
                    const eventsGrid = document.getElementById('events-grid');
                    eventsGrid.innerHTML = newEventsGrid.innerHTML;
                } else {
                    console.error('No events grid found in the response.');
                }
            })
            .catch(error => {
                console.error("Error fetching filtered events:", error);
            });
    });
    resetFiltersButton.addEventListener('click', function () {
        console.log("button was clicked");
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);

        location.reload();
    });
});
