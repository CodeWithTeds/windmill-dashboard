<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    
        let hasShownArrivalMessage = false;
        let lastKnownDistance = null;
        let isNavigating = false;
        let isDemoMode = false;
        let demoInterval = null;

        const map = L.map('map', {
            center: [14.393344, 121.0412],
            zoom: 100,
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
                console.log('Places data:', data); // Debug log
                const markers = [];

                data.forEach(place => {
                    const {
                        id,
                        slot_name,
                        location_address,
                        availability_status,
                        slot_type,
                        parking_spot_area,
                        slot_capacity,
                        nearby_landmarks,
                        geolocation_latitude,
                        geolocation_longitude
                    } = place;

                    const marker = L.marker([geolocation_latitude, geolocation_longitude])
                        .addTo(map)
                        .bindPopup(`
                        <div class="popup-container" style="min-width: 300px; font-family: 'Arial', sans-serif;">
                            <div class="popup-header" style="background-color: #4a90e2; color: white; padding: 10px; border-radius: 5px 5px 0 0; margin: -13px -13px 10px -13px;">
                                <h3 style="margin: 0; font-size: 18px;">${slot_name}</h3>
                            </div>
                            
                            <div class="popup-info" style="margin-bottom: 15px;">
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">pin_drop</i>
                                    <span><b>ID:</b> ${id}</span>
                                </div>
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">location_on</i>
                                    <span><b>Address:</b> ${location_address}</span>
                                </div>
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">info</i>
                                    <span><b>Status:</b> <span class="status-badge" style="background-color: ${
                                        availability_status === 'available' ? '#4CAF50' : 
                                        availability_status === 'occupied' ? '#f44336' : '#ff9800'
                                    }; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">${availability_status}</span></span>
                                </div>
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">local_parking</i>
                                    <span><b>Type:</b> ${slot_type}</span>
                                </div>
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">square_foot</i>
                                    <span><b>Area:</b> ${parking_spot_area} m²</span>
                                </div>
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">people</i>
                                    <span><b>Capacity:</b> ${slot_capacity}</span>
                                </div>
                                <div class="info-row" style="display: flex; margin-bottom: 8px; align-items: center;">
                                    <i class="material-icons" style="margin-right: 8px; color: #666;">place</i>
                                    <span><b>Nearby:</b> ${nearby_landmarks}</span>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button onclick="openBookingModal('${id}', '${slot_name}', ${geolocation_latitude}, ${geolocation_longitude})" 
                                    style="flex: 1; background-color: #4a90e2; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: background-color 0.3s;">
                                    <i class="material-icons" style="font-size: 18px; vertical-align: middle;">event</i>
                                    Reserve now
                                </button>
                                
                                <button onclick="calculateRoute(${geolocation_latitude}, ${geolocation_longitude})"
                                    style="flex: 1; background-color: #28a745; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: background-color 0.3s;">
                                    <i class="material-icons" style="font-size: 18px; vertical-align: middle;">directions</i>
                                    Get Directions
                                </button>
                            </div>
                        </div>
                    `);

                    const placeCard = document.createElement('div');
                    placeCard.className = 'place-card';
                    placeCard.innerHTML = `
                        <h3>${slot_name}</h3>
                        <div class="details">
                            <div><i class="material-icons">location_on</i>${location_address}</div>
                            <div><i class="material-icons">check_circle</i>${availability_status}</div>
                            <div><i class="material-icons">local_parking</i>${slot_type}</div>
                            <div><i class="material-icons">info</i>${parking_spot_area} m²</div>



                        </div>
                    `;

                    placeCard.addEventListener('click', () => {
                        map.setView([geolocation_latitude, geolocation_longitude], 15);
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

    <script>
        let userMarker = null;
        let routingControl = null;
        let watchId = null;
        let currentBookingId = null;
        let parkingTimer = null;
        let parkingStartTime = null;

        // First fetch initial location from PHP
        fetch('get_user_location.php')
            .then(response => response.json())
            .then(location => {
                userLocation = [location.latitude, location.longitude];
                // Add initial marker for user's location from PHP
                userMarker = L.marker(userLocation, {
                    icon: L.icon({
                        iconUrl: 'assets/img/man.png',
                        iconSize: [32, 32]
                    })
                }).addTo(map).bindPopup('Your Location').openPopup();

                // After getting PHP location, start real-time tracking
                startLocationTracking();

                // Load parking places with routing buttons
                loadParkingPlaces();
            })
            .catch(error => {
                console.error('Error fetching PHP location:', error);
                // If PHP location fails, fall back to browser geolocation
                startLocationTracking();
                loadParkingPlaces();
            });

        // Function to load parking places
        function loadParkingPlaces() {
            fetch('include/get_places.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(place => {
                        const {
                            name,
                            latitude,
                            longitude,
                            location_address,
                            availability_status,
                            slot_type,
                            parking_spot_area,
                            slot_capacity,
                            nearby_landmarks
                        } = place;

                        const marker = L.marker([latitude, longitude])
                            .addTo(map)
                            .bindPopup(`
                        
                            <button onclick="calculateRoute(${latitude}, ${longitude})">Get Directions</button>
                        `);
                    });
                })
                .catch(error => console.error('Error loading parking places:', error));
        }

        // Function to update user's location
        function updateUserLocation(position) {
            const newLocation = [position.coords.latitude, position.coords.longitude];
            
            if (userMarker) {
                userMarker.setLatLng(newLocation);
                
                // Check distance if we're navigating
                if (routingControl && isNavigating) {
                    const waypoints = routingControl.getWaypoints();
                    if (waypoints[1] && waypoints[1].latLng) {
                        const destLat = waypoints[1].latLng.lat;
                        const destLng = waypoints[1].latLng.lng;
                        
                        const distance = calculateDistance(
                            position.coords.latitude,
                            position.coords.longitude,
                            destLat,
                            destLng
                        );

                        console.log('Distance update:', distance, 'meters');
                        updateDistanceDisplay(distance);
                        
                        // Immediate arrival check
                        if (distance <= 100 && !hasShownArrivalMessage) {
                            hasShownArrivalMessage = true;
                            isNavigating = false;
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'You have arrived!',
                                text: 'Welcome to your parking spot.',
                                confirmButtonColor: '#4a90e2',
                                showConfirmButton: true,
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    clearRoute();
                                    startParkingTimer();
                                }
                            });
                        }
                    }
                }
            } else {
                userMarker = L.marker(newLocation, {
                    icon: L.icon({
                        iconUrl: 'assets/img/man.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    })
                }).addTo(map);
            }

            // Update route if it exists and we're navigating
            if (routingControl && isNavigating) {
                updateRoute();
            }
        }

        // Function to handle location errors
        function handleLocationError(error) {
            console.error('Error getting location:', error);
            alert('Unable to get your location. Please enable location services.');
        }

        // Start tracking location
        function startLocationTracking() {
            if ("geolocation" in navigator) {
                watchId = navigator.geolocation.watchPosition(updateUserLocation, handleLocationError, {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 27000
                });
            } else {
                alert('Geolocation is not supported by your browser');
            }
        }

        // First, update the modal HTML structure with a lower z-index
        const modalHtml = `
        <div id="bookingModal" class="modal" style="display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow: auto;">
            <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 500px; position: relative;">
                <span class="close-modal" style="position: absolute; right: 20px; top: 10px; font-size: 28px; font-weight: bold; cursor: pointer; color: #666;">&times;</span>
                <h2 id="modalParkingName" style="margin-bottom: 20px; color: #333; font-size: 24px;"></h2>
                <div id="modalBookingForm" style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        <label for="modalTimeIn" style="font-weight: bold; color: #555;">Time In:</label>
                        <input type="datetime-local" id="modalTimeIn" required style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        <label for="modalTimeOut" style="font-weight: bold; color: #555;">Time Out:</label>
                        <input type="datetime-local" id="modalTimeOut" required style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <button id="modalBookButton" style="background-color: #4a90e2; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: background-color 0.3s;">
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>`;

        // Add this CSS to ensure SweetAlert appears above the modal
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .swal2-container {
                    z-index: 2000 !important;
                }
                
                .swal2-popup {
                    z-index: 2001 !important;
                }

                .leaflet-routing-container {
                    background-color: white;
                    padding: 10px;
                    margin: 10px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.2);
                    max-height: 400px;
                    overflow-y: auto;
                    width: 300px;
                }

                .leaflet-routing-alt {
                    max-height: none !important;
                }

                .leaflet-routing-container h2 {
                    font-size: 16px;
                    margin: 0 0 10px 0;
                    color: #333;
                }

                .leaflet-routing-container h3 {
                    font-size: 14px;
                    margin: 10px 0;
                    color: #666;
                }

                .leaflet-routing-alt-minimized {
                    display: none;
                }

                .leaflet-routing-geocoder input {
                    width: 100%;
                    padding: 5px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }

                .leaflet-routing-geocoder-result {
                    background-color: white;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    margin-top: 2px;
                }

                .leaflet-routing-geocoder-result table {
                    width: 100%;
                }

                #distance-display {
                    transition: all 0.3s ease;
                }
                
                @media (max-width: 768px) {
                    #distance-display {
                        font-size: 14px;
                        padding: 8px 16px;
                    }
                }
            </style>
        `);

        // Add modal to the document
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Initialize routing control with detailed instructions
        function calculateRoute(destLat, destLng) {
            isNavigating = true;
            hasShownArrivalMessage = false;
            lastKnownDistance = null;
            
            if (!userMarker) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Location Not Available',
                    text: 'Waiting for your location...',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            // Get user's real position
            const userPos = userMarker.getLatLng();
            
            // Clear existing route
            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userPos.lat, userPos.lng),
                    L.latLng(destLat, destLng)
                ],
                routeWhileDragging: false,
                lineOptions: {
                    styles: [{ color: '#4a90e2', weight: 6 }]
                }
            }).addTo(map);

            // Initial distance calculation
            const distance = calculateDistance(
                userPos.lat,
                userPos.lng,
                destLat,
                destLng
            );
            updateDistanceDisplay(distance);
        }

        // Add function to verify location
        function verifyLocation(destLat, destLng) {
            // Get user's real position
            if (!userMarker) return;
            const userPos = userMarker.getLatLng();

            fetch('include/verify_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    parking_id: currentBookingId,
                    user_latitude: userPos.lat,
                    user_longitude: userPos.lng
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Start parking timer when real location is verified
                    startParkingTimer(currentBookingId);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Location Verified!',
                        text: 'Your parking timer has started.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error verifying location:', error);
            });
        }

        // Add these new functions for parking timer
        function startParkingTimer() {
            // Clear any existing timer
            if (parkingTimer) {
                clearInterval(parkingTimer);
            }
            
            // Set start time
            parkingStartTime = new Date();
            
            // Create timer element if it doesn't exist
            if (!document.getElementById('parking-timer-container')) {
                const timerContainer = document.createElement('div');
                timerContainer.id = 'parking-timer-container';
                timerContainer.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background-color: white;
                    padding: 15px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                    z-index: 1000;
                `;
                timerContainer.innerHTML = `
                    <h3 style="margin: 0 0 10px 0;">Parking Time</h3>
                    <div id="parking-timer" style="font-size: 24px; font-weight: bold;">00:00:00</div>
                `;
                document.body.appendChild(timerContainer);
            }
            
            // Start the timer
            parkingTimer = setInterval(updateParkingTimer, 1000);
        }

        function updateParkingTimer() {
            if (!parkingStartTime) return;
            
            const now = new Date();
            const diff = now - parkingStartTime;
            
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            const timerDisplay = document.getElementById('parking-timer');
            if (timerDisplay) {
                timerDisplay.textContent = timeString;
            }
        }

        function endParking() {
            if (parkingTimer) {
                clearInterval(parkingTimer);
                
                // Calculate total time
                const endTime = new Date();
                const totalTime = endTime - parkingStartTime;
                const hours = Math.ceil(totalTime / 3600000); // Round up to nearest hour
                
                // Show summary
                Swal.fire({
                    icon: 'info',
                    title: 'Parking Ended',
                    html: `
                        <p>Total Parking Time: ${formatDuration(totalTime)}</p>
                        <p class="mt-2">Charged Hours: ${hours}</p>
                    `,
                    confirmButtonColor: '#4a90e2'
                }).then(() => {
                    // Clean up
                    const timerContainer = document.getElementById('parking-timer-container');
                    if (timerContainer) {
                        timerContainer.remove();
                    }
                    parkingStartTime = null;
                    clearRoute();
                });
            }
        }

        function formatDuration(ms) {
            const hours = Math.floor(ms / 3600000);
            const minutes = Math.floor((ms % 3600000) / 60000);
            const seconds = Math.floor((ms % 60000) / 1000);
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Update the clearRoute function
        function clearRoute() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            
            if (distanceDisplayContainer) {
                distanceDisplayContainer.remove();
                distanceDisplayContainer = null;
            }
            
            isNavigating = false;
            lastKnownDistance = null;
        }

        // Update route when user location changes
        function updateRoute() {
            if (routingControl && userMarker) {
                const userPos = userMarker.getLatLng();
                const waypoints = routingControl.getWaypoints();
                if (waypoints.length >= 2 && waypoints[1].latLng) {
                    routingControl.setWaypoints([
                        L.latLng(userPos.lat, userPos.lng),
                        waypoints[1].latLng
                    ]);
                }
            }
        }

        // Clean up when page is unloaded
        window.addEventListener('unload', function() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
            }
            clearRoute();
        });

        // Add visibility change handler
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Don't reset arrival state when coming back to tab
                if (isNavigating) {
                    // Only update the route if we're still navigating
                    if (routingControl) {
                        updateRoute();
                    }
                }
            }
        });

        // Add this function to check if user is near parking spot
        function isUserNearParking(parkingLat, parkingLng) {
            if (!userMarker) return false;
            
            const userPos = userMarker.getLatLng();
            const distance = calculateDistance(
                userPos.lat,
                userPos.lng,
                parkingLat,
                parkingLng
            );
            
            // Return true if user is within 100 meters of parking spot
            return distance <= 100;
        }

        // Update the openBookingModal function
        function openBookingModal(parkingId, parkingName, parkingLat, parkingLng) {
            // Check if user is near parking spot first
            if (!isUserNearParking(parkingLat, parkingLng)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot Reserve',
                    text: 'You must be within 100 meters of the parking spot to make a Reservation.',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            const modal = document.getElementById('bookingModal');
            const modalParkingName = document.getElementById('modalParkingName');
            const modalBookButton = document.getElementById('modalBookButton');
            const closeBtn = document.querySelector('.close-modal');
            
            modalParkingName.textContent = parkingName;
            modal.style.display = 'block';
            
            // Set minimum datetime to current time
            const now = new Date();
            // Add 5 minutes to current time
            now.setMinutes(now.getMinutes() + 5);
            
            const timeInInput = document.getElementById('modalTimeIn');
            const timeOutInput = document.getElementById('modalTimeOut');
            
            // Calculate initial time out (1 hour after time in)
            const initialTimeOut = new Date(now);
            initialTimeOut.setHours(initialTimeOut.getHours() + 1);
            
            // Format dates for input fields
            timeInInput.min = now.toISOString().slice(0, 16);
            timeInInput.value = now.toISOString().slice(0, 16);
            timeOutInput.min = initialTimeOut.toISOString().slice(0, 16);
            timeOutInput.value = initialTimeOut.toISOString().slice(0, 16);
            
            // Update time out min value when time in changes
            timeInInput.addEventListener('change', function() {
                const selectedTimeIn = new Date(this.value);
                const minTimeOut = new Date(selectedTimeIn.getTime() + (60 * 60 * 1000)); // +1 hour
                const maxTimeOut = new Date(selectedTimeIn.getTime() + (24 * 60 * 60 * 1000)); // +24 hours
                
                timeOutInput.min = minTimeOut.toISOString().slice(0, 16);
                timeOutInput.max = maxTimeOut.toISOString().slice(0, 16);
                timeOutInput.value = minTimeOut.toISOString().slice(0, 16);
            });
            
            modalBookButton.onclick = (event) => {
                bookParkingSpot(event, parkingId, parkingName, parkingLat, parkingLng);
            };
            
            closeBtn.onclick = () => {
                modal.style.display = 'none';
            };
            
            window.onclick = (event) => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };
        }

        // Update the bookParkingSpot function with SweetAlert validations
        function bookParkingSpot(event, parkingId, parkingName, parkingLat, parkingLng) {
            event.preventDefault();
            
            const timeIn = new Date(document.getElementById('modalTimeIn').value);
            const timeOut = new Date(document.getElementById('modalTimeOut').value);
            const now = new Date();
            
            // Basic validation
            if (!timeIn || !timeOut) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select both time in and time out',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            // Validate minimum future time
            if (timeIn < now) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: 'Time in must be in the future',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            // Validate time out is after time in
            if (timeIn >= timeOut) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time Range',
                    text: 'Time out must be after time in',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            // Calculate duration in hours
            const durationHours = (timeOut - timeIn) / (1000 * 60 * 60);

            // Validate minimum 1 hour
            if (durationHours < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Booking Duration Too Short',
                    text: 'Minimum booking duration is 1 hour',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            // Validate maximum 24 hours
            if (durationHours > 24) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Booking Duration Too Long',
                    text: 'Maximum booking duration is 24 hours',
                    confirmButtonColor: '#4a90e2'
                });
                return;
            }

            // Show confirmation dialog before proceeding
            Swal.fire({
                title: 'Confirm Booking',
                html: `
                    <div style="text-align: left; margin-bottom: 1rem;">
                        <p><strong>Parking Spot:</strong> ${parkingName}</p>
                        <p><strong>Time In:</strong> ${timeIn.toLocaleString()}</p>
                        <p><strong>Time Out:</strong> ${timeOut.toLocaleString()}</p>
                        <p><strong>Duration:</strong> ${Math.round(durationHours)} hours</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4a90e2',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm Booking',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing Booking',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const bookingData = {
                        parking_id: parkingId,
                        time_in: timeIn.toISOString(),
                        time_out: timeOut.toISOString()
                    };

                    fetch('include/book_parking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(bookingData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Start navigation immediately after successful booking
                            startNavigationAfterBooking(parkingId, data.booking_id, parkingLat, parkingLng);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Booking Failed',
                                text: data.message || 'Failed to book parking spot',
                                confirmButtonColor: '#4a90e2'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error booking parking:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process booking. Please try again.',
                            confirmButtonColor: '#4a90e2'
                        });
                    });
                }
            });
        }

        // Add this function to handle post-booking navigation
        function startNavigationAfterBooking(parkingId, bookingId, parkingLat, parkingLng) {
            Swal.fire({
                icon: 'success',
                title: 'Booking Successful!',
                text: 'Starting navigation to your parking spot...',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Close the booking modal if it's open
                const modal = document.getElementById('bookingModal');
                if (modal) {
                    modal.style.display = 'none';
                }
                
                currentBookingId = bookingId;
                hasShownArrivalMessage = false;
                isNavigating = true;
                lastKnownDistance = null;
                
                // Start immediate distance checking
                checkDistance(parkingLat, parkingLng);
                calculateRoute(parkingLat, parkingLng);
            });
        }

        // Add this new function for immediate distance checking
        function checkDistance(destLat, destLng) {
            if (!userMarker) return;
            
            const userPos = userMarker.getLatLng();
            const distance = calculateDistance(
                userPos.lat,
                userPos.lng,
                destLat,
                destLng
            );
            
            console.log('Initial distance check:', distance, 'meters');
            updateDistanceDisplay(distance);
            
            // Check for arrival immediately
            if (distance <= 100 && !hasShownArrivalMessage) {
                hasShownArrivalMessage = true;
                isNavigating = false;
                
                Swal.fire({
                    icon: 'success',
                    title: 'You have arrived!',
                    text: 'Welcome to your parking spot.',
                    confirmButtonColor: '#4a90e2',
                    showConfirmButton: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearRoute();
                        startParkingTimer();
                    }
                });
            }
        }

        // Add these new functions

        let distanceDisplayContainer = null;

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Earth's radius in meters
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;

            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            return R * c; // Distance in meters
        }

        function updateDistanceDisplay(distance) {
            if (!distanceDisplayContainer) {
                // Create distance display container if it doesn't exist
                distanceDisplayContainer = document.createElement('div');
                distanceDisplayContainer.id = 'distance-display';
                distanceDisplayContainer.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    left: 20px;
                    background-color: white;
                    padding: 15px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                    z-index: 1000;
                    font-weight: bold;
                `;
                distanceDisplayContainer.innerHTML = `
                    <h3 style="margin: 0 0 10px 0;">Distance to Destination</h3>
                    <div id="distance-value" style="font-size: 24px; font-weight: bold;"></div>
                `;
                document.body.appendChild(distanceDisplayContainer);
            }

            // Update distance text
            let distanceText = '';
            if (distance >= 1000) {
                distanceText = `${(distance/1000).toFixed(1)} km away`;
            } else {
                distanceText = `${Math.round(distance)} meters away`;
            }
            
            const distanceValue = document.getElementById('distance-value');
            if (distanceValue) {
                distanceValue.textContent = distanceText;
            }
        }
    </script>

