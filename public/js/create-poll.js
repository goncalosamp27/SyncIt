function addOption() {
    const optionsContainer = document.getElementById('poll-options');
    const input = document.createElement('div');
    input.classList.add('mb-2');
    input.innerHTML = '<input type="text" name="options[]" class="form-control" placeholder="New Option" required>';
    optionsContainer.appendChild(input);
}

document.addEventListener('DOMContentLoaded', () => {
    let pollDataUrl = pollWrapper.querySelector('.poll-data-url').value;
    const form = document.querySelector('form');
    form.addEventListener('click', function () {
        console.log("button was clicked");


        const title = document.getElementById('title').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const options = Array.from(document.querySelectorAll('input[name="options[]"]')).map(input => input.value);
        const eventId = document.getElementById('event_id').value;

        console.log(title);
        console.log(startDate);
        console.log(endDate);
        console.log(options);
        console.log(eventId);
       
        const data = {
            title: title,
            start_date: startDate,
            end_date: endDate,
            options: options,
            eventId :eventId,
        };

        fetch(pollUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Poll Created Successfully!');
                } else {
                    alert('Error creating poll');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting the form');
            });
    });
});


function addOption() {
    const optionsContainer = document.getElementById('poll-options');
    const input = document.createElement('div');
    input.classList.add('mb-2');
    input.innerHTML = `<input type="text" name="options[]" class="form-control" placeholder="New Option" required>`;
    optionsContainer.appendChild(input);
}
