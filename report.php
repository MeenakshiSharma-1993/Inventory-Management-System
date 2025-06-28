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
    <title>Add User</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'partial/app-sidebar.php'; ?>
        <!-- Main Content -->
        <div class="main  report-main">
            <?php include 'partial/app-topnav.php'; ?>

            <div class="content-container report-container">
                <div class="sub-report-container">
                    <h3>Export Products </h3>
                    <div class="export-btn">
                        <a href="public/Database/report_csv.php?report=product" rel="noopener noreferrer" target="_blank" class="formBtn reportBtn">Excel</a>
                        <a href="public/Database/report_pdf.php?report=product" rel="noopener noreferrer" target="_blank" class="formBtn reportBtn">PDF</a>
                    </div>
                </div>
                <div class="sub-report-container">
                    <h3>Export Suppliers </h3>
                    <div class="export-btn">
                        <a href="public/Database/report_csv.php?report=supplier" class="formBtn reportBtn">Excel</a>
                        <a href="public/Database/report_pdf.php?report=supplier" class="formBtn reportBtn">PDF</a>
                    </div>
                </div>
            </div>
            <div class="content-container report-container">
                <div class="sub-report-container">
                    <h3>Export Deliveries </h3>
                    <div class="export-btn">
                        <a href="public/Database/report_csv.php?report=deliveries" class="formBtn reportBtn">Excel</a>
                        <a href="public/Database/report_pdf.php?report=deliveries" class="formBtn reportBtn">PDF</a>
                    </div>
                </div>
                <div class="sub-report-container">
                    <h3>Export Purchase Order </h3>
                    <div class="export-btn">
                        <a href="public/Database/report_csv.php?report=purchase_order" class="formBtn reportBtn">Excel</a>
                        <a href="public/Database/report_pdf.php?report=purchase_order" class="formBtn reportBtn">PDF</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
<script src="public/js/script.js"></script>

</html>