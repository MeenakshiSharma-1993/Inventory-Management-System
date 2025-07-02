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

$query = "SELECT product,supplier,amount,supplier_name FROM supplier AS s JOIN productsupplier AS ps ON s.id = ps.supplier join products as p on p.id=ps.product;";
$result = $conn->query($query);

// --- Build product-supplier map in PHP ---
$productSupplierMap = [];
while ($row = $result->fetch_assoc()) {
    $productId = $row['product'];
    $supplierId = $row['supplier']; // this is from productsupplier.supplier
    $amount = $row['amount'];
    $supplierName = $row['supplier_name'];
    if (!isset($productSupplierMap[$productId])) {
        $productSupplierMap[$productId] = [];
    }
    $productSupplierMap[$productId][] = [
        'id' => $supplierId,
        'name' => $supplierName,
        'amount' => $amount
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
                            
                            <div style="margin-top: 15px;">
                                <strong style="margin-top:10px">Total Amount: ₹ <span id="totalAmount">0.00</span></strong>
                                <a href="#" class="formBtn submitBtn payBtn" style="margin-top:0" id="payNowLink" target="_blank">Pay Now</a>
                                <input type="submit" class="formBtn submitBtn" style="margin-right:10px;margin-top:0" value="Submit Order">
                            </div>
                        </form>

                        <br>
                        <?php
                        if (isset($_SESSION['error'])) {
                            $error = $_SESSION['error'];
                            if ($error != "") {
                                echo "<div id='errMsg'>$error</div>";
                                unset($_SESSION['error']);
                            }
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

        let globalIndex = 0;

        function bindProductSelect($context) {
            $context.find('.product-select').off('change').on('change', function() {
                const selectedId = $(this).val();
                const suppliers = productSupplierMap[selectedId] || [];
                console.log(suppliers);

                const $row = $(this).closest('.product-row');
                const $container = $row.find('.supplier-container');

                $container.empty();

                if (suppliers.length > 0) {
                    suppliers.forEach(supplier => {
                        const html = `<br>
                    <div class="sub-product-row row-bottom">
                        <div>
                            <div class="supplier-display">${supplier.name}</div>
                        </div>
                        <div style="width: 50%;" class="supplier-inputs">
                            <div style="display:flex; gap: 5px;">
                                <input type="number" class="qty-input" name="items[${globalIndex}][quantity]" required placeholder="Enter quantity">
                                <input type="number" class="price-input" name="items[${globalIndex}][price]" value="${supplier.amount}" required placeholder="Enter price">
                                <input type="hidden" class="amount-input" name="items[${globalIndex}][amount]" value="${supplier.amount}">
                                <input type="hidden" name="items[${globalIndex}][supplier_id]" value="${supplier.id}">
                                <input type="hidden" name="items[${globalIndex}][product_id]" value="${selectedId}">
                            </div>
                        </div>
                    </div>`;
                        $container.append(html);
                        globalIndex++;
                    });

                    // Bind calculator for newly added supplier rows
                    bindAmountCalculation($container);
                } else {
                    $container.append(`
                    <div class="sub-product-row row-bottom">
                        <div>No supplier found.</div>
                    </div>
                `);
                }
            });
        }

        function bindAmountCalculation($context) {
            $context.find('.qty-input, .price-input').off('input').on('input', function() {
                const $inputs = $(this).closest('.supplier-inputs');
                const quantity = parseFloat($inputs.find('.qty-input').val()) || 0;
                const price = parseFloat($inputs.find('.price-input').val()) || 0;
                const amount = quantity * price;
                $inputs.find('.amount-input').val(amount.toFixed(2));

                updateTotalAmount();
            });
        }

        function updateTotalAmount() {
            let total = 0;
            $('.amount-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            const totalRounded = total.toFixed(2);
            $('#totalAmount').text(totalRounded);

            // Convert total to paise for Stripe (e.g., ₹30.50 → 3050)
            const amountInPaise = Math.round(total * 100);

            // Update payment link
            $('#payNowLink').attr('href', 'payment.php?totalAmount=' + amountInPaise);
        }

        // Bind initial row
        bindProductSelect($('.product-row'));
        bindAmountCalculation($('.product-row'));

        // Handle Add Product button
        $('#addProductBtn').on('click', function() {
            const $lastRow = $('#productList .product-row').last();
            const $clone = $lastRow.clone();
            $('#errMsg').empty();

            $clone.css({
                'margin-top': '10px'
            });
            $lastRow.append(`<div id="DivHR"></div>`);

            $clone.find('select').val('');
            $clone.find('.supplier-container').empty();

            $('#productList').append($clone);

            // Re-bind to new cloned row
            bindProductSelect($clone);
            bindAmountCalculation($clone);
        });

        // Handle Remove Product button
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