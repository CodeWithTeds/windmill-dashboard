<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/Database.php';

require('views/head.php');
require('views/sidebar.php');
require('views/header.php');

class BookingHistory {
    private $db;
    private $userId;

    public function __construct($userId) {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userId = $userId;
    }

    public function getBookings() {
        $sql = "SELECT b.*, p.slot_name, p.location_address, b.status 
                FROM parking_bookings b 
                JOIN places p ON b.parking_id = p.id 
                WHERE b.user_id = ? 
                ORDER BY b.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->userId]);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error fetching bookings: " . $e->getMessage());
            return false;
        }
    }

    public function calculateDuration($timeIn, $timeOut) {
        $timeInObj = new DateTime($timeIn);
        $timeOutObj = new DateTime($timeOut);
        $duration = $timeInObj->diff($timeOutObj);
        return $duration->h + ($duration->days * 24);
    }

    public function determineStatus($timeIn, $timeOut) {
        $now = new DateTime();
        $timeInObj = new DateTime($timeIn);
        $timeOutObj = new DateTime($timeOut);

        if ($now < $timeInObj) {
            return [
                'status' => 'Upcoming',
                'class' => 'bg-blue-100 text-blue-800'
            ];
        } elseif ($now > $timeOutObj) {
            return [
                'status' => 'Completed',
                'class' => 'bg-green-100 text-green-800'
            ];
        } else {
            return [
                'status' => 'Active',
                'class' => 'bg-yellow-100 text-yellow-800'
            ];
        }
    }

    public function renderBookingRow($booking) {
        try {
            $hours = $this->calculateDuration($booking['time_in'], $booking['time_out']);
            $timeIn = new DateTime($booking['time_in']);
            $timeOut = new DateTime($booking['time_out']);

            // Check if the booking is already cancelled in the database
            $statusInfo = isset($booking['status']) && $booking['status'] === 'cancelled' 
                ? [
                    'status' => 'Cancelled',
                    'class' => 'bg-red-100 text-red-800'
                ] 
                : $this->determineStatus($booking['time_in'], $booking['time_out']);

            $html = "<tr>";
            $html .= "<td class='font-medium'>#" . htmlspecialchars($booking['id']) . "</td>";
            $html .= "<td>" . htmlspecialchars($booking['location_address']) . "</td>";
            $html .= "<td>" . $timeIn->format('M d, Y h:i A') . "</td>";
            $html .= "<td>" . $timeOut->format('M d, Y h:i A') . "</td>";
            $html .= "<td>{$hours} hours</td>";
            $html .= "<td><span class='status-badge {$statusInfo['class']}'>{$statusInfo['status']}</span></td>";
            $html .= "<td class='space-x-2'>";
            // Only show cancel button if status is Upcoming and not cancelled
            if ($statusInfo['status'] === 'Upcoming' && (!isset($booking['status']) || $booking['status'] !== 'cancelled')) {
                $html .= "<button 
                          onclick='cancelBooking({$booking['id']}, this);' 
                          class='btn-action btn-cancel'>
                          Cancel
                        </button>";
            }
            $html .= "</td>";
            $html .= "</tr>";

            return $html;
        } catch (Exception $e) {
            error_log("Error rendering booking row: " . $e->getMessage());
            return "<tr><td colspan='7' class='px-6 py-4 text-center text-sm text-gray-500'>Error displaying this booking</td></tr>";
        }
    }
}

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$bookingHistory = new BookingHistory($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking - Booking History</title>
    <link href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* Modern Table Container with enhanced borders */
        .table-container {
            @apply bg-white rounded-xl p-6;
            border: 2px solid #e5e7eb;
            border-top: 4px solid #3b82f6;
            position: relative;
            box-shadow: 
                0 4px 6px -1px rgba(59, 130, 246, 0.1),
                0 2px 4px -1px rgba(59, 130, 246, 0.06),
                0 0 0 3px rgba(59, 130, 246, 0.05);
            z-index: 1;
        }

        /* Decorative corners */
        .table-container::before,
        .table-container::after,
        .table-corner-bl::before,
        .table-corner-br::before {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            border: 2px solid #3b82f6;
            border-radius: 50%;
            background: white;
        }

        .table-container::before {
            top: -6px;
            left: -6px;
        }

        .table-container::after {
            top: -6px;
            right: -6px;
        }

        .table-corner-bl::before {
            bottom: -6px;
            left: -6px;
        }

        .table-corner-br::before {
            bottom: -6px;
            right: -6px;
        }

        /* Table styles */
        #bookingTable {
            @apply rounded-lg overflow-hidden;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        #bookingTable thead th {
            @apply bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 px-6;
            border-bottom: 2px solid #3b82f6;
        }

        #bookingTable tbody tr {
            @apply hover:bg-blue-50 transition-all duration-200;
            border-bottom: 1px solid rgba(59, 130, 246, 0.1);
        }

        #bookingTable tbody td {
            @apply py-4 px-6;
            border-right: 1px solid rgba(59, 130, 246, 0.05);
        }

        /* Status Badge styles */
        .status-badge {
            @apply px-3 py-1.5 rounded-full text-xs font-medium inline-flex items-center;
            border: 1px solid transparent;
        }
        .status-active {
            @apply bg-green-100 text-green-800;
            border-color: rgba(34, 197, 94, 0.3);
        }
        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
            border-color: rgba(234, 179, 8, 0.3);
        }
        .status-cancelled {
            @apply bg-red-100 text-red-800;
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* Action Button styles */
        .btn-action {
            @apply px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200;
            border: 1px solid transparent;
        }
        .btn-view {
            @apply bg-blue-100 text-blue-700 hover:bg-blue-200;
            border-color: rgba(59, 130, 246, 0.3);
        }
        .btn-cancel {
            @apply bg-red-100 text-red-700 hover:bg-red-200 ml-2;
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* Ensure DataTables elements are clickable */
        .dataTables_wrapper {
            position: relative;
            z-index: 2;
        }

        /* Make sure buttons and inputs are clickable */
        #bookingTable button,
        #bookingTable_filter input,
        #bookingTable_length select,
        .dataTables_paginate .paginate_button {
            position: relative;
            z-index: 3 !important;
            pointer-events: auto !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8 bg-white rounded-lg shadow-lg p-6 flex items-center border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300">
            <!-- Travel Icon in a small box with gradient and animation -->
            <div class="mr-6 bg-gradient-to-br from-blue-100 to-blue-200 p-4 rounded-lg shadow-inner transform hover:scale-110 transition-all duration-300">
                <svg class="w-8 h-8 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                        d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
            </div>

            <!-- Text Content with enhanced typography -->
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800 mb-2">
                    Booking History
                </h1>
                <p class="text-gray-600 text-lg tracking-wide">
                    Manage and track your parking reservations
                </p>
            </div>

            <!-- Additional decorative element -->
            <div class="hidden md:block absolute top-0 right-0 -mt-2 -mr-2">
                <div class="w-20 h-20 bg-blue-50 rounded-full opacity-20"></div>
            </div>
        </div>

    

        <!-- Table Card -->
        <div class="relative">
            <div class="table-container">
                <div class="table-corner-bl"></div>
                <div class="table-corner-br"></div>
                
                <table id="bookingTable" class="w-full">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Location</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bookings = $bookingHistory->getBookings();
                        if ($bookings) {
                            while ($booking = $bookings->fetch(PDO::FETCH_ASSOC)) {
                                echo $bookingHistory->renderBookingRow($booking);
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-blue-600 opacity-5 rounded-xl -z-10"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#bookingTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10,
                stateSave: true,
                drawCallback: function() {
                    $('.dataTables_wrapper button, .dataTables_wrapper input, .dataTables_wrapper select, .paginate_button').css({
                        'pointer-events': 'auto',
                        'position': 'relative',
                        'z-index': '3'
                    });
                },
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    searchable: false
                }],
                language: {
                    search: "🔍 Search bookings",
                    lengthMenu: "Show _MENU_ bookings",
                    info: "Showing _START_ to _END_ of _TOTAL_ bookings",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "→",
                        previous: "←"
                    }
                }
            });

            // Additional event binding for good measure
            $(document).on('click', '.btn-cancel', function(e) {
                e.stopPropagation();
                const bookingId = $(this).closest('tr').find('td:first').text().replace('#', '');
                cancelBooking(bookingId, this);
            });
        });

        function cancelBooking(bookingId, buttonElement) {
            if (!bookingId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid booking ID'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('booking_id', bookingId);

                    fetch('cancel_booking.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Get the table row and update its status cell
                            const row = buttonElement.closest('tr');
                            const statusCell = row.querySelector('td:nth-last-child(2)'); // Status column
                            statusCell.innerHTML = '<span class="status-badge bg-red-100 text-red-800">Cancelled</span>';
                            
                            // Remove the cancel button
                            buttonElement.remove();
                            
                            Swal.fire(
                                'Cancelled!',
                                'Your booking has been cancelled.',
                                'success'
                            );
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to cancel booking: ' + data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error canceling booking. Please try again.'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>