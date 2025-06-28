<?php
include "connection.php";

// Start the session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_Id']) && !empty($_SESSION['user_Id'])) {
    $userId = (int) $_SESSION['user_Id']; 

    $products = fetchAll($conn, 'products', "created_by=$userId");
    $_SESSION['product_data'] = $products;

    // Retrieve ONLY the current user's data
    $user = fetchAll($conn, 'Users', "");
    $_SESSION['user_data'] = $user;

    $supplier = fetchAll($conn, 'supplier','');
    $_SESSION['supplier_data']= $supplier;

} 
 else {
    $_SESSION['error'] = "You are not logged in.";
    header("Location: login.php");
    exit();
}

function fetchAll($conn, $table, $condition = '')
{
    $query = "SELECT * FROM $table";
    if ($condition) {
        $query .= " WHERE $condition";
    }
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return []; // return an empty array if no results
}

