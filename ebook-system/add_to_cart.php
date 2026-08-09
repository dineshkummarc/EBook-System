<?php
session_start();
include 'db_config.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart.']);
    } else {
        header("Location: login.php?redirect=books.php");
    }
    exit();
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = htmlspecialchars(trim($_POST['title']    ?? ''));
    $price    = (float)($_POST['price']    ?? 0);
    $format   = htmlspecialchars(trim($_POST['format']   ?? 'PDF'));
    $shipping = (float)($_POST['shipping'] ?? 0);
    $weight   = (float)($_POST['weight']   ?? 0.5);
    $qty      = max(1, (int)($_POST['quantity'] ?? 1));

    if (empty($title)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid book title.']);
        } else {
            header("Location: books.php");
        }
        exit();
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$title])) {
        // Update quantity if already in cart
        $_SESSION['cart'][$title]['quantity'] += $qty;
    } else {
        // Add new item
        $_SESSION['cart'][$title] = [
            'price'    => $price,
            'format'   => $format,
            'shipping' => $shipping,
            'weight'   => $weight,
            'quantity' => $qty,
        ];
    }

    // Calculate total cart items
    $cartQty = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartQty += $item['quantity'];
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success'       => true,
            'message'       => "\"$title\" added to cart!",
            'added_title'   => $title,
            'cart_quantity' => $cartQty,
        ]);
    } else {
        $_SESSION['message'] = "\"$title\" has been added to your cart!";
        header("Location: cart.php");
    }
    exit();
}

// Not a POST request
header("Location: books.php");
exit();
