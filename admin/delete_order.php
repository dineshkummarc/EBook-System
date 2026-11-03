<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id) {
    mysqli_query($conn, "DELETE FROM orders WHERE id=$order_id");
    $_SESSION['admin_message'] = "Order #$order_id has been deleted.";
}

header("Location: view_orders.php");
exit();
