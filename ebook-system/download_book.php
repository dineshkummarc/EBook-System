<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$user_id  = $_SESSION['user_id'];

if (!$order_id) {
    header("Location: view_orders.php");
    exit();
}

// Verify order belongs to this user and is paid
$result = mysqli_query($conn, "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id AND paid=1 LIMIT 1");

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['message'] = "Download not available. Please ensure your order is marked as paid.";
    header("Location: view_orders.php");
    exit();
}

$order = mysqli_fetch_assoc($result);

// Try to find the PDF file for the ordered book
// Match books from the items field
$items_str = $order['items'] ?? '';
// Extract first book title
preg_match('/^([^,\(]+)/', $items_str, $matches);
$bookTitle  = trim($matches[1] ?? '');
$bookTitle  = mysqli_real_escape_string($conn, $bookTitle);

$bookResult = mysqli_query($conn, "SELECT * FROM books WHERE title LIKE '%$bookTitle%' LIMIT 1");

if ($bookResult && mysqli_num_rows($bookResult) > 0) {
    $book    = mysqli_fetch_assoc($bookResult);
    $pdfPath = $book['pdf_path'] ?? '';

    if ($pdfPath && file_exists($pdfPath)) {
        $filename = basename($pdfPath);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: no-cache');
        readfile($pdfPath);
        exit();
    }
}

// PDF not found — redirect with error
$_SESSION['message'] = "PDF file not found for this order. Please contact support.";
header("Location: view_orders.php");
exit();
