// Main front-end script for Student Book Exchange.
// In later steps this file will contain fetch calls to the PHP API endpoints.

console.log("Student Book Exchange: base layout loaded.");

// Front-end helper to send POST requests for adding favourites.
async function addFavourite(type, id) {
    const formData = new FormData();
    formData.append('type', type);
    formData.append('id', id);

    const res = await fetch('api/fav_add.php', {
        method: 'POST',
        body: formData
    });

    const data = await res.json();

    if (data.success) {
        alert('Added to favourites.');
        location.reload();
    } else {
        alert(data.message || 'Could not add to favourites.');
    }
}