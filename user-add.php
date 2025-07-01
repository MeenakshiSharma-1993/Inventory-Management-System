<?php
session_start();
include './public/Database/connection.php';
include './public/Database/showData.php';
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
                <!-- Create User Section -->
                <?php
                $errMsg = "";
                $permissions = $_SESSION['permissions'];
                $permission_values = explode(',', $permissions[0]['permissions']);
                if (in_array("user-create", $permission_values)) { ?>

                    <div class="add-user">
                        <h3><i class="fas fa-user-plus"></i>Create User</h3>
                        <div class="form-box">
                            <form action="public/Database/add.php" method="POST">
                                <input type="hidden" name="form_name" value="User-add">
                                <label for="first_name">FIRST NAME</label>
                                <input type="text" id="first_name" name="first_name" required>

                                <label for="last_name">LAST NAME</label>
                                <input type="text" id="last_name" name="last_name" required>

                                <label for="email">EMAIL</label>
                                <input type="email" id="email" name="email" required>

                                <label for="password">PASSWORD</label>
                                <input type="password" id="password" name="password" required>
                                <label for="email">Permissions</label>
                                <input type="hidden" name="permission_value" id="permission_value">
                                <!-- permission code -->
                                <?php
                                include 'permission.php';
                                ?>
                                <button type="submit" class="formBtn">+ Add User</button>
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
                    $errMsg = "You Do not have permission to Add user.";
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