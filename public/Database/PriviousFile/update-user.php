<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];

    $query = "UPDATE users SET first_name='$fname', last_name='$lname', email='$email', updated_at=NOW() WHERE id=$id";
     mysqli_query($conn, $query);
    $_SESSION['error'] = "User updated successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    header("Location: user-view.php");
    exit();
}

?>