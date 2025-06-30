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

include './public/Database/showData.php';
$products = $_SESSION['product_data'];

$suppliers = $_SESSION['supplier_data'];

$query = "SELECT * FROM supplier AS s right JOIN productsupplier AS ps ON s.id = ps.supplier";
$result = $conn->query($query);

// --- Build product-supplier map in PHP ---
$productSupplierMap = [];
while ($row = $result->fetch_assoc()) {
    $productId = $row['product'];
    $supplierId = $row['supplier']; // this is from productsupplier.supplier
    $supplierName = $row['supplier_name'];
    if (!isset($productSupplierMap[$productId])) {
        $productSupplierMap[$productId] = [];
    }
    $productSupplierMap[$productId][] = [
        'id' => $supplierId,
        'name' => $supplierName
    ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Product</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
        <!-- Sidebar and Topnav -->
        <?php include 'partial/app-sidebar.php'; ?>
        <div class="main">
            <?php include 'partial/app-topnav.php'; ?>

            <div class="content-container">
                <?php
                $errMsg = "";
                $permissions = $_SESSION['permissions'];
                $permission_values = explode(',', $permissions[0]['permissions']);
                if (in_array("purchase-create", $permission_values)) { ?>
                    <div class="add-user">
                        <h3><i class="fas fa-user-plus"></i> Order Product</h3>

                        <form method="POST" action="public/Database/add.php" id="orderForm">

                            <div class="form-box" style="padding-bottom:5%"><button type="button" class="formBtn" id="addProductBtn">Add Another Product</button>
                                <input type="hidden" name="form_name" value="Product-order">
                                <br><br>
                                <ul id="productList">
                                    <li class="product-row">
                                        <div class="">
                                            <label for="product_id" style="display:inline">PRODUCT NAME: </label>
                                            <select name="product_id[]" class="product-select" id="productBind" required>
                                                <option value="">Select Product</option>
                                                <?php foreach ($products as $product):
                                                ?>
                                                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                                                <?php endforeach;
                                                ?>
                                            </select>
                                            <button class="formBtn btnRemoveProduct">Remove</button>
                                        </div>
                                        <div class="supplier-container">
                                            <!--supplier will bind here -->
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <input type="submit" class="formBtn submitBtn" style="margin-right:10px" value="Submit Order">
                        </form>
                        <?php
                        if (isset($_SESSION['error'])) {
                            $error = $_SESSION['error'];
                            echo "<div id='errMsg'>$error</div>";
                            unset($_SESSION['error']);
                        }
                        ?>
                    </div>
                <?php } else {
                    $errMsg = "You Do not have permission to Purchase an order.";
                }

                if ($errMsg != "") {
                    echo "<div id='errMsg'>$errMsg</div>";
                };
                ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const productSupplierMap = <?php echo json_encode($productSupplierMap); ?>;

        let globalIndex = 0; // define at top if needed

        function bindProductSelect($context) {
            $context.find('.product-select').off('change').on('change', function() {
                const selectedId = $(this).val();
                const suppliers = productSupplierMap[selectedId] || [];

                const $row = $(this).closest('.product-row');
                const $container = $row.find('.supplier-container');

                $container.empty();
                console.log(suppliers);
                if (suppliers.length > 0) {
                    suppliers.forEach(supplier => {
                        const html = `
                        <div class="sub-product-row row-bottom">
                            <div>
                                <div class="supplier-display">${supplier.name}</div>
                            </div>
                            <div style="width: 40%;" class="supplier-inputs">
                                <label>QUANTITY:</label>
                                <input type="number" name="items[${globalIndex}][quantity]" required placeholder="Enter quantity">
                                <input type="hidden" name="items[${globalIndex}][supplier_id]" value="${supplier.id}">
                                <input type="hidden" name="items[${globalIndex}][product_id]" value="${selectedId}">
                            </div>
                        </div>`;
                        $container.append(html);
                        globalIndex++;
                    });
                } else {
                    $container.append(`
                <div class="sub-product-row row-bottom">
                    <div>No supplier found.</div>
                </div>
            `);
                }
            });
        }

        // Bind initial product row
        bindProductSelect($('.product-row'));

        // Handle Add Product button
        $('#addProductBtn').on('click', function() {
            const $lastRow = $('#productList .product-row').last();
            const $clone = $lastRow.clone();
            const $errMsg = $('#errMsg');
            $errMsg.empty();
            // Add margin to the clone row
            $clone.css({
                'margin-top': '10px'
            });

            $lastRow.append(`<div id="DivHR"></div>`);
            // console.log($clone);
            // Clear inputs in the cloned row
            $clone.find('select').val('');
            $clone.find('.supplier-container').empty();

            // Append the clone
            $('#productList').append($clone);

            // Re-bind events to the new cloned row
            bindProductSelect($clone);
        });

        $('#productList').on('click', '.btnRemoveProduct', function() {
            if ($('#productList .product-row').length > 1) {
                $(this).closest('.product-row').remove();
            } else {
                alert("At least one product row is required.");
            }
        });
    </script>
    <script src="public/js/script.js"></script>
</body>

</html>