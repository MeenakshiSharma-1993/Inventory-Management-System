<?php
// session_start();
if (isset($_GET['delete'])) {
    $User_IdLogin = $_SESSION['user_Id'];
    echo "<script>console.log('User ID logged in: " . $User_IdLogin . "');</script>";
    $deleteId = $_GET['delete'];
    echo "<script>console.log('User ID deleted: " . $deleteId . "');</script>";
    if ($User_IdLogin != $deleteId) {

        mysqli_query($conn, "DELETE FROM users WHERE id = $deleteId");
        $_SESSION['error'] = "User deleted successfully!";
        header("Location: user-view.php");
        exit();
    } else {
        $_SESSION['error'] = "You can not delete your own account!";
        header("Location: user-view.php");
        exit();
    }
} 
