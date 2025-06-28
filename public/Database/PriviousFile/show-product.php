<?php
$user = $_SESSION['user_Id'];
// echo "<script>console.log('$user')</script>";
if (isset($user) && !empty($user)) {
    $query = "SELECT * FROM products where created_by = " . $user . " ORDER BY id ASC";
    echo "<script>console.log('$query');</script>";
    $res = mysqli_query($conn, $query);
    $product_data = [];

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $product_data[] = $row;
        }
    }

    $_SESSION['product_data'] = $product_data;
} else {
    $_SESSION['error'] = "You are not logged in.";
    header("Location: login.php");
    exit();
}
