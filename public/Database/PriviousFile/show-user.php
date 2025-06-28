<?php
include "connection.php";
//session_start();
$query = "SELECT * from Users";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
} else {
    echo "<script>console.log('Query executed successfully');</script>";
}
if ($result->num_rows > 0) {
    $_SESSION['user_data'] = $result->fetch_all(MYSQLI_ASSOC);
}
else {
}
?>