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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
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
                        <table style="width: 70%;margin: auto;">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                $query = "SELECT s.supplier_name, SUM(po.quantity_received * p.amount) AS total_amount 
                                        FROM order_product po 
                                        JOIN supplier s ON po.supplier = s.id 
                                        JOIN products p ON po.product = p.id 
                                        GROUP BY s.supplier_name";
                                $result = mysqli_query($conn, $query);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $amountInPaise = $row['total_amount'] * 100;

                                        echo "<tr><td>" . ++$count . "</td>" .
                                            "<td>" . $row['supplier_name'] . "</td>" .
                                            "<td>" . $row['total_amount'] . "</td>" .
                                            "<td style='display:flex;justify-content:center'>
                                                  <a href='payment.php?totalAmount=$amountInPaise' class='formBtn submitBtn' style='margin-top:0' id='payNowLink' target='_blank' onclick=\"return confirm('Are you sure? ');\">
                                                    <i class=\"fa-brands fa-cc-amazon-pay\"></i>Pay Now</a>
                                             </td>
                                             </tr>";
                                    }
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
                $errMsg = "You Do not have permission to my orders.";
            }

            if ($errMsg != "") {
                echo "<div id='errMsg'>$errMsg</div>";
            }
            ?>
        </div>
    </div>
</body>
<script src="public/js/script.js"></script>
<script>
    const amountInPaise = Math.round(total * 100);
    // Update payment link
    $('#payNowLink').attr('href', 'payment.php?totalAmount=' + amountInPaise);
</script>

</html>