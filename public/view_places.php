<?php 
require('views/head.php');
require('views/sidebar.php');
require('views/header.php');
?>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div id="map-container">
        <div id="map"></div>
        <div id="sidebar">
            <h2 style="font-size: 1em; margin-bottom: 10px;">Parking Locations</h2>
            <div id="places-list"></div>
        </div>
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Search parking locations...">
        </div>
    </div>

    <script>
    const map = L.map('map', { 
        center: [14.393344, 121.0412],
        zoom: 16,
        zoomControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    L.control.zoom({
        position: 'bottomright'
    }).addTo(map);

    const placesList = document.getElementById('places-list');
    const searchInput = document.getElementById('search-input');

    fetch('include/get_places.php')
        .then(response => response.json())
        .then(data => {
            const markers = [];

            data.forEach(place => {
                const { name, location_address, availability_status, slot_type, parking_spot_area, slot_capacity, nearby_landmarks, latitude, longitude } = place;

                const marker = L.marker([latitude, longitude])
                    .addTo(map)
                    .bindPopup(`
                        <b>${name}</b><br>
                        <b>Address:</b> ${location_address}<br>
                        <b>Status:</b> ${availability_status}<br>
                        <b>Type:</b> ${slot_type}<br>
                        <b>Sq metters:</b>${parking_spot_area}<br>
                        <b>Capacity:</b> ${slot_capacity}<br>
                        <b>Nearby:</b> ${nearby_landmarks}
                    `);

                const placeCard = document.createElement('div');
                placeCard.className = 'place-card';
                placeCard.innerHTML = `
                    <h3>${name}</h3>
                    <div class="details">
                        <div><i class="material-icons">location_on</i>${location_address}</div>
                        <div><i class="material-icons">check_circle</i>${availability_status}</div>
                        <div><i class="material-icons">local_parking</i>${slot_type}</div>
                      <div><i class="material-icons">local_parking</i>${parking_spot_area}</div>


                    </div>
                `;

                placeCard.addEventListener('click', () => {
                    map.setView([latitude, longitude], 15);
                    marker.openPopup();
                });

                placesList.appendChild(placeCard);
                markers.push(marker);
            });

            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                markers.forEach((marker, index) => {
                    const place = data[index];
                    const placeCard = placesList.children[index];
                    
                    const matchesSearch = 
                        place.name.toLowerCase().includes(searchTerm) ||
                        place.location_address.toLowerCase().includes(searchTerm) ||
                        place.nearby_landmarks.toLowerCase().includes(searchTerm);

                    placeCard.style.display = matchesSearch ? 'block' : 'none';
                    
                    if (matchesSearch) {
                        marker.addTo(map);
                    } else {
                        map.removeLayer(marker);
                    }
                });
            });
        })
        .catch(error => console.error('Error fetching places:', error));
</script>

