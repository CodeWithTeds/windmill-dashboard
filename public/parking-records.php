<?php
require_once 'config/Database.php';
require('views/admin-head.php');
require('views/admin-sidebar.php');
require('views/admin-header.php');

class ParkingRecords {
    private $db;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllParkingRecords(){
        $sql = "SELECT * FROM parking_bookings ORDER by created_at DESC";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() === 0){
                error_log("No parking records found");
            }
            return $stmt;
        } catch(PDOException $e){
            error_log("Error Fetching parking records: " . $e->getMessage());
            return false;
        }
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
}

$parkingRecords = new ParkingRecords();
?>

<main class="h-full overflow-y-auto">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8 bg-white rounded-lg shadow-lg p-6 flex items-center border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300">
            <!-- Parking Icon in a small box with gradient and animation -->
            <div class="mr-6 bg-gradient-to-br from-blue-100 to-blue-200 p-4 rounded-lg shadow-inner transform hover:scale-110 transition-all duration-300">
                <svg class="w-8 h-8 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                        d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            </div>

            <!-- Text Content with enhanced typography -->
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800 mb-2">
                    Parking Records
                </h1>
                <p class="text-gray-600 text-lg tracking-wide">
                    View and manage all parking reservations
                </p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="relative">
            <div class="table-container">
                <div class="table-corner-bl"></div>
                <div class="table-corner-br"></div>
                
                <table id="parkingTable" class="w-full">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>User ID</th>
                            <th>Parking ID</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $records = $parkingRecords->getAllParkingRecords();
                        if ($records === false) {
                            echo '<tr><td colspan="6" class="px-4 py-3 text-center text-red-600">Error fetching parking records</td></tr>';
                        } else {
                            $hasRecords = false;
                            while ($record = $records->fetch(PDO::FETCH_ASSOC)) {
                                $hasRecords = true;
                                $statusInfo = $parkingRecords->determineStatus($record['time_in'], $record['time_out']);
                                ?>
                                <tr>
                                    <td class="font-medium">#<?= $record['id'] ?></td>
                                    <td><?= $record['user_id'] ?></td>
                                    <td><?= $record['parking_id'] ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($record['time_in'])) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($record['time_out'])) ?></td>
                                    <td>
                                        <span class="status-badge <?= $statusInfo['class'] ?>">
                                            <?= $statusInfo['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                            }
                            if (!$hasRecords) {
                                echo '<tr><td colspan="6" class="px-4 py-3 text-center">No parking records found</td></tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-blue-600 opacity-5 rounded-xl -z-10"></div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('table').DataTable({
            pageLength: 10,
            order: [[5, 'desc']], // Sort by Time In column
            responsive: true
        });
    });
</script>
