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
    <title>E-Clothing Store </title>
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
            <a href=" index.php" class="nav-link active">Home</a>
             <a href=" aboutus.php" class="nav-link active">Aboutus</a>
            
             <a href="shop-detail.html" class="nav-item nav-link">Shop Detail</a>
            <a href="cart.html" class="dropdown-item">Cart</a>
              <a href="chackout.html" class="dropdown-item">Chackout</a>
             <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                    

              <a href="../Admin/logout.php" class="nav-link logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
        
    </header>

   

</body>
</html>