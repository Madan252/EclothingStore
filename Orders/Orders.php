<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "", "eclothingstore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch customer orders
$orderQuery = "SELECT id, user_id, order_by, order_status FROM customer_order";
$orderResult = mysqli_query($con, $orderQuery);

// Fetch summary stats
$totalOrdersResult = mysqli_query($con, "SELECT COUNT(*) AS total FROM customer_order");
$totalOrders = mysqli_fetch_assoc($totalOrdersResult)['total'] ?? 0;

$pendingOrdersResult = mysqli_query($con, "SELECT COUNT(*) AS total FROM customer_order WHERE order_status='pending'");
$pendingOrders = mysqli_fetch_assoc($pendingOrdersResult)['total'] ?? 0;

$paidOrdersResult = mysqli_query($con, "SELECT COUNT(*) AS total FROM customer_order WHERE order_status='paid'");
$paidOrders = mysqli_fetch_assoc($paidOrdersResult)['total'] ?? 0;

$uniqueUsersResult = mysqli_query($con, "SELECT COUNT(DISTINCT user_id) AS total FROM customer_order");
$uniqueUsers = mysqli_fetch_assoc($uniqueUsersResult)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Orders</title>
  <link rel="stylesheet" href="../assets/css/orders.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
      <?php include '../Sidebar/Header.php'; ?>
      <main class="main-content">
  <div class="orders-page">
    <header class="header">
      <h1><i class="fas fa-shopping-cart"></i> Customer Orders</h1>
    </header>

    <!-- Summary Boxes -->
    <div class="stats-container">
      <div class="stat-box">
        <i class="fas fa-receipt stat-icon"></i>
        <h3>Total Orders</h3>
        <p><?= $totalOrders ?></p>
      </div>
      <div class="stat-box">
        <i class="fas fa-hourglass-half stat-icon"></i>
        <h3>Pending Orders</h3>
        <p><?= $pendingOrders ?></p>
      </div>
      <div class="stat-box">
        <i class="fas fa-check-circle stat-icon"></i>
        <h3>Paid Orders</h3>
        <p><?= $paidOrders ?></p>
      </div>
      <div class="stat-box">
        <i class="fas fa-users stat-icon"></i>
        <h3>Unique Users</h3>
        <p><?= $uniqueUsers ?></p>
      </div>
    </div>

    <!-- Orders Table -->
    <section class="order-content">
      <?php if (mysqli_num_rows($orderResult) > 0): ?>
        <table class="order-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>User ID</th>
              <th>Order By</th>
              <th>Order Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($order = mysqli_fetch_assoc($orderResult)): ?>
              <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['user_id']) ?></td>
                <td><?= htmlspecialchars($order['order_by']) ?></td>
                <td><?= htmlspecialchars($order['order_status']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="no-orders">No orders found.</p>
      <?php endif; ?>
    </section>
  </div>
      </main>
       <?php include '../Sidebar/Footer.php'; ?>
</body>
</html>
