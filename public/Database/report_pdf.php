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
    function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');

        // Column widths
        $w = array(10, 45, 50, 20, 40, 55, 55);

        // Header
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');

        // Data
        foreach ($data as $row) {
            $y = $this->GetY(); // Current row Y

            // Column 1: ID or serial number
            $this->Cell($w[0], 30, $row[0], 'LRBT', 0, 'C');

            // Column 2: Image or "No Image"
            if (!empty($row[1]) && file_exists('.././uploads/' . $row[1])) {
                $this->Cell($w[1], 30, '', 'LRBT', 0,'C'); // Empty cell for structure
                $this->Image('.././uploads/' . $row[1], 30, $y + 2, 25, 25); // Position image inside the previous cell
            } else {
                // Display "No Image" in cell
                $this->Cell($w[1], 30, 'No Image', 'LRBT', 0, 'C');
            }

            // Remaining columns
            $this->Cell($w[2], 30, $row[2], 'LRBT', 0, 'C');
            $this->Cell($w[3], 30, $row[3], 'LRBT', 0, 'C');
            $this->Cell($w[4], 30, $row[4], 'LRBT', 0, 'C');
            $this->Cell($w[5], 30, $row[5], 'LRBT', 0, 'C');
            $this->Cell($w[6], 30, $row[6], 'LRBT', 0, 'C');

            $this->Ln(); // Move to next row
        }       // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
$pdf = new PDF();
$type = $_GET['report'];
$report_header = [
    'product' => 'Product Report',
    'supplier' => 'Supplier Report'
];
if ($type === "product") {
    $header = ['id', 'img', 'product_name',  'stock', 'created_by', 'created_at', 'updated_at'];
    $product_data = $_SESSION['product_data'];
    $user_name = $_SESSION['username'];
    $data = [];
    foreach ($product_data as $product) {
        $product['created_by'] = $user_name;
        $data[] = [
            $product['id'],
            $product['img'],
            $product['product_name'],
            // $product['description'],
            $product['stock'],
            $product['created_by'],
            date('M d, Y h:i:s A', strtotime($product['created_at'])),
            date('M d, Y h:i:s A', strtotime($product['updated_at'])),
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
