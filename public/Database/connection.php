<?php
$host = 'localhost';
$userName = 'root';
$pass = '';
$db = 'inventory';

$conn = new mysqli($host, $userName, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
