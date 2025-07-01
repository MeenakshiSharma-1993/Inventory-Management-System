<?php
session_start();
include './public/Database/connection.php';

// Access control
$user = $_SESSION['username'];
$user_id = $_SESSION['user_Id'];
if (!isset($user)) {
    header("location: login.php");
    exit();
} else {
    echo "<script>console.log('User logged in dashboard page: " . $user . "');</script>";
}

// DELETE user
include './public/Database/delete.php';

// UPDATE user
include './public/Database/update.php';

// Fetch orders joined with product names
$sql = "
  SELECT o.id AS order_id, s.supplier_name, o.product, p.id as product_Id, p.product_name, o.quantity_ordered,
         o.quantity_received, o.quantity_remaining, o.status, o.created_by, 
         o.created_at, o.updated_at, o.batch 
        FROM order_product AS o 
        JOIN products AS p ON FIND_IN_SET(p.id, o.product)
        JOIN supplier AS s ON s.id = o.supplier where o.created_by = $user_id 
        ORDER BY o.batch DESC, o.created_at DESC ;
";
$result = mysqli_query($conn, $sql);

// Group by batch + order
$batches = [];

while ($r = mysqli_fetch_assoc($result)) {
    $b = $r['batch'];
    $oid = $r['order_id'];
    if (!isset($batches[$b][$oid])) {
        $batches[$b][$oid] = [
            'batch'           => $b,
            'order_id'           => $oid,
            'supplierName'       => $r['supplier_name'],
            'product_name'        => $r['product'],
            'product_id'        => $r['product_Id'],
            'product_names'      => [],
            'product_name'        => $r['product_name'],
            'quantity_ordered'   => $r['quantity_ordered'],
            'quantity_received'  => $r['quantity_received'],
            'quantity_remaining' => $r['quantity_remaining'],
            'status'             => $r['status'],
            'created_by'         => $r['created_by'],
            'created_at'         => $r['created_at'],
            'updated_at'         => $r['updated_at'],
        ];
    }
    $batches[$b][$oid]['product_names'][] = $r['product_name'];
}
// History from order_product_history

$historyData = [];

$historyQuery = "SELECT * FROM order_product_history ORDER BY date_received DESC";
$resultHistory = mysqli_query($conn, $historyQuery);

