<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['action'])) {
    die("Invalid request.");
}

$product_id = (int) $_GET['id'];
$action = $_GET['action'];

// Check if product exists in cart
if (!isset($_SESSION['cart'][$product_id])) {
    die("Product not found in cart.");
}

// Fetch available quantity from the database
$con = mysqli_connect("localhost", "root", "", "eclothingstore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql = "SELECT quantity FROM product WHERE id = $product_id";
$res = mysqli_query($con, $sql);

if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $available_quantity = (int) $row['quantity'];
} else {
    mysqli_close($con);
    die("Product not found in database.");
}

$current_quantity = $_SESSION['cart'][$product_id]['quantity'];

// Increase or decrease with validation
if ($action === "increase") {
    if ($current_quantity < $available_quantity) {
        $_SESSION['cart'][$product_id]['quantity']++;
    }
} elseif ($action === "decrease") {
    if ($current_quantity > 1) {
        $_SESSION['cart'][$product_id]['quantity']--;
    }
}

mysqli_close($con);
header("Location: cart.php");
exit();
