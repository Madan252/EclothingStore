<?php
session_start();

// Validate and sanitize ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = (int) $_GET['id'];

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    // Connect to DB
    $con = mysqli_connect("localhost", "root", "", "eclothingstore");
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM product WHERE id = $product_id";
    $res = mysqli_query($con, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $product = mysqli_fetch_assoc($res);

        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => 1
        ];
    } else {
        die("Product not found.");
    }

    mysqli_close($con);
}

// Redirect back
header("Location: ../index.php");
exit();