while ($historyRow = mysqli_fetch_assoc($resultHistory)) {
    $oid = $historyRow['order_product_id'];
    $historyData[$oid][] = $historyRow;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Orders</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <?php include 'partial/app-sidebar.php'; ?>
        <div class="main">
            <?php include 'partial/app-topnav.php'; ?>
            <div class="content-container">
                <?php
                $errMsg = "";
                $permissions = $_SESSION['permissions'];
                $permission_values = explode(',', $permissions[0]['permissions']);
                if (in_array("purchase-view", $permission_values)) { ?>
                    <!-- Edit Order Modal -->
                    <div id="modal" style="display:none;">
                        <div class="add-user" style="background:#fff; padding:20px; border:1px solid #000">
                            <h3>Edit Order</h3>
                            <form method="POST" class="form-box">
                                <input type="hidden" name="order_id" id="editId">
                                <table class="modal-edit-table">
                                    <thead>
                                        <tr>
                                            <th>Supplier</th>
                                            <th>Product Name</th>
                                            <th>Qty Ordered</th>
                                            <th>Qty Delivered</th>
                                            <th>Qty Received</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modal-table-body">
                                        <!--Genrate Row through JS -->
                                    </tbody>
                                </table>
                                <div style="margin-top:15px;">
                                    <input class="formBtn" name="update_order" type="submit" value="Update All">
                                    <input class="formBtn" id="cancelBtn" type="button" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- View Product delivery history -->
                    <div id="modal2" style="display:none;">
                        <div class="add-user" style="background:#fff; padding:20px; border:1px solid #000">
                            <h3>Delivery History</h3>
                            <form method="POST" class="form-box">
                                <table class="modal-edit-table">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Date Received</th>
                                            <th>Qty Received</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modal2-table-body">
                                        <!--Genrate Row through JS -->
                                    </tbody>
                                </table>
                                <div style="margin-top:15px;">
                                    <input class="formBtn" id="cancelBtnHistory" type="button" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List of Orders -->
                    <div class="list-order">
                        <div class="add-user">
                            <h3><i class="fas fa-list"></i> List of Purchase Orders</h3>
                        </div>
                        <?php if (!empty($batches)): ?>
                            <?php foreach ($batches as $batchKey => $orders): ?>
                                <h4 id="view-orders-h4" style="margin-top:20px;">Batch#: <?= htmlspecialchars($batchKey) ?></h4>
                                <table id="order-view" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Supplier</th>
                                            <th>Product(s)</th>
                                            <th>Qty Ordered</th>
                                            <th>Qty Received</th>
                                            <th>Qty Remaining</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $o): ?>
                                            <tr>
                                                <td><?= $o['order_id'] ?></td>
                                                <td><?= htmlspecialchars($o['supplierName']) ?></td>
                                                <td><?= htmlspecialchars(implode(', ', $o['product_names'])) ?></td>
                                                <td><?= $o['quantity_ordered'] ?></td>
                                                <td><?= $o['quantity_received'] ?></td>
                                                <td><?= $o['quantity_remaining'] ?></td>
                                                <td><span class="order-status-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span></td>
                                                <td><?= date('m, d, y @ h:i:s A', strtotime($o['created_at'])) ?></td>
                                                <td><?= date('m, d, y @ h:i:s A', strtotime($o['updated_at'])) ?></td>
                                                <td>
                                                    <!-- <a href="?delete_order=<?= $o['order_id'] ?>" class="delete-btn"
                                                    onclick="return confirm('Delete Order ID <?= $o['order_id'] ?>?');">
                                                    <i class="fa fa-trash"></i>Delete
                                                </a> -->
                                                    <a href="#" data-id="<?= $o['order_id'] ?>" class="history-btn"><i class="fa-solid fa-clock-rotate-left"></i> Delivery History</a>


                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php
                                if (in_array("purchase-edit", $permission_values)) {
                                ?>
                                    <button type="submit" data-id="<?= $o['order_id'] ?>"
                                        data-batch="<?= $o['batch'] ?>"
                                        data-supplier="<?= htmlspecialchars($o['supplierName'], ENT_QUOTES) ?>"
                                        data-product="<?= htmlspecialchars($o['product_name'], ENT_QUOTES) ?>"
                                        data-quantity="<?= $o['quantity_ordered'] ?>"
                                        data-status="<?= htmlspecialchars($o['status'], ENT_QUOTES) ?>" class="edit-btn formBtn orderBtn ">Update</button>
                                <?php } ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No orders found.</p>
                        <?php endif; ?>
                    </div>

                    <?php
                    if (isset($_SESSION['error'])) {
                        $error = $_SESSION['error'];
                        if ($error != "") {
                            echo "<div id='errMsg'>$error</div>";
                            unset($_SESSION['error']);
                        }
                    }
                    ?>
                <?php } else {
                    $errMsg = "You Do not have permission to view Purchase orders.";
                }

                if ($errMsg != "") {
                    echo "<div id='errMsg'>$errMsg</div>";
                }
                ?>

            </div>
        </div>
    </div>

    <script src="public/js/script.js"></script>
    <script>
        const batches = <?= json_encode($batches) ?>;
        const o_history_detail = <?= json_encode($historyData) ?>;
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                // e.preventDefault();
                const batchKey = btn.dataset.batch;
                const orders = batches[batchKey];
                const tableBody = document.getElementById('modal-table-body');
                // document.getElementById('batchId').value = batchKey;
                tableBody.innerHTML = '';

                for (const oid in orders) {
                    const order = orders[oid];
                    const quantity_ordered = order.quantity_ordered;
                    const product_id = order.product_id;
                    const quantity_received = order.quantity_received === "" || order.quantity_received === null ? "0" : order.quantity_received;
                    const selectedPending = order.status === "Pending" ? "selected" : "";
                    const selectedCompleted = order.status === "Completed" ? "selected" : "";
                    const selectedInCompleted = order.status === "Incomplete" ? "selected" : "";
                    tableBody.innerHTML += `
                        <input type="hidden" name="order_ids[]" value="${order.order_id}">
                        <tr>
                            <td><label style="color: #494848 !important;text-align: center !important;">${order.supplierName}</label></td>
                            <td><label style="color: #494848 !important;text-align: center !important;">${order.product_name}</label> </td>
                            <td><label style="color: #494848 !important;text-align: center !important;">${order.quantity_ordered}</label></td>
                            <input type="hidden" name="quantity_ordered[]" value="${order.quantity_ordered}">
                            <input type="hidden" name="quantity_delivered[]" value="${quantity_received}">
                            <input type="hidden" name="product_id[]" value="${product_id}">
                            <td><label style="color: #494848 !important;text-align: center !important;">${quantity_received}</label></td>
                            <td><input name="quantity_received[]" type="number" value="0" ></td>
                            <td style="width: 100px;">
                                <select style="padding: 3%;" name="statuses[]" required> 
                                    <option value="Pending" ${selectedPending}>Pending</option>
                                    <option value="Completed" ${selectedCompleted}>Completed</option>
                                    <option value="Incomplete" ${selectedInCompleted}>Incompleted</option>
                                </select>
                            </td>
                        </tr>`;
                }
                document.getElementById('modal').style.display = 'flex';
            });
        });
        document.querySelectorAll('.history-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const orderId = btn.dataset.id;

                const tableBody = document.getElementById('modal2-table-body');
                tableBody.innerHTML = '';

                if (o_history_detail[orderId]) {
                    o_history_detail[orderId].forEach((entry, index) => {
                        tableBody.innerHTML += `<tr>
                                                    <td>${index + 1}</td>
                                                    <td>${entry.date_received}</td>
                                                    <td>${entry.qty_received}</td>
                                                </tr>`;
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="3">No delivery history found.</td></tr>`;
                }

                document.getElementById('modal2').style.display = 'flex';
            });
        });


        document.getElementById('cancelBtn').addEventListener('click', () => {
            document.getElementById('modal').style.display = 'none';
        });
        document.getElementById('cancelBtnHistory').addEventListener('click', () => {
            document.getElementById('modal2').style.display = 'none';
        });

        document.getElementById('modal').addEventListener('click', e => {
            if (e.target === document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none';
            }
        });
        document.getElementById('modal2').addEventListener('click', e => {
            if (e.target === document.getElementById('modal2')) {
                document.getElementById('modal2').style.display = 'none';
            }
        });
    </script>
</body>

</html>