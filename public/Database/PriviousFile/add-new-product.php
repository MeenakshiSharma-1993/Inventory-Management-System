<?php
include 'connection.php';
session_start();

$_SESSION['error'] = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_Id'])) {
    $product_name = $_POST["product_name"];
    $description = $_POST["description"];
    $created_by = $_SESSION['user_Id'];
    // Handle file upload
    $img = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $img = time() . "_" . basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], "../../public/uploads/" . $img);
    } else {
        $img = '';
    }
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");
    try {
        $query = "INSERT INTO products (product_name, description, img , created_by , created_at, Updated_at) VALUES(?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $product_name, $description, $img, $created_by, $created_at, $updated_at);

        if ($stmt->execute()) {
            $_SESSION['error'] = "Product added successfully.";
            header("Location: ../../product-add.php");
        } else {
            $_SESSION['error'] = "Failed to add product.";
        }
        $stmt->close();
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage();
        $_SESSION['error'] = "Failed to add product. Please try again.";
    }
}
