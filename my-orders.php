<?php
session_start();
include './public/Database/connection.php';
// Access control
$user = $_SESSION['username'];
if (!isset($user)) {
    header("location: login.php");
    exit();
} else {
    echo "<script>console.log('User logged in dashboard page: " . $user . "');</script>";
}

// DELETE supplier
include './public/Database/delete.php';

// UPDATE supplier
include './public/Database/update.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Supplier</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'partial/app-sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main">
            <?php include 'partial/app-topnav.php'; ?>
            <?php
            $errMsg = "";
            $permissions = $_SESSION['permissions'];
            $permission_values = explode(',', $permissions[0]['permissions']);
            if (in_array("payment", $permission_values)) { ?>
                <div class="content-container">

                    <!-- List of My orders Section -->
                    <div class="list-users">
                        <div class="add-user">
                            <h3><i class="fas fa-shopping-cart"></i> My Orders</h3>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Location</th>
                                    <th>EMAIL</th>
                                    <th>CREATED AT</th>
                                    <th>UPDATED AT</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include "./public/Database/ShowData.php";

                                if (isset($_SESSION['supplier_data']) && is_array($_SESSION['supplier_data'])) {
                                    $update_data = $_SESSION['supplier_data'];
                                    $count = 0;
                                    foreach ($update_data as $supplier1) {
                                        $supplierid = $supplier1['id'];
                                        echo "<tr>
                                            <td>" . ++$count . "</td>
                                            <td>" . $supplier1['supplier_name'] . "</td>
                                            <td>" . $supplier1['supplier_location'] . "</td>
                                            <td>" . $supplier1['email'] . "</td>
                                            <td>" . date('m, d, y @ h:i:s A', strtotime($supplier1['created_at'])) . "</td>
                                            <td>" . date('m, d, y @ h:i:s A', strtotime($supplier1['updated_at'])) . "</td>
                                            <td>";
                                        if (in_array("supplier-edit", $permission_values)) {

                                            echo "<a href='#' class='edit-btn'
                                        data-id='" . $supplier1['id'] . "'
                                        data-suppname='" . htmlspecialchars($supplier1['supplier_name'], ENT_QUOTES) . "'
                                        data-supplocation='" . htmlspecialchars($supplier1['supplier_location'], ENT_QUOTES) . "'
                                        data-email='" . htmlspecialchars($supplier1['email'], ENT_QUOTES) . "'>
                                        <i class='fa fa-pencil'></i>Edit</a>";
                                        }
                                        if (in_array("supplier-delete", $permission_values)) {

                                            echo "<a href='?delete_supplier=$supplierid' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete " . $supplier1["supplier_name"] . ' ' . $supplier1['supplier_location']  . "?');\"><i class='fa fa-trash'></i>Delete</a>
                                            </td>
                                        </tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No users found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="footer-text">
                    <?php
                    echo "<script>console.log('Total Suppliers: " . count($update_data ?? []) . "');</script>";
                    echo $count ?? 0;
                    ?> Suppliers
                </p>
                <?php
                if (isset($_SESSION['error'])) {
                    $error = $_SESSION['error'];
                    if ($error != "") {
                        echo "<div id='errMsg'>$error</div>";
                        unset($_SESSION['error']);
                    }
                }
                ?>
            <?php } else {
                $errMsg = "You Do not have permission to View Supplier.";
            }

            if ($errMsg != "") {
                echo "<div id='errMsg'>$errMsg</div>";
            }
            ?>
        </div>
    </div>
</body>
<script src="public/js/script.js"></script>

</html>