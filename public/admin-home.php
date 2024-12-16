<?php 
require('views/admin-head.php');
require('views/admin-sidebar.php');
require('views/admin-header.php');
require('config/Database.php');

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch statistics
function getCount($db, $table, $condition = '') {
    $sql = "SELECT COUNT(*) as count FROM $table $condition";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Get various counts
$totalUsers = getCount($db, 'users');
$totalParkingSlots = getCount($db, 'places');
$totalBookings = getCount($db, 'parking_bookings');
$activeBookings = getCount($db, 'parking_bookings', "WHERE NOW() BETWEEN time_in AND time_out");
?>

<main class="h-full overflow-y-auto">
    <div class="container px-6 mx-auto grid">
        <!-- Dashboard Header -->
        <div class="flex justify-between items-center my-6">
            <h2 class="text-2xl font-semibold text-gray-700">
                Dashboard Overview
            </h2>
            <div class="text-sm text-gray-500">
                <?php echo date('l, F j, Y'); ?>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <!-- Users Card -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">
                        Total Users
                    </p>
                    <p class="text-lg font-semibold text-gray-700">
                        <?php echo $totalUsers; ?>
                    </p>
                </div>
            </div>

            <!-- Parking Slots Card -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">
                        Total Parking Slots
                    </p>
                    <p class="text-lg font-semibold text-gray-700">
                        <?php echo $totalParkingSlots; ?>
                    </p>
                </div>
            </div>

            <!-- Total Bookings Card -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">
                        Total Bookings
                    </p>
                    <p class="text-lg font-semibold text-gray-700">
                        <?php echo $totalBookings; ?>
                    </p>
                </div>
            </div>

            <!-- Active Bookings Card -->
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">
                        Active Bookings
                    </p>
                    <p class="text-lg font-semibold text-gray-700">
                        <?php echo $activeBookings; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <!-- Recent Bookings -->
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Recent Bookings</h4>
                <div class="overflow-hidden overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b">
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Time In</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php
                            $sql = "SELECT pb.*, u.username 
                                   FROM parking_bookings pb 
                                   JOIN users u ON pb.user_id = u.id 
                                   ORDER BY pb.created_at DESC LIMIT 5";
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr class='text-gray-700'>";
                                echo "<td class='px-4 py-3'>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td class='px-4 py-3'>" . date('M d, H:i', strtotime($row['time_in'])) . "</td>";
                                echo "<td class='px-4 py-3'><span class='px-2 py-1 text-xs font-semibold leading-tight rounded-full " . 
                                     (strtotime($row['time_in']) > time() ? "bg-green-100 text-green-700" : "bg-gray-100 text-gray-700") . 
                                     "'>" . (strtotime($row['time_in']) > time() ? "Upcoming" : "Past") . "</span></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Recent Activities</h4>
                <div class="overflow-hidden overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b">
                                <th class="px-4 py-3">Action</th>
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Details</th>
                                <th class="px-4 py-3">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php
                            // Get recent activities (bookings, cancellations, etc.)
                            $sql = "SELECT pb.*, u.username, p.slot_name 
                                   FROM parking_bookings pb 
                                   JOIN users u ON pb.user_id = u.id 
                                   JOIN places p ON pb.parking_id = p.id
                                   ORDER BY pb.created_at DESC LIMIT 5";
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr class='text-gray-700'>";
                                
                                // Action column with icon
                                echo "<td class='px-4 py-3 flex items-center'>";
                                if ($row['status'] == 'confirmed') {
                                    echo "<span class='p-2 bg-green-100 text-green-700 rounded-full mr-2'><i class='fas fa-check'></i></span>";
                                    echo "New Booking";
                                } elseif ($row['status'] == 'cancelled') {
                                    echo "<span class='p-2 bg-red-100 text-red-700 rounded-full mr-2'><i class='fas fa-times'></i></span>";
                                    echo "Cancelled Booking";
                                }
                                echo "</td>";
                                
                                // User column
                                echo "<td class='px-4 py-3'>" . htmlspecialchars($row['username']) . "</td>";
                                
                                // Details column
                                echo "<td class='px-4 py-3'>";
                                echo "Parking at " . htmlspecialchars($row['slot_name']);
                                echo "<br><span class='text-sm text-gray-500'>";
                                echo date('M d, H:i', strtotime($row['time_in'])) . " - " . date('H:i', strtotime($row['time_out']));
                                echo "</span>";
                                echo "</td>";
                                
                                // Time column with relative time
                                echo "<td class='px-4 py-3 text-sm'>";
                                $created = new DateTime($row['created_at']);
                                $now = new DateTime();
                                $interval = $created->diff($now);
                                
                                if ($interval->d > 0) {
                                    echo $interval->d . " days ago";
                                } elseif ($interval->h > 0) {
                                    echo $interval->h . " hours ago";
                                } elseif ($interval->i > 0) {
                                    echo $interval->i . " minutes ago";
                                } else {
                                    echo "Just now";
                                }
                                echo "</td>";
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require('views/admin-footer.php'); ?>