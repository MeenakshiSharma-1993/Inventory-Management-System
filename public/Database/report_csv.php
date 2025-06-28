<?php
include 'showData.php';
include 'connection.php';
$type = $_GET['report'];
$file_name = ".xls";

$mapping_filenames = [
    'supplier' => 'Supplier Report',
    'product' => 'Product Report',
    'purchase_order' => 'Purchase Order Report',
    'deliveries' => 'Deliveries Report'
];
$file_name = $mapping_filenames[$type] . '.xls';
header("Content-Disposition: attachment; filename=\"$file_name\"");
header("content-type: application/vnd.ms-excel");

$user_name = $_SESSION['username'];

if ($type === "product") {
    $product_data = $_SESSION['product_data'];

    $is_header = true;
    foreach ($product_data as $product) {
        // unset($product['stock']); to remove any column in excel.
        $product['created_by'] = $user_name;
        if ($is_header) {
            echo implode("\t", array_keys($product)) . "\n";
            $is_header = false;
        }
        // Escape each value properly for Excel/TSV/CSV
        escape_value_for_excel($product);

        echo implode("\t", $product) . "\n";
    }
}
if ($type === "supplier") {
    $supplier_data = $_SESSION['supplier_data'];
    $is_header = true;
    foreach ($supplier_data as $supplier) {
        $supplier['created_by'] = $user_name;
        if ($is_header) {
            echo implode("\t", array_keys($supplier)) . "\n";
            $is_header = false;
        }
        // Escape each value properly for Excel/TSV/CSV
        escape_value_for_excel($supplier);
        echo implode("\t", $supplier) . "\n";
    }
}
if ($type === "purchase_order") {
    $query="SELECT o.id AS order_id, s.supplier_name, p.id as product_Id, p.product_name, o.quantity_ordered,
            o.quantity_received, o.quantity_remaining, o.status, o.created_by, o.created_at, o.updated_at, o.batch 
            FROM order_product AS o 
            JOIN products AS p ON FIND_IN_SET(p.id, o.product)
            JOIN supplier AS s ON s.id = o.supplier
            ORDER BY o.batch DESC, o.created_at DESC";
    $purchase_orders = mysqli_query($conn, $query);
    $is_header = true;
    foreach ($purchase_orders as $purchase_order) {
        $purchase_order['created_by'] = $user_name;
        if ($is_header) {
            echo implode("\t", array_keys($purchase_order)) . "\n";
            $is_header = false;
        }
        // Escape each value properly for Excel/TSV/CSV
        escape_value_for_excel($purchase_order);
        echo implode("\t", $purchase_order) . "\n";
    }
}

if ($type === "deliveries") {
    $query="";
    $purchase_orders = mysqli_query($conn, $query);
    $is_header = true;
    foreach ($purchase_orders as $purchase_order) {
        $purchase_order['created_by'] = $user_name;
        if ($is_header) {
            echo implode("\t", array_keys($purchase_order)) . "\n";
            $is_header = false;
        }
        // Escape each value properly for Excel/TSV/CSV
        escape_value_for_excel($purchase_order);
        echo implode("\t", $purchase_order) . "\n";
    }
}

function escape_value_for_excel($data)
{
    array_walk($data, function (&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"')) {
            $str = '"' . str_replace('"', '""', $str) . '"';
        }
    });
}
