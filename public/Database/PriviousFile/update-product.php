<?php
if (isset($_POST['update_product'])) {
    $id = (int) $_POST['product_id']; 
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']); 
    $description = mysqli_real_escape_string($conn, $_POST['description']); 
    $img = '';
    if ($_FILES['img']['name']) {
        $img = time().'_'.$_FILES['img']['name']; 
        move_uploaded_file($_FILES['img']['tmp_name'], "public/uploads/".$img);
    } else {
        // If no new photo is uploaded, you may want to retain the existing photo
        $query = "SELECT img FROM products WHERE id=$id LIMIT 1";
        $res = mysqli_query($conn, $query);
        $current = mysqli_fetch_assoc($res);
        $img = $current['img']; 
    }
    $query = "UPDATE products SET product_name='$product_name', description='$description', img='$img' WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        header("Location: product-view.php");
    } else {
        $_SESSION['error'] = "Failed to update product.";
        header("Location: product-view.php");
    }
}
