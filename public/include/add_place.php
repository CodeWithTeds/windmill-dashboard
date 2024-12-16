<!DOCTYPE html>
<html lang="en" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Slots Management</title>
    <!-- SweetAlert CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leafl et.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

    <style>
        /* Previous DataTables custom styles remain the same */
        .dataTables_wrapper select,
        .dataTables_wrapper .dataTables_filter input {
            color: #4a5568;
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: .5rem;
            padding-bottom: .5rem;
            line-height: 1.25;
            border-width: 2px;
            border-radius: .25rem;
            border-color: #edf2f7;
            background-color: #edf2f7;
        }

        /* Additional modal styles */
        #parkingSlotModal {
            display: none;
        }

        #parkingSlotModal.modal-open {
            display: flex;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 tracking-wider leading-normal">
    <!--Container-->
    <div class="container w-full md:w-4/5 xl:w-3/5 mx-auto px-2">
        <!--Title-->
        <h1 class="flex items-center font-sans font-bold break-normal text-indigo-500 px-2 py-8 text-xl md:text-2xl">
            Parking Slots Management
        </h1>

        <!-- Add New Slot Button -->
        <div class="mb-4">
            <button
                id="openModalBtn"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                Add New Parking Slot
            </button>
        </div>

        <!--Card-->
        <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
            <table id="parkingSlotsTable" class="stripe hover" style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                <thead>
                    <tr>
                        <th data-priority="1">ID</th>
                        <th data-priority="2">Slot Name</th>
                        <th data-priority="3">Location</th>
                        <th data-priority="4">Availability Status</th>
                        <th data-priority="5">Type</th>
                        <th data-priority="6">Price/Hour</th>
                        <th data-priority="7">Capacity</th>
                        <th data-priority="8">Nearby Landmarks</th>
                        <th data-priority="9">Latitude</th>
                        <th data-priority="10">Longitude</th>
                        <th data-priority="11">Created At</th>
                        <th data-priority="12">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $conn = new mysqli("localhost", "root", "", "easy_park"); // Replace with your database credentials

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch parking slot data
                    $sql = "SELECT * FROM places"; // Replace with your table name
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Output each row as a table row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['slot_name'] . "</td>";
                            echo "<td>" . $row['location_address'] . "</td>";
                            echo "<td>" . $row['availability_status'] . "</td>";
                            echo "<td>" . $row['slot_type'] . "</td>";
                            echo "<td>" . $row['price_per_hour'] . "</td>";
                            echo "<td>" . $row['slot_capacity'] . "</td>";
                            echo "<td>" . $row['nearby_landmarks'] . "</td>";
                            echo "<td>" . $row['geolocation_latitude'] . "</td>";
                            echo "<td>" . $row['geolocation_longitude'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td>
    <button class='edit-btn text-blue-500 hover:text-blue-700' data-id='" . $row['id'] . "'>Edit</button>
    <button class='delete-btn text-red-500 hover:text-red-700' data-id='" . $row['id'] . "'>Delete</button>
</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12' class='text-center'>No data available</td></tr>";
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal for Adding Parking Slot -->
        <div
    id="parkingSlotModal" 
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center overflow-y-auto"
>
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl max-h-[80vh] flex flex-col relative">
        <!-- Close Button -->
        <button
            id="closeModalBtn"
            class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4 px-4 pt-4">Add New Parking Slot</h2>

        <div class="flex flex-col flex-grow overflow-hidden">
            <div id="map" class="h-48 w-full rounded-lg border-2 border-gray-200 mb-4 px-4 flex-shrink-0"></div>

            <div class="overflow-y-auto flex-grow px-4 pb-4">
                <form id="locationForm" method="post" action="insert_place.php" class="space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="slot_name" class="block text-sm font-medium text-gray-700">Slot Name/Identifier</label>
                            <input
                                type="text"
                                id="slot_name"
                                name="slot_name"
                                required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="location_address" class="block text-sm font-medium text-gray-700">Location/Address</label>
                            <textarea
                                id="location_address"
                                name="location_address"
                                required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="availability_status" class="block text-sm font-medium text-gray-700">Availability Status</label>
                            <select
                                id="availability_status"
                                name="availability_status"
                                required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>

                        <div>
                            <label for="slot_type" class="block text-sm font-medium text-gray-700">Slot Type</label>
                            <select
                                id="slot_type"
                                name="slot_type"
                                required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="standard">Standard</option>
                                <option value="compact">Compact</option>
                                <option value="motorcycle">Motorcycle</option>
                                <option value="electric_vehicle">Electric Vehicle</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="price_per_hour" class="block text-sm font-medium text-gray-700">Price per Hour</label>
                            <input
                                type="number"
                                step="0.01"
                                id="price_per_hour"
                                name="price_per_hour"
                                required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="slot_capacity" class="block text-sm font-medium text-gray-700">Slot Capacity</label>
                            <input
                                type="text"
                                id="slot_capacity"
                                name="slot_capacity"
                                required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label for="nearby_landmarks" class="block text-sm font-medium text-gray-700">Nearby Landmarks</label>
                        <textarea
                            id="nearby_landmarks"
                            name="nearby_landmarks"
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                            <input
                                type="text"
                                id="latitude"
                                name="latitude"
                                readonly
                                required
                                class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                            <input
                                type="text"
                                id="longitude"
                                name="longitude"
                                readonly
                                required
                                class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Parking Slot
                        </button>
                    </div>
                </form>

                <div id="message" class="hidden mt-4 p-4 rounded-md"></div>
            </div>
        </div>
    </div>
</div>


        <!-- jQuery -->
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

        <!-- SweetAlert JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#parkingSlotsTable').DataTable({
                    responsive: true
                });

                // Modal Control
                const modal = document.getElementById('parkingSlotModal');
                const openModalBtn = document.getElementById('openModalBtn');
                const closeModalBtn = document.getElementById('closeModalBtn');

                openModalBtn.addEventListener('click', () => {
                    modal.classList.add('modal-open');
                    initMap();
                });

                closeModalBtn.addEventListener('click', () => {
                    modal.classList.remove('modal-open');
                });

                // Map Initialization Function
                let map, marker;

                function initMap() {
                    if (map) {
                        map.remove();
                    }

                    map = L.map('map').setView([14.5501, 121.0144], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                    }).addTo(map);

                    map.on('click', function(e) {
                        const {
                            lat,
                            lng
                        } = e.latlng;

                        if (marker) {
                            map.removeLayer(marker);
                        }

                        marker = L.marker([lat, lng]).addTo(map);

                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    });
                }

                // Form Submission
                document.getElementById('locationForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch('insert_place.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'Cool',
                                    confirmButtonColor: '#3085d6'
                                }).then((result) => {
                                    location.reload(); // This will refresh the page
                                });
                            }
                        })
                        .catch(error => {
                            // Use SweetAlert for error notification
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while adding the place.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                            console.error('Error:', error);
                        });
                });
            });

       // Edit Button Handler
