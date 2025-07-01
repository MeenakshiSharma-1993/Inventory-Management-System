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
    <title>Add User</title>
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
            <div class="content-container">
                <?php
                $errMsg = "";
                $permissions = $_SESSION['permissions'];
                $permission_values = explode(',', $permissions[0]['permissions']);
                if (in_array("supplier-create", $permission_values)) { ?>
                    <!-- Create User Section -->

                    <div class="add-user">
                        <h3><i class="fas fa-user-plus"></i>Create Supplier</h3>
                        <div class="form-box">
                            <form action="public/Database/add.php" method="POST">
                                <input type="hidden" name="form_name" value="Supplier-add">
                                <label for="supp_name">Supplier Name</label>
                                <input type="text" id="supp_name" name="supp_name" required>

                                <label for="supp_Location">Supplier Location</label>
                                <input type="text" id="supp_Location" name="supp_Location" required>

                                <label for="supp_email">EMAIL</label>
                                <input type="email" id="supp_email" name="supp_email" required>

                                <button type="submit" class="formBtn">+ Add Supplier</button>
                            </form>

                        </div>
                    </div>
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
                    $errMsg = "You Do not have permission to Add Supplier.";
                }

                if ($errMsg != "") {
                    echo "<div id='errMsg'>$errMsg</div>";
                };
                ?>
            </div>
        </div>
    </div>
</body>
<script src="public/js/script.js"></script>

</html>