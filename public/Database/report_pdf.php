<?php
session_start(); // Required to access $_SESSION
include 'showData.php';
require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    function __construct()
    {
        parent::__construct('L');
    }
    function FancyTable($headers, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // // Column widths
        // $w = array(10, 45, 50, 20, 40, 55, 55);

        // // Header
        // for ($i = 0; $i < count($header); $i++) {
        //     $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        // }
        // $this->Ln();

        $width_sum = 0;
        $header_keys = array_keys($headers);
        $values = array_values($headers);

        for ($i = 0; $i < count($headers); $i++) {
            $this->Cell($values[$i]['width'], 7, $header_keys[$i], 1, 0, 'C', true);
            $width_sum += $values[$i]['width'];
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');

        $img_pos_y = $this->GetY();
        foreach ($data as $row) {
            foreach ($header_keys as $header_key) {
                $content = $row[$header_key]['content'];
                $width = $headers[$header_key]['width'];
                $align = $row[$header_key]['align'];

                if ($header_key == 'img') {
                    $content = is_null($content) || $content === '' ? 'No Image' :
                        $this->Image('../uploads/' . $content, 30, $img_pos_y + 2, 25, 25);
                }
                $this->Cell($width, 30, $content, 'LRTB', 0, $align);
            }
            $this->Ln();
            $img_pos_y += 30;
        }

        // Data
        // foreach ($data as $row) {
        //     $y = $this->GetY(); // Current row Y

        //     // Column 1: ID or serial number
        //     $this->Cell($w[0], 30, $row[0], 'LRBT', 0, 'C');

        //     // Column 2: Image or "No Image"
        //     if (!empty($row[1]) && file_exists('.././uploads/' . $row[1])) {
        //         $this->Cell($w[1], 30, '', 'LRBT', 0, 'C'); // Empty cell for structure
        //         $this->Image('.././uploads/' . $row[1], 30, $y + 2, 25, 25); // Position image inside the previous cell
        //     } else {
        //         // Display "No Image" in cell
        //         $this->Cell($w[1], 30, 'No Image', 'LRBT', 0, 'C');
        //     }

        //     // Remaining columns
        //     $this->Cell($w[2], 30, $row[2], 'LRBT', 0, 'C');
        //     $this->Cell($w[3], 30, $row[3], 'LRBT', 0, 'C');
        //     $this->Cell($w[4], 30, $row[4], 'LRBT', 0, 'C');
        //     $this->Cell($w[5], 30, $row[5], 'LRBT', 0, 'C');
        //     $this->Cell($w[6], 30, $row[6], 'LRBT', 0, 'C');

        //     $this->Ln(); // Move to next row
        // }       // Closing line
        $this->Cell($width_sum, 0, '', 'T');
    }
}
$pdf = new PDF();
$type = $_GET['report'];
$report_header = [
    'product' => 'Product Report',
    'supplier' => 'Supplier Report',
    'deliveries' => 'Delivery Report',
    'purchase_order' => 'Purchase Order Report',
];
//Export Product Pdf
if ($type === "product") {
    $header = [
        'id' => ['width' => 10],
        'img' => ['width' => 45],
        'product_name' => ['width' => 50],
        'stock' => ['width' => 20],
        'created_by' => ['width' => 40],
        'created_at' => ['width' => 55],
        'updated_at' => ['width' => 55]
    ];
    $product_data = $_SESSION['product_data'];
    $user_name = $_SESSION['username'];
    $data = [];
    foreach ($product_data as $product) {
        $product['created_by'] = $user_name;
        // $data[] = [
        //     $product['id'],
        //     $product['img'],
        //     $product['product_name'],
        //     // $product['description'],
        //     $product['stock'],
        //     $product['created_by'],
        //     date('M d, Y h:i:s A', strtotime($product['created_at'])),
        //     date('M d, Y h:i:s A', strtotime($product['updated_at'])),
        // ];
        $data[] = [
            'id' => [
                'content' => $product['id'],
                'align' => 'C'
            ],
            'img' => [
                'content' => $product['img'],
                'align' => 'C'
            ],
            'product_name' => [
                'content' => $product['product_name'],
                'align' => 'C'
            ],
            'stock' => [
                'content' => number_format($product['stock']),
                'align' => 'C'
            ],
            'created_by' => [
                'content' => $product['created_by'],
                'align' => 'L'
            ],
            'created_at' => [
                'content' => date('M d, Y h:i:s A', strtotime($product['created_at'])),
                'align' => 'L'
            ],
            'updated_at' => [
                'content' => date('M d, Y h:i:s A', strtotime($product['updated_at'])),
                'align' => 'L'
            ]
        ];
    }

    // Generate PDF
    $pdf->SetFont('Arial', '', 20);
    $pdf->AddPage();
    $pdf->Cell(120);
    $pdf->Cell(30, 10, $report_header[$type], 0, 0, 'C');
    $pdf->SetFont('Arial', '', 10);

    $pdf->Ln();
    $pdf->Ln();
    $pdf->FancyTable($header, $data);
    $pdf->Output();
}
//Export supplier Pdf
if ($type === "supplier") {
    $header = [
        'id' => ['width' => 10],
        'supplier_name' => ['width' => 40],
        'supplier_location' => ['width' => 45],
        'email' => ['width' => 50],
        'created_by' => ['width' => 35],
        'created_at' => ['width' => 50],
        'updated_at' => ['width' => 50]
    ];
    $supplier_data = $_SESSION['supplier_data'];
    $user_name = $_SESSION['username'];
    $data = [];
    foreach ($supplier_data as $supplier) {
        $supplier['created_by'] = $user_name;
        $data[] = [
            'id' => [
                'content' => $supplier['id'],
                'align' => 'C'
            ],
            'supplier_name' => [
                'content' => $supplier['supplier_name'],
                'align' => 'C'
            ],
            'supplier_location' => [
                'content' => $supplier['supplier_location'] == "" ? "N/A" : $supplier['supplier_location'],

                'align' => 'C'
            ],
            'email' => [
                'content' => $supplier['email'],
                'align' => 'C'
            ],
            'created_by' => [
                'content' => $supplier['created_by'],
                'align' => 'L'
            ],
            'created_at' => [
                'content' => date('M d, Y h:i:s A', strtotime($supplier['created_at'])),
                'align' => 'L'
            ],
            'updated_at' => [
                'content' => date('M d, Y h:i:s A', strtotime($supplier['updated_at'])),
                'align' => 'L'
            ]
        ];
    }

    // Generate PDF
    $pdf->SetFont('Arial', '', 20);
    $pdf->AddPage();
    $pdf->Cell(120);
    $pdf->Cell(30, 10, $report_header[$type], 0, 0, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->FancyTable($header, $data);
    $pdf->Output();
}
//Export deliveries Pdf
if ($type === "deliveries") {
    $header = [
        'date_received' => ['width' => 65],
        'qty_received' => ['width' => 50],
        'product_name' => ['width' => 50],
        'supplier_name' =>['width' => 50],
        'created_by' => ['width' => 55],
    ];
    $query = "select date_received,qty_received,products.product_name AS product_name,supplier_name 
                from order_product_history,order_product,supplier,products where order_product_history.order_product_id=order_product.id
                and order_product.supplier=supplier.id
                and order_product.product=products.id
                order by order_product.batch desc;";
    $deliveries = mysqli_query($conn, $query);

    $user_name = $_SESSION['username'];
    $data = [];
    foreach ($deliveries as $delivery) {
        $delivery['created_by'] = $user_name;
        $data[] = [
            'date_received' => [
                'content' =>  date('M d, Y h:i:s A', strtotime($delivery['date_received'])),
                'align' => 'C'
            ],
            'qty_received' => [
                'content' => $delivery['qty_received'],
                'align' => 'C'
            ],
            'product_name' => [
                'content' => $delivery['product_name'],

                'align' => 'L'
            ],
            'supplier_name' => [
                'content' => $delivery['supplier_name'],
                'align' => 'L'
            ],
            'created_by' => [
                'content' => $delivery['created_by'],
                'align' => 'L'
            ]
        ];
    }

    // Generate PDF
    $pdf->SetFont('Arial', '', 20);
    $pdf->AddPage();
    $pdf->Cell(120);
    $pdf->Cell(30, 10, $report_header[$type], 0, 0, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->FancyTable($header, $data);
    $pdf->Output();
}
//Export deliveries Pdf
if ($type === "purchase_order") {
    $header = [
        'id' => ['width' => 10],
        'supplier_name' => ['width' => 30],
        'product_name' =>['width' => 28],
        'qty_ordered' => ['width' => 25],
        'qty_received' => ['width' => 25],
        'qty_remaining' => ['width' => 25],
        'status' => ['width' => 20],
        'created_by' => ['width' => 28],
        'created_at' => ['width' => 47],
        'updated_at' => ['width' => 47]
    ];
    $query = "SELECT o.id AS id, s.supplier_name, p.id as product_Id, p.product_name, o.quantity_ordered AS qty_ordered,
            o.quantity_received AS qty_received, o.quantity_remaining AS qty_remaining, o.status, o.created_by, o.created_at, o.updated_at, o.batch 
            FROM order_product AS o 
            JOIN products AS p ON FIND_IN_SET(p.id, o.product)
            JOIN supplier AS s ON s.id = o.supplier
            ORDER BY o.batch DESC, o.created_at DESC";

    $purchase_orders = mysqli_query($conn, $query);
    $user_name = $_SESSION['username'];
    $data = [];
    $id=0;
    foreach ($purchase_orders as $purchase_order) {
        $purchase_order['created_by'] = $user_name;
        $data[] = [
             'id' => [
                'content' => ++$id,
                'align' => 'C'
            ],
             'supplier_name' => [
                'content' => $purchase_order['supplier_name'],
                'align' => 'L'
            ],
             'product_name' => [
                'content' => $purchase_order['product_name'],
                'align' => 'L'
            ],
             'qty_ordered' => [
                'content' => $purchase_order['qty_ordered'],
                'align' => 'C'
            ],
             'qty_received' => [
                'content' => $purchase_order['qty_received'],
                'align' => 'C'
            ],
            'qty_remaining' => [
                'content' => $purchase_order['qty_remaining'],
                'align' => 'C'
            ],
             'status' => [
                'content' => $purchase_order['status'],
                'align' => 'L'
            ],
            'created_by' => [
                'content' => $purchase_order['created_by'],
                'align' => 'L'
            ],
            'created_at' => [
                'content' => date('M d, Y h:i:s A', strtotime($purchase_order['created_at'])),
                'align' => 'C'
            ],
            'updated_at' => [
                'content' => date('M d, Y h:i:s A', strtotime($purchase_order['updated_at'])),
                'align' => 'C'
            ]       
        ];
    }

    // Generate PDF
    $pdf->SetFont('Arial', '', 20);
    $pdf->AddPage();
    $pdf->Cell(120);
    $pdf->Cell(30, 10, $report_header[$type], 0, 0, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->FancyTable($header, $data);
    $pdf->Output();
}
