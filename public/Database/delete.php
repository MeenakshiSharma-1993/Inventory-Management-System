<?php
include 'connection.php';
// session_start();
$_SESSION['error'] = "";

// Delete User
if (isset($_GET['delete_user'])) {
    $User_IdLogin = $_SESSION['user_Id']; // Currently logged in
    $deleteId = (int) $_GET['delete_user'];

    if ($User_IdLogin != $deleteId) {
        DeleteData("users", $deleteId, "id", "Location: user-view.php", "User");
    } else {
        $_SESSION['error'] = "You cannot delete your own account.";
        header("Location: user-view.php");
        exit();
    }
}

// Delete Product
if (isset($_GET['delete_product'])) {
    $deleteId = (int) $_GET['delete_product'];
    DeleteData("products", $deleteId, "id", "Location: product-view.php", "Product");
}

// delete supplier
if (isset($_GET['delete_supplier'])) {
    $delId = intval($_GET['delete_supplier']);
    DeleteData("supplier",$delId,'id',"Location: supplier-view.php","supplier");
   
}

// delete order
if (isset($_GET['delete_order'])) {
    $delId = intval($_GET['delete_order']);
    DeleteData("order_product",$delId,'id',"Location: view-orders.php","Order");
   
}

// Reusable function for delete
function DeleteData($table, $id, $idColumn, $redirectPage, $itemName)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM $table WHERE $idColumn = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['error'] = "$itemName deleted successfully.";
            header($redirectPage);
            exit();
        } else {
            $_SESSION['error'] = "Unable to delete $itemName.";
            header($redirectPage);
            exit();
        }
    } else {
        $_SESSION['error'] = "Failed to prepare statement.";
        header($redirectPage);
        exit();
    }
}
