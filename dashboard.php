<?php
session_start();
include('public/Database/connection.php');

$user = $_SESSION['username'] ?? null;
if (!$user) {
    header("location: login.php");
    exit();
}

echo "<script>console.log('User logged in dashboard page: " . $user . "');</script>";

// === Bar Chart: Product Count by Supplier ===
$query = "SELECT * FROM supplier";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$categories = [];
$bar_chart_count = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $categories[] = $row['supplier_name'];

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS p_count FROM productsupplier WHERE supplier = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $bar_chart_count[] = $count;
}

// === Pie Chart: Order Status Count ===
$statuses = ['Pending', 'Completed', 'Incomplete'];
$results = [];

foreach ($statuses as $status) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS count FROM order_product WHERE status = ?");
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $results[$status] = $count;
}

$chartData = [];
foreach ($results as $status => $count) {
    $chartData[] = [
        'name' => $status,
        'y'    => $count
    ];
}


// Line chart data
$line_data = [];
$query2 = "SELECT qty_received, date_received from order_product_history order by date_received ASC";
$result2 = mysqli_query($conn, $query2);

if (!$result2) {
    die("Query failed: " . mysqli_error($conn));
} else {
    while ($row = mysqli_fetch_assoc($result2)) {
        // print_r($row);
        $key = date('Y-m-d', strtotime($row['date_received']));
        $line_data[$key] = (int)$row['qty_received'];
    }
    $line_categories = array_keys($line_data);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>IMS Dashboard</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://kit.fontawesome.com/dfec668964.js" crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script>
</head>

<body>
    <div class="container">
        <?php include 'partial/app-sidebar.php'; ?>
        <div class="main">
            <?php include 'partial/app-topnav.php'; ?>
            <?php
            include('public/Database/showData.php');
            $permissions = $_SESSION['permissions'];
            $permission_values = explode(',', $permissions[0]['permissions']);
            if (in_array("dasboard-view", $permission_values)) { ?>

                <div class="content-container dashboard-container">
                    <!-- Pie Chart -->
                    <div class="sub-dashboard-container">
                        <div id="container1" style="height: 220px;"></div>
                        <p class="highcharts-description">
                            This pie chart shows the distribution of purchase orders by status.
                        </p>
                    </div>
                    <!-- Bar Chart -->
                    <div class="sub-dashboard-container">
                        <div id="container2" style="height:220px;"></div>
                        <p class="highcharts-description">
                            This bar chart shows the number of products assigned to each supplier.
                        </p>
                    </div>
                </div>
                <div class="dashboard-container-last" style="height: 200px; width:100%">
                    <figure class="highcharts-figure">
                        <div id="container3" style="height:230px;"></div>
                        <!-- <p class="highcharts-description">
                        Basic line chart showing trends in a dataset. This chart includes the
                        <code>series-label</code> module, which adds a label to each line for
                        enhanced readability.
                    </p> -->
                    </figure>
                </div>
            <?php } else {
                $errMsg = "You Do not have permission to view Dashboard.";
            }

            echo "<div id='errMsg'>$errMsg</div>";
            ?>
        </div>
    </div>

    <script src="public/js/script.js"></script>

    <!-- Pie Chart Script -->
    <script>
        Highcharts.chart('container1', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Purchase Orders by Status'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y} ({point.percentage:.1f}%)'
                    }
                }
            },
            series: [{
                name: 'Orders',
                colorByPoint: true,
                data: <?= json_encode($chartData) ?>
            }]
        });
    </script>

    <!-- Bar Chart Script -->
    <script>
        Highcharts.chart('container2', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Product Count Assigned to Supplier'
            },
            xAxis: {
                categories: <?= json_encode($categories) ?>,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Number of Products'
                }
            },
            tooltip: {
                valueSuffix: ' Products'
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Suppliers',
                data: <?= json_encode($bar_chart_count) ?>
            }]
        });
    </script>

    <!-- Line Chart Script -->
    <script>
        Highcharts.chart('container3', {
            title: {
                text: 'Delivery History per Day',
                align: 'left'
            },

            subtitle: {
                text: 'By Job Category. Source: <a href="https://irecusa.org/programs/solar-jobs-census/" target="_blank">IREC</a>.',
                align: 'left'
            },

            yAxis: {
                title: {
                    text: 'Product Delivered'
                }
            },

            xAxis: {
                categories: <?= json_encode(array_keys($line_data)) ?>,
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            series: [{
                name: 'Received Quantity',
                data: <?= json_encode(array_values($line_data)) ?>
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
    </script>

</body>

</html>