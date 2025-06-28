<?php
session_start();
include './public/Database/connection.php';
// Access control
$user = $_SESSION['username'];
if (!isset($user)){
    header("location: login.php");
    exit();
} else {
    echo "<script>console.log('User logged in dashboard page: " . $user . "');</script>";
}

// DELETE user
include './public/Database/delete.php';

// UPDATE user
include './public/Database/update.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View User</title>
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

                <!-- Edit User Section -->
                <div id="modal">
                    <div class="add-user" style="background: #fff; padding: 20px; border: 1px solid black">
                        <h3>Edit User</h3>
                        <form action="" method="POST" class="form-box">
                            <input type="hidden" name="user_id" id="editId">
                            <label>First Name:<input name="first_name" id="editFirst" required></label><br>
                            <label>Last Name:<input name="last_name" id="editLast" required></label><br>
                            <label>Email:<input name="email" id="editEmail" type="email" required></label><br>
                            <input class="formBtn" name="update_user" type="submit" value="Update">
                            <input class="formBtn" id="cancelBtn" type="button" value="Cancel">
                        </form>
                    </div>
                </div>

                <!-- Edit User Form -->
                <!-- List of Users Section -->
                <div class="list-users">
                    <div class="add-user">
                        <h3><i class="fas fa-list"></i> List of Users</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>FIRST NAME</th>
                                <th>LAST NAME</th>
                                <th>EMAIL</th>
                                <th>CREATED AT</th>
                                <th>UPDATED AT</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "./public/Database/ShowData.php";

                            if (isset($_SESSION['user_data']) && is_array($_SESSION['user_data'])) {
                                $user_data = $_SESSION['user_data'];
                                $count = 0;
                                foreach ($user_data as $user1) {
                                    $userid = $user1['id'];
                                    echo "<tr>
                                            <td>" . ++$count . "</td>
                                            <td>" . $user1['first_name'] . "</td>
                                            <td>" . $user1['last_name'] . "</td>
                                            <td>" . $user1['email'] . "</td>
                                            <td>" . date('m, d, y @ h:i:s A', strtotime($user1['created_at'])) . "</td>
                                            <td>" . date('m, d, y @ h:i:s A', strtotime($user1['updated_at'])) . "</td>
                                            <td>
                                            <a href='#' class='edit-btn' data-id=" . $user1['id'] . " 
                                                data-first-name=" . $user1['first_name'] . " 
                                                data-last-name=" . $user1['last_name'] . " 
                                                data-email=" . $user1['email'] . "><i class='fa fa-pencil'></i>Edit</a>" .
                                        "<a href='?delete_user=$userid' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete " . $user1["first_name"] . ' ' . $user1['last_name']  . "?');\"><i class='fa fa-trash'></i>Delete</a>
                                            </td>
                                        </tr>";
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
                echo "<script>console.log('Total users: " . count($user_data ?? []) . "');</script>";
                echo $count ?? 0;
                ?> Users
            </p>
            <?php
                if (isset($_SESSION['error'])) {
                    $error = $_SESSION['error'];
                    echo "<div id='errMsg'>$error</div>";
                    unset($_SESSION['error']);
                }
                ?>
        </div>
    </div>
</body>
<script src="public/js/script.js"></script>
<script>
    document.querySelectorAll('.edit-btn').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();

            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editFirst').value = this.dataset.firstName;
            document.getElementById('editLast').value = this.dataset.lastName;
            document.getElementById('editEmail').value = this.dataset.email;
            document.getElementById('modal').style.display = 'flex';
        });
    });
    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('modal').style.display = 'none';
    });
    // Optional: close modal if clicked outside
    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
</script>

</html>