<?php

include 'connection.php';
$_SESSION['error'] = "";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["form_name"] == "User-add") {
        // echo "<script>console.log('Data received successfully');</script>";
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        $permissions = $_POST["permission_value"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        try {
            $query = "INSERT INTO users(first_name, last_name, email, password,permissions, created_at, updated_at) VALUES(?,?,?,?,?,?,?)";
            // var_dump($query);
            $params = array($first_name, $last_name, $email, $password, $permissions, $created_at, $updated_at);
            $sentPageAdd = "Location: ../../user-add.php";
            $pageName = "User";
            AddData($query, $params, $sentPageAdd, $pageName);
        } catch (Exception $e) {
            echo "ERROR:- " . $e->getMessage();
            $_SESSION['error'] = "Failed to add user. Please try again.";
        }
    } else if ($_POST["form_name"] == "Product-add" && isset($_SESSION['user_Id'])) {

        $product_name = $_POST["product_name"];
        $description = $_POST["description"];
        $amount = $_POST["amount"];
        $created_by = $_SESSION['user_Id'];
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
            // add data in products table
            $query = "INSERT INTO products (product_name, description,amount, img , created_by , created_at, Updated_at) VALUES(?,?,?,?,?,?,?)";
            $params = array($product_name, $description, $amount, $img, $created_by, $created_at, $updated_at);
            $sentPageAdd = "Location: ../../product-add.php";
            $pageName = "Product";
            AddData($query, $params, $sentPageAdd, $pageName);

            // add data in productSupplier table
            if (isset($_POST['supplier']) && is_array($_POST['supplier'])) {
                $selectedSuppliers = $_POST['supplier']; // This will be an array of IDs
                $lastProductId = $_SESSION['last_inserted_id'];
                foreach ($selectedSuppliers as $supplierId) {

                    $query2 = "INSERT INTO productsupplier (Product, Supplier, created_at, Updated_at) VALUES(?,?,?,?)";
                    $params2 = array($lastProductId, $supplierId, $created_at, $updated_at);
                    // $sentPageAdd = "Location: ../../product-add.php";
                    // $pageName = "Product";
                    AddData($query2, $params2, $sentPageAdd, $pageName);
                }
            }
        } catch (Exception $e) {
            echo "ERROR:- " . $e->getMessage();
            $_SESSION['error'] = "Failed to add product. Please try again.";
        }
    } else if ($_POST["form_name"] == "Supplier-add" && isset($_SESSION['user_Id'])) {
        $supp_name = $_POST["supp_name"];
        $Supp_Location = $_POST["supp_Location"];
        $supp_email = $_POST["supp_email"];
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $user_Id = $_SESSION['user_Id'];
        try {
            $query = "INSERT INTO supplier(supplier_name, supplier_location, email, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?)";
            $params = array($supp_name, $Supp_Location, $supp_email, $user_Id, $created_at, $updated_at);
            $sentPageAdd = "Location: ../../supplier-add.php";
            $pageName = "Supplier";
            AddData($query, $params, $sentPageAdd, $pageName);
        } catch (Exception $e) {
            echo "ERROR:- " . $e->getMessage();
            $_SESSION['error'] = "Failed to add supplier. Please try again.";
        }
    } else if ($_POST["form_name"] == "Product-order") {
        foreach ($_POST['items'] as $item) {
            $productId = $item['product_id'];
            $supplierId = $item['supplier_id'];
            $quantity_ordered = intval($item['quantity']);
            $batch = time();
            $status = "Pending";
            $created_at = date("Y-m-d H:i:s");
            $updated_at = date("Y-m-d H:i:s");
            $created_by = $_SESSION['user_Id'];
            try {
                $query = "INSERT INTO order_product(supplier, product, quantity_ordered, batch, status, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)";
                $params = array($supplierId, $productId, $quantity_ordered, $batch, $status, $created_by, $created_at, $updated_at);
                $sentPageAdd = "Location: ../../product-order.php";
                $pageName = "Order-Product";
                AddData($query, $params, $sentPageAdd, $pageName);
            } catch (Exception $e) {
                echo "ERROR:- " . $e->getMessage();
                $_SESSION['error'] = "Failed to placed order. Please try again.";
            }
        }
    }
}
function AddData($query, $params, $sentPageAdd, $pageName)
{
    global $conn;
    $result = $conn->prepare($query);
    if ($result) {
        $types = str_repeat('s', count($params));
        $result->bind_param($types, ...$params);
        if ($result->execute()) {
            $lastId = $conn->insert_id;
            $_SESSION['last_inserted_id'] = $lastId;
            $_SESSION['error'] = $pageName . " added successfully.";
            header($sentPageAdd);
        } else {
            $_SESSION['error'] = $result->error;
        }
        $result->close();
        // $conn->close();
    } else {
        $_SESSION['error'] = "Failed to prepare statement.";
    }
}
