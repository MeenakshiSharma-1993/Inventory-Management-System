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

// DELETE product
include './public/Database/delete.php';
// UPDATE product
include './public/Database/update.php';

$allSuppliers = $conn->query("SELECT * FROM supplier")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Product</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'partial/app-sidebar.php'; ?>
        <!-- Main Content -->
        <div class="main">
            <?php include 'partial/app-topnav.php'; ?>
            <div class="content-container">
                <!-- Edit Product Section -->
                <div id="modal" style="display: none">
                    <div class="add-user updateProduct">
                        <h3>Edit Product</h3>
                        <form action="" class="form-box" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" id="editId">
                            <label>Product Name:<input name="product_name" id="editProduct" required></label><br>
                            <label>Description:<input name="description" id="editDesc" required></label><br>
                            <label>Suppliers:<br>
                                <select id="supplier" name="supplier[]" multiple>
                                    <?php foreach ($allSuppliers as $sup) : ?>
                                        <option value="<?= $sup['id']; ?>"> <?= htmlentities($sup['supplier_name']); ?> </option>
                                    <?php endforeach ?>
                                </select>
                            </label><br>
                            <div class="ProductImage">
                                <div class="SubProductImage">
                                    <label>Old Image:</label>
                                    <img id="editPreview" src="" alt="Product Image">
                                </div>
                                <div class="SubProductImage" id="newImgContainer" style="display: none">
                                    <label>New Image:</label>
                                    <img id="newPreview" style="display: none" alt="New Image Preview"><br>
                                </div>
                            </div>
                            <input name="img" id="editImg" type="file" accept="image/*"><br><br>
                            <input name="update_product" type="submit" class="formBtn" value="Update">
                            <input id="cancelBtn" type="button" class="formBtn" value="Cancel">
                        </form>
                    </div>
                </div>

                <!-- List of Product Section -->
                <div class="list-users">
                    <div class="add-user">
                        <h3><i class="fas fa-list"></i> List of Products</h3>
                    </div>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th>Suppliers</th>
                                <th>Image</th>
                                <th>Stock</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include './public/Database/showData.php';
                            $product_data = $_SESSION['product_data'];
                            if (!empty($product_data)):
                                $count = 0;
                                foreach ($product_data as $product):
                                    // Prepare supplier IDs
                                    $sup_query = "SELECT s.id, s.supplier_name FROM supplier s JOIN productsupplier ps ON s.id = ps.supplier WHERE ps.product = " . $product['id'];
                                    $sup_result = $conn->query($sup_query);
                                    $suppliers = [];
                                    $sup_ids = [];

                                    while ($sup = $sup_result->fetch_assoc()) {
                                        $suppliers[] = $sup['supplier_name'];
                                        $sup_ids[] = $sup['id'];
                                    }
                                    $sup_ids_json = htmlentities(json_encode($sup_ids));
                            ?>
                                    <tr>
                                        <td><?= ++$count ?></td>
                                        <td><?= htmlentities($product['product_name']) ?></td>
                                        <td><?= htmlentities($product['description']) ?></td>
                                        <td>
                                            <?php if (!empty($suppliers)): ?>
                                                <ul>
                                                    <?php foreach ($suppliers as $sup) : ?>
                                                        <li style='margin-left:15px;'><?= htmlentities($sup) ?></li>
                                                    <?php endforeach ?>
                                                </ul>
                                            <?php else: ?>
                                                <?= "No suppliers" ?>
                                            <?php endif ?>
                                        </td>
                                        <td><?= htmlentities($product['stock']) ?></td>
                                        <td><img src="public/uploads/<?= $product['img']; ?>" width="50" height="50" alt="Product Image"></td>
                                        <td><?= date('m, d, y @ h:i:s A', strtotime($product['created_at'])) ?></td>
                                        <td>
                                            <a
                                                href="#"
                                                class="editBtn"
                                                data-id="<?= $product['id']; ?>"
                                                data-name="<?= htmlentities($product['product_name']); ?>"
                                                data-img="<?= htmlentities($product['img']); ?>"
                                                data-description="<?= htmlentities($product['description']); ?>"
                                                data-suppliers='<?= $sup_ids_json ?>'>
                                                <i class='fa fa-pencil'></i>Edit</a> |
                                            <a
                                                href="?delete_product=<?= $product['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete <?= htmlentities($product['product_name']); ?>?');">
                                                <i class='fa fa-trash'></i>Delete</a>
                                        </td>
                                    </tr>
                                <?php
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="7">No products</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- content-container -->
        </div><!-- main -->
    </div><!-- container -->

    <script src="public/js/script.js"></script>
    <script>
        document.querySelectorAll('.editBtn').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();

                document.getElementById('editId').value = this.dataset.id;
                document.getElementById('editProduct').value = this.dataset.name;
                document.getElementById('editDesc').value = this.dataset.description;
                document.getElementById('editPreview').src = "public/uploads/" + this.dataset.img;

                // Set multiselect
                const supplierSelect = document.getElementById('supplier');
                for (let option of supplierSelect.options) {
                    option.selected = false;
                }

                let supplierIds = JSON.parse(this.dataset.suppliers);

                supplierIds.forEach(id => {
                    for (let option of supplierSelect.options) {
                        if (parseInt(option.value) === parseInt(id)) {
                            option.selected = true;
                        }
                    }
                });

                document.getElementById('modal').style.display = 'block';
            });
        });

        document.getElementById('editImg').onchange = function(event) {
            const [file] = event.target.files;
            if (file) {
                const preview = document.getElementById('newPreview');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
                document.getElementById('newImgContainer').style.display = 'block';
            }
        };

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none';
        });

        window.onclick = function(e) {
            if (e.target == document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none';
            }
        }
    </script>

</body>

</html>