document.addEventListener("DOMContentLoaded", function () {
    const genreSelect = document.getElementById("genre");
    const musicDanceSelect = document.getElementById("music-dance");
    const musicDanceLabel = document.getElementById("music-dance-label");
    
    // Update the dropdown and label based on the selected genre
    genreSelect.addEventListener("change", function () {
        const selectedGenre = genreSelect.value;
        console.log(selectedGenre);
        // Clear current options
        
        musicDanceSelect.innerHTML = '<option value="" disabled selected>Select the event type</option>';

        console.log(musicDanceSelect.innerHTML);
        // Update label and options
        if (selectedGenre === "Music") {
            musicDanceSelect.innerHTML = '<option value="" disabled selected>Music</option>';
            musicTags.forEach(tag => {
                const option = document.createElement("option");
                option.value = tag.tag_id;
                option.textContent = tag.tag_name;
                musicDanceSelect.appendChild(option);
            });
        } else if (selectedGenre === "Dance") {
            musicDanceSelect.innerHTML = '<option value="" disabled selected>Dance</option>';
            danceTags.forEach(tag => {
                const option = document.createElement("option");
                option.value = tag.tag_id;
                option.textContent = tag.tag_name;
                musicDanceSelect.appendChild(option);
            });
        } else {
            musicDanceLabel.textContent = "Select the event type";
        }
    });
});
