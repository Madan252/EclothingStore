<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: Adminlogin.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eclothingstore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Dashboard Stats
$res = mysqli_query($con, "SELECT COUNT(*) AS total FROM product");
$totalProducts = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) AS total FROM user WHERE deleted_at IS NULL");
$totalUsers = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT COUNT(*) AS total FROM customer_order WHERE order_status='pending'");
$pendingOrders = mysqli_fetch_assoc($res)['total'] ?? 0;

$res = mysqli_query($con, "SELECT SUM(total) AS total FROM orderdetail");
$totalRevenue = mysqli_fetch_assoc($res)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Clothing Store Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

    <?php include '../Sidebar/Header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <h1>Dashboard Overview</h1>

        <div class="stats-container">
            <div class="stat-box">
                <i class="fas fa-box stat-icon"></i>
                <h3>Total Products</h3>
                <p><?php echo $totalProducts; ?></p>
            </div>
            <div class="stat-box">
                <i class="fas fa-users stat-icon"></i>
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stat-box">
                <i class="fas fa-hourglass-half stat-icon"></i>
                <h3>Pending Orders</h3>
                <p><?php echo $pendingOrders; ?></p>
            </div>
            <div class="stat-box">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>
    </main>
 

        <?php include '../Sidebar/Footer.php'; ?>

     
</body>
</html>
