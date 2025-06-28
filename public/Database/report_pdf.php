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
    'supplier' => 'Supplier Report'
];
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
if ($type === "supplier") {
    $header = [
        'id' => ['width' => 15],
        'supplier_name',
        'supplier_location',
        'email',
        'created_by',
        'created_at',
        'updated_at'
    ];
    $supplier_data = $_SESSION['supplier_data'];
    $user_name = $_SESSION['username'];
    $data = [];
    foreach ($supplier_data as $supplier) {
        $supplier['created_by'] = $user_name;
        $data[] = [
            $supplier['id'],
            $supplier['supplier_name'],
            $supplier['supplier_location'],
            $supplier['email'],
            $supplier['created_by'],
            date('M d, Y h:i:s A', strtotime($supplier['created_at'])),
            date('M d, Y h:i:s A', strtotime($supplier['updated_at'])),
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
