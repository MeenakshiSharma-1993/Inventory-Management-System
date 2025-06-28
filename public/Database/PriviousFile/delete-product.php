<?php
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $query = "DELETE FROM products WHERE id=$id";
    if (mysqli_query($conn, $query)) {
      header("Location: product-view.php");
    } else {
        $_SESSION['error'] = "Unable to delete product.";
        header("Location: product-view.php");
    }
} 