$('#parkingSlotsTable').on('click', '.edit-btn', function() {
    const parkingSlotId = $(this).data('id');
    
    // Open modal for editing
    $('#parkingSlotModal').addClass('modal-open');
    
    // Fetch parking slot details via AJAX
    $.ajax({
        url: 'get_parking_slot.php',
        method: 'GET',
        data: { id: parkingSlotId },
        success: function(data) {
            // Check if there's an error in the response
            if (data.error) {
                Swal.fire({
                    title: 'Error!',
                    text: data.error + (data.details ? ': ' + data.details : ''),
                    icon: 'error'
                });
                return;
            }

            // Populate form fields with existing data
            $('#slot_name').val(data.slot_name);
            $('#location_address').val(data.location_address);
            $('#availability_status').val(data.availability_status);
            $('#slot_type').val(data.slot_type);
            $('#price_per_hour').val(data.price_per_hour);
            $('#slot_capacity').val(data.slot_capacity);
            $('#nearby_landmarks').val(data.nearby_landmarks);
            $('#latitude').val(data.geolocation_latitude);
            $('#longitude').val(data.geolocation_longitude);
            
            // Add a hidden input to track we're editing
            $('<input>').attr({
                type: 'hidden',
                name: 'edit_id',
                value: parkingSlotId
            }).appendTo('#locationForm');
        },
        error: function(xhr, status, error) {
            // Log full error details to console
            console.error('AJAX Error:', status, error);
            console.log('Response Text:', xhr.responseText);

            Swal.fire({
                title: 'Error!',
                text: 'Could not fetch parking slot details. ' + error,
                icon: 'error'
            });
        }
    });
});
// Delete Button Handler
$('#parkingSlotsTable').on('click', '.delete-btn', function() {
    const parkingSlotId = $(this).data('id');
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'delete_place.php',
                method: 'POST',
                data: { id: parkingSlotId },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Parking slot has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload(); // This will refresh the page
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Could not delete the parking slot.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Could not delete the parking slot.',
                        'error'
                    );
                }
            });
        }
    });
});

        </script>
</body>

</html>
