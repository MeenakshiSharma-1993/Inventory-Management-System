<?php
include 'connection.php';
// session_start();
$_SESSION['error'] = "";
// Update User
if (isset($_POST['update_user'])) {
    $id = (int) $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $query = "UPDATE users SET first_name=?, last_name=?, email=?, updated_at=NOW() WHERE id=?";
    $params = array($first_name, $last_name, $email, $id);
    UpdateData($query, $params, "Location: user-view.php", "User");
}

if (isset($_POST['update_product'])) {
    $product_id   = intval($_POST['product_id']);
    $product_name = $_POST['product_name'];
    $description  = $_POST['description'];
    $suppliers    = isset($_POST['supplier']) ? $_POST['supplier'] : [];
    $updated_at   = date("Y-m-d H:i:s");

    // Handle image upload or keep existing
    if (!empty($_FILES['img']['name'])) {
        $img = time() . "_" . basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], "public/uploads/" . $img);
    } else {
        $query = "SELECT img FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $img = $row['img'];
        $stmt->close();
    }

    // Update product
    $query = "UPDATE products SET product_name = ?, description = ?, img = ?, updated_at = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $product_name, $description, $img, $updated_at, $product_id);
    $stmt->execute();
    $stmt->close();

    // Delete old supplier links
    $delete = $conn->prepare("DELETE FROM productsupplier WHERE product = ?");
    $delete->bind_param("i", $product_id);
    $delete->execute();
    $delete->close();

    // Insert new suppliers
    if (!empty($suppliers)) {
        $sql = "INSERT INTO productsupplier (supplier, product, updated_at, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $created_at = date("Y-m-d H:i:s");
        foreach ($suppliers as $supId) {
            $stmt->bind_param("iiss", $supId, $product_id, $updated_at, $created_at);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Redirect after success
    header("Location: product-view.php");
    exit;
}
// --- Update Supplier Logic ---
if (isset($_POST['update_supplier'])) {
    $supplier_id   = intval($_POST['supplier_id']);
    $name      = $_POST['supplier_name'];
    $location  = $_POST['supplier_location'];
    $email     = $_POST['email'];
    $updatedAt = date("Y-m-d H:i:s");

    $query = "UPDATE supplier SET supplier_name = ?, supplier_location = ?, email = ?, updated_at = ? WHERE id = ?";
    $params = array($name, $location, $email, $updatedAt, $supplier_id);
    UpdateData($query, $params, "Location: supplier-view.php", "Supplier");
}

//update Order quantity and status
if (isset($_POST['update_order']) && isset($_POST['order_ids'])) {
    $product_idList         = $_POST['product_id'];
    $orderIdsList           = $_POST['order_ids'];
    $quantity_receivedList  = $_POST['quantity_received'];
    $quantity_deliveredList = $_POST['quantity_delivered'];
    $quantity_orderedList   = $_POST['quantity_ordered'];
    $statusesList           = $_POST['statuses'];
    $updated_at             = date("Y-m-d H:i:s");
    $CheckUpdateOrNot       = 0;

    // Optional: collect updated product IDs to show later
    $updatedProducts = [];

    foreach ($orderIdsList as $i => $orderId) {
        $product_id   = intval($product_idList[$i]);
        $qtyReceived  = intval($quantity_receivedList[$i]);
        $qtyDelivered = intval($quantity_deliveredList[$i]);
        $qtyOrdered   = intval($quantity_orderedList[$i]);
        $status       = $statusesList[$i];

        $qtyRemainingBefore = $qtyReceived + $qtyDelivered;
        $qtyRemaining = $qtyOrdered - $qtyRemainingBefore;

        if ($qtyDelivered < $qtyRemainingBefore) {
            $CheckUpdateOrNot++;

            // Update main order
            $query = "UPDATE order_product 
                      SET quantity_received = ?, quantity_remaining = ?, status = ?, updated_at = ? 
                      WHERE id = ?";
            $params = array($qtyRemainingBefore, $qtyRemaining, $status, $updated_at, $orderId);
            UpdateData($query, $params, "", " Order ");

            // Log to history
            $query = "INSERT INTO order_product_history(order_product_id, qty_received, date_received, date_updated) VALUES (?, ?, ?, ?)";
            $types = "iiss";
            $params = array($orderId, $qtyReceived, $updated_at, $updated_at);
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            $updatedProducts[] = $product_id;
        }

        //Update main stock in product table
       
        $updateSql = "UPDATE products SET stock = ?, updated_at = ? WHERE id = ?";
        $stmt1 = $conn->prepare($updateSql);
        $stmt1->bind_param("isi", $qtyRemainingBefore, $updated_at, $product_id);
        if (!$stmt1->execute()) {
            die("Stock update failed: " . $stmt1->error);
        }
        $stmt1->close();
    }

    if ($CheckUpdateOrNot === 0) {
        echo "<script>
            alert('Check your quantity values. Nothing was updated.');
            window.location.href = 'view-orders.php';
        </script>";
    } else {
        $productList = implode(', ', $updatedProducts);
        echo "<script>
            alert('$CheckUpdateOrNot value(s) updated');
            window.location.href = 'view-orders.php';
        </script>";
    }
    exit();
}


function UpdateStock() {}


function UpdateData($query, $params, $redirectPage, $pageName)
{
    global $conn;
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $types = '';
        foreach ($params as $p) {
            $types .= is_int($p) ? 'i' : 's';
        }
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $_SESSION['error'] = "$pageName updated successfully.";
            if ($redirectPage) {
                header($redirectPage);
                exit();
            }
        } else {
            $_SESSION['error'] = $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to prepare statement.";
    }
}
