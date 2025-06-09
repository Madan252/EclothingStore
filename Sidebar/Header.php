<?php
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['email'])){
    header("Location: ../Adminlogin.php");
    exit();
}

$adminName = $_SESSION['admin_name'] ?? $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Clothing Store Admin</title>
    <link rel="stylesheet" href="../assets/css/Admindashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <!-- Top Navigation -->
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> E-Clothing Store
        </div>
        <nav class="topnav-menu">
            <a href="../Admin/Admindashboard.php" class="nav-link active">Home</a>
              <a href="../Admin/logout.php" class="nav-link logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
        <div class="welcome-msg">
            <i class="fas fa-user-circle"></i> Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="../Admin/Admindashboard.php" class="sidebar-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            
            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle">
                    <i class="fas fa-tags"></i> Products <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="../product/index.php" class="sidebar-sublink">Add Product</a></li>
                    <li><a href="../product/view_product.php" class="sidebar-sublink">View Products</a></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="sidebar-link dropdown-toggle">
                    <i class="fas fa-tags"></i> Categories <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="#" class="sidebar-sublink">Men</a></li>
                    <li><a href="#" class="sidebar-sublink">Women</a></li>
                    <li><a href="#" class="sidebar-sublink">Babies</a></li>
                </ul>
            </li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="../Orders/Orders.php" class="sidebar-link"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-clipboard-list"></i> Order Details</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>

</body>
</html>