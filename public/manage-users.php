<?php
require('views/admin-head.php');
require('views/admin-sidebar.php');
require('views/admin-header.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require('config/Database.php');
require('models/User.php');

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$result = $user->getUsers();
?>
<!DOCTYPE html>
<html lang="en" class="antialiased">

<body class="bg-gray-100 text-gray-900 tracking-wider leading-normal">
    <div class="container w-full md:w-4/5 xl:w-3/5 mx-auto px-2">
        <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
            <table id="example" class="stripe hover" style="width:100%; padding-top: 1em; padding-bottom: 1em;">
                <thead>
                    <tr>
                        <th data-priority="1">ID</th>
                        <th data-priority="2">Name</th>
                        <th data-priority="3">Email</th>
                        <th data-priority="4">Created at</th>
                        <th data-priority="5">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr data-id='" . htmlspecialchars($row['id']) . "'>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                            <button onclick='editUser(" .
                                htmlspecialchars($row['id'], ENT_QUOTES) . ", \"" .
                                htmlspecialchars($row['username'], ENT_QUOTES) . "\", \"" .
                                htmlspecialchars($row['email'], ENT_QUOTES) .
                                "\")' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded mr-2'>
                                Edit
                            </button>
                            <button onclick='deleteUser(" . htmlspecialchars($row['id'], ENT_QUOTES) . ")' class='bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded'>
                                Delete
                            </button>
                        </td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

   <?php require('views/actions.php') ?>

    <!-- jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

    <!--Datatables -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                    responsive: true
                })
                .columns.adjust()
                .responsive.recalc();
        });
    </script>
</body>

</html>
<?php require('views/admin-footer.php'); ?>