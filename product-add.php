<?php
session_start();
include './public/Database/connection.php';

// Access control
$user = $_SESSION['username'];
if (!isset($user)) {
    header("location: login.php");
    exit();
} else {
    echo "<script>console.log('User logged in dashboard page: " . $user . "');</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>
</head>

<body>

    <div class="container">
        <!-- Sidebar -->
        <?php include 'partial/app-sidebar.php'; ?>
        <!-- Main Content -->
        <div class="main p-4">
            <?php include 'partial/app-topnav.php'; ?>
            <div class="content-container">
<?php       
                $errMsg="";         
                $permissions = $_SESSION['permissions'];
                $permission_values = explode(',', $permissions[0]['permissions']);
                if (in_array("product-create", $permission_values)) { ?>
                <!-- Create Product Section -->
                <div class="add-user mb-4">
                    <h3 class="add-user mb-4 fs-4 fw-semibold">
                        <i class="fas fa-box-open me-2"></i>Add Product
                    </h3>
                    <div class="form-box p-4 border rounded shadow">
                        <form action="public/Database/add.php" enctype="multipart/form-data" method="POST">
                            <input type="hidden" name="form_name" value="Product-add">

                            <label for="product_name" class="form-label">Product Name</label>
                            <input id="product_name" name="product_name" required class="form-control mb-3" type="text">

                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" rows="3" class="form-control mb-3"></textarea>

                            <label for="supplier" class="form-label">suppliers</label>
                            <select id="supplier" name="supplier[]" class=" form-select mb-3" multiple>
                                <option disabled selected>Select Supplier</option>
                                <?php

                                include './public/Database/showData.php';
                                if (isset($_SESSION['supplier_data']) && is_array($_SESSION['supplier_data'])) {
                                    foreach ($_SESSION['supplier_data'] as $supplier) {
                                        echo "<option value='" . $supplier['id'] . "'>" . $supplier['supplier_name'] . "</option>";
                                    }
                                } else {
                                    echo "<option disabled>No suppliers available</option>";
                                }
                                ?>
                            </select>

                            <label for="img" class="form-label">Product Image</label>
                            <input id="img" name="img" accept="image/*" class="form-control mb-4" type="file">

                            <button type="submit" class="formBtn btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Product
                            </button>
                        </form>
                    </div>
                </div>

                <?php
                if (isset($_SESSION['error'])) {
                    $error = $_SESSION['error']; ?>
                    <div id='errMsg' class='alert alert-warning mt-4'><?= $error; ?></div>
                <?php unset($_SESSION['error']);
                }
                ?>
                 <?php } else  {
                        $errMsg = "You Do not have permission to Add Product.";
                    }
                
                if($errMsg!=""){echo "<div id='errMsg'>$errMsg</div>";};
                ?>
            </div>
        </div>
    </div>

    <script src="public/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/js/bootstrap.bundle.min.js"></script>

    <!-- <div class="container">
       Sidebar 
        <?php //include 'partial/app-sidebar.php'; 
        ?>
       Main Content
        <div class="main">
            <?php
            //include 'partial/app-topnav.php'; 
            ?>
            <div class="content-container">
                Create Product Section 
                <div class="add-user">
                    <h3><i class="fas fa-box-open"></i>Add Product</h3>
                    <div class="form-box">
                        <form action="public/Database/add.php" enctype="multipart/form-data" method="POST">
                            <input type="hidden" name="form_name" value="Product-add">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name" required>

                            <label for="description">Description</label>
                            <textarea id="description" name="description"></textarea>

                            <label for="img">Product Image</label>
                            <input type="file" id="img" name="img"  accept="image/*" >

                            <button type="submit" class="formBtn"><i class="fas fa-plus"></i> Add Product</button>
                        </form>
                    </div>
                </div>
                <?php
                // if (isset($_SESSION['error'])) {
                //     $error = $_SESSION['error'];
                //     echo "<div id='errMsg'>$error</div>";
                //     unset($_SESSION['error']);
                // }
                ?>
            </div> 
        </div>
    </div> -->
</body>
<script src="public/js/script.js"></script>

</html>