<?php
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

?>

<!DOCTYPE html>
<html>
   <head>
        <meta charset="utf-8">
        <title>Fruitables - Vegetable Website Template</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="../design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="../design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="../design-assets/design-assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="../design-assets/design-assets/css/style.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
<body>
<div class="container mt-5">
    <h2>Your Shopping Cart</h2>
    <?php if (empty($cart)): ?>
        <p class="text-muted">Your cart is empty.</p>
    <?php else: ?>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                foreach ($cart as $item): 
                    $total = $item['price'] * $item['quantity'];
                    $grandTotal += $total;
                ?>
                    <tr>
                        <td><img src="../assets/images/<?php echo $item['image']; ?>" width="70"></td>
                        <td><?php echo $item['name']; ?></td>
                        <td>$<?php echo $item['price']; ?></td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                            <a href="update_quantity.php?id=<?php echo $item['id']; ?>&action=decrease" class="btn btn-sm btn-outline-secondary me-2">-</a>
                                <?php echo $item['quantity']; ?>
                                <a href="update_quantity.php?id=<?php echo $item['id']; ?>&action=increase" class="btn btn-sm btn-outline-secondary ms-2">+</a>
                            </div>
                        </td>

                        <td>$<?php echo $total; ?></td>
                        <td><a href="remove_from_cart.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">X</a></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Grand Total</td>
                    <td colspan="2" class="fw-bold text-success">$<?php echo $grandTotal; ?></td>
                </tr>
            </tbody>
        </table>
        <a href="chackout.php" class="btn btn-success">Checkout</a>
    <?php endif; ?>
</div>
</body>
</html>
