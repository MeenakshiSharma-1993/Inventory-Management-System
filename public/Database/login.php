<?php
include_once 'public/Database/connection.php';
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statement to fetch user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE first_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password matches the hashed one
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_Id'] = $user['id'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password."; // password didn't match
        }
    } else {
        $error = "Invalid username or password."; // user not found
    }
}
?>
