<?php
session_start();

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;


$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch top 10 bestselling products (by order quantity)

$bestsql = "SELECT p.*, SUM(od.quantity) as total_sold
        FROM product p
        JOIN orderdetail od ON p.id = od.product_id
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 10";

$result = mysqli_query($con, $bestsql);
$bestsellers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bestsellers[] = $row;
}

//$sql = "select * from product";
$sql = "SELECT p.*, c.name AS category_name 
        FROM product p
        LEFT JOIN category c ON p.category_id = c.id
        ORDER BY p.id ASC";
$res = mysqli_query($con, $sql);

// for new added 10 product

$sqlNew = "SELECT p.*, c.name AS category_name 
            FROM product p
            LEFT JOIN category c ON p.category_id = c.id
            -- WHERE p.created_at >= NOW() - INTERVAL 2 DAY
            ORDER BY p.created_at DESC LIMIT 10";

$resNew = mysqli_query($con, $sqlNew);

// Store New Products
$newProducts = [];

while ($rowNew = mysqli_fetch_assoc($resNew)) {
    $newProducts[] = $rowNew;
}

// Group products by category
$allProducts = [];
$categories = [];

while ($row = mysqli_fetch_assoc($res)) {
    $allProducts[] = $row;
    $categories[$row['category_name']][] = $row;
}

// Count total Users
$user_result = mysqli_query($con, "SELECT COUNT(*) AS total_users FROM user where deleted_at is null");
$user_row = mysqli_fetch_assoc($user_result);
$total_users = $user_row['total_users'];

// Count total products
$product_result = mysqli_query($con, "SELECT COUNT(*) AS total_products FROM product where deleted_at is null");
$product_row = mysqli_fetch_assoc($product_result);
$total_products = $product_row['total_products'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>E-Clothing Store</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="design-assets/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="design-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="design-assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="design-assets/css/style.css" rel="stylesheet">
     <link href="assets/css/view_product.css" rel="stylesheet">

    <style>
        /* Smooth transition for the entire card */
        .clothing-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #ddd;
            background-color: #fff;
        }

        /* Hover/Touch Effect for full card */
        .clothing-item:hover,
        .clothing-item:focus-within,
        .clothing-item:active {
            transform: scale(1.03);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        /* Optional: Prevent zoom affecting layout */
        .col-md-6.col-lg-4.col-xl-3 {
            overflow: hidden;
        }
    </style>


</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#"
                            class="text-white">Dhangadhi ,Kailali</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#"
                            class="text-white">user@gmail.com</a></small>
                </div>
                <div class="top-link pe-2">
                    <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                    <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                </div>
            </div>
        </div><br>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="index.php" class="navbar-brand">
                    <h2 class="text-primary display-6">Clothes</h2>
                    <h3 class="mb-3 text-secondary">Buy now pay Latter</h2>
                </a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <a href="index.php"
                            class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'index.php')
                                echo 'active'; ?>">Home</a>

                        <a href="user/our_shop.php"
                            class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'our_shop.php')
                                echo 'active'; ?>">Shop</a>

                        <a href="user/contact.php"
                            class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'contact.php')
                                echo 'active'; ?>">Contact</a>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="user/myorders.php"
                                class="nav-item nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'myorders.php')
                                    echo 'active'; ?>">My
                                Orders</a>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center gap-3">

                        <div class="d-flex align-items-center gap-3">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="user/Userlogin.php" class="btn btn-outline-dark">Login</a>
                            <?php else: ?>
                                <span class="text-dark fw-bold">Welcome,
                                    <?= htmlspecialchars($_SESSION['user_name']); ?></span>
                                <a href="user/logout.php" class="btn btn-danger">Logout</a>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex m-3 me-0">
                            <button id="searchToggle"
                                class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4">
                                <i class="fas fa-search text-primary"></i>
                            </button>

                            <!-- Search Input with Clear Button -->
                            <div id="searchWrapper" class="d-none position-relative">
                                <input type="text" id="searchBox" class="form-control ps-2 pe-5" style="width: 280px;">

                                <!-- Clear Button inside input -->
                                <button id="clearSearch" class="btn btn-sm btn-light border position-absolute"
                                    style="right: 5px; top: 50%; transform: translateY(-50%);">
                                    ✖
                                </button>
                            </div>
                            <a href="user/cart.php" class="position-relative me-4 my-auto">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                                <span
                                    class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                                    style="top: -5px; left: 15px; height: 20px; min-width: 20px;"><?php echo $cartCount; ?></span>
                            </a>
                            <a href="#" class="my-auto">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                        </div>
                    </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Modal Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="keywords"
                            aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7">
                    <h4 class="mb-3 text-secondary">100% Best Suitable Clothes</h4>
                    <h1 class="mb-5 display-3 text-primary">Brand new CLothes</h1>
                    <div class="position-relative mx-auto">
                        <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="number"
                            placeholder="Search">
                        <button type="submit"
                            class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100"
                            style="top: 0; right: 25%;">Submit Now</button>
                    </div>
                </div>
                <div class="col-md-12 col-lg-5">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active rounded">
                                <img src="design-assets/img/blackcoat.webp"
                                    class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                                <a href="#" class="btn px-4 py-2 text-white rounded">Men</a>
                            </div>
                            <div class="carousel-item rounded">
                                <img src="design-assets/img/Red-WeddingBridalGown.avif"
                                    class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                <a href="#" class="btn px-4 py-2 text-white rounded">Women</a>
                            </div>
                            <div class="carousel-item rounded">
                                <img src="design-assets/img/boykidsdress.jpg" class="img-fluid w-100 h-100 rounded"
                                    alt="Second slide">
                                <a href="#" class="btn px-4 py-2 text-white rounded">Babies</a>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Clothes Section Start -->
    <div class="container-fluid featurs py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-car-side fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Free Shipping</h5>
                            <p class="mb-0">Free on order over $300</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-user-shield fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Security Payment</h5>
                            <p class="mb-0">100% security payment</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-exchange-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>30 Day Return</h5>
                            <p class="mb-0">30 day money guarantee</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fa fa-phone-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>24/7 Support</h5>
                            <p class="mb-0">Support every time fast</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featurs Section End -->

    <!-- Clothes Shop Start-->
    <div class="container-fluid clothing py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>All Clothes</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill"
                                    href="#tab-all">
                                    <span class="text-dark" style="width: 130px;">All Products</span>
                                </a>
                            </li>
                            <?php foreach ($categories as $cat => $products) { ?>
                                <li class="nav-item">
                                    <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill"
                                        href="#tab-<?php echo strtolower(str_replace(' ', '-', $cat)); ?>">
                                        <span class="text-dark" style="width: 130px;"><?php echo $cat; ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <!-- Tab Content Start -->
                <div class="tab-content">

                    <!-- All Products -->
                    <div id="tab-all" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <?php if (count($allProducts) > 0) {
                                        foreach ($allProducts as $row) { ?>
                                            <div class="col-md-6 col-lg-4 col-xl-3">
                                                <div class="rounded position-relative clothing-item">
                                                    <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                        <img src="assets/images/<?php echo $row['image']; ?>"
                                                            class="img-fluid w-100 rounded-top" alt="">
                                                    </a>
                                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                        style="top: 10px; left: 10px;"><?php echo $row['category_name']; ?>
                                                    </div>
                                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                        <h4><?php echo $row['name']; ?></h4>
                                                        <p><?php echo $row['description']; ?></p>
                                                        <div class="d-flex justify-content-between flex-lg-wrap">
                                                            <p class="text-dark fs-5 fw-bold mb-0">Rs
                                                                <?php echo number_format($row['price'], 2); ?>
                                                            </p>

                                                            <?php if ($row['quantity'] > 0) { ?>
                                                                <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>"
                                                                    class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                                </a>
                                                            <?php } else { ?>
                                                                <button
                                                                    class="btn border border-secondary rounded-pill px-3 text-danger"
                                                                    disabled>
                                                                    Out of Stock
                                                                </button>
                                                            <?php } ?>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } else {
                                        echo "<p class='text-danger'>No products found.</p>";
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Specific Tabs -->
                    <?php foreach ($categories as $cat => $products) { ?>
                        <div id="tab-<?php echo strtolower(str_replace(' ', '-', $cat)); ?>" class="tab-pane fade show p-0">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="row g-4">
                                        <?php if (count($products) > 0) {
                                            foreach ($products as $row) { ?>
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="rounded position-relative clothing-item">
                                                        <a href="user/product_details.php?id=<?php echo $row['id']; ?>">
                                                            <img src="assets/images/<?php echo $row['image']; ?>"
                                                                class="img-fluid w-100 rounded-top" alt="">
                                                        </a>
                                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                            style="top: 10px; left: 10px;"><?php echo $row['category_name']; ?>
                                                        </div>
                                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                            <h4><?php echo $row['name']; ?></h4>
                                                            <p><?php echo $row['description']; ?></p>
                                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                                <p class="text-dark fs-5 fw-bold mb-0">Rs
                                                                    <?php echo number_format($row['price'], 2); ?>
                                                                </p>

                                                                <?php if ($row['quantity'] > 0) { ?>
                                                                    <a href="user/add_to_cart.php?id=<?php echo $row['id']; ?>"
                                                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <button
                                                                        class="btn border border-secondary rounded-pill px-3 text-danger"
                                                                        disabled>
                                                                        Out of Stock
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            <?php }
                                        } else {
                                            echo "<p class='text-danger'>Sorry, there is no product at the time.</p>";
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <!-- Tab Content End -->

            </div>
        </div>
    </div>
    <!-- Clothes Shop End-->


    <!-- schems Start -->
    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-secondary rounded border border-secondary">
                            <img src="design-assets/img/blazerformen.jpeg" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-primary text-center p-4 rounded">
                                    <h5 class="text-white">Blazer</h5>
                                    <h3 class="mb-0">20% OFF</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-dark rounded border border-dark">
                            <img src="design-assets/img/blackcoat.webp" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-light text-center p-4 rounded">
                                    <h5 class="text-primary">Brand clothes</h5>
                                    <h3 class="mb-0">Free delivery</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="#">
                        <div class="service-item bg-primary rounded border border-primary">
                            <img src="design-assets/img/blackgaun.webp" class="img-fluid rounded-top w-100" alt="">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content bg-secondary text-center p-4 rounded">
                                    <h5 class="text-white">Gown</h5>
                                    <h3 class="mb-0">Discount 30$</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Clothes End -->

    <!-- New Products Section -->

    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="mb-0">New Products</h1>
            <div class="row g-4 mt-4">

                <?php if (!empty($newProducts)) {
                    foreach ($newProducts as $product) { ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="rounded position-relative clothing-item">

                                <!-- Product Image -->
                                <a href="user/product_details.php?id=<?php echo $product['id']; ?>">
                                    <img src="assets/images/<?php echo $product['image']; ?>"
                                        class="img-fluid w-100 rounded-top"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>

                                <!-- Category Name -->
                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                    style="top: 10px; left: 10px;">
                                    <?php echo htmlspecialchars($product['category_name']); ?>
                                </div>

                                <!-- New Badge -->
                                <div class="text-white bg-danger px-3 py-1 rounded position-absolute"
                                    style="top: 10px; right: 10px;">
                                    New
                                </div>

                                <!-- Product Details -->
                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                    <!-- Name -->
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>

                                    <!-- Description -->
                                    <p><?php echo htmlspecialchars($product['description']); ?></p>

                                    <!-- Price and Add to Cart -->
                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                        <p class="text-dark fs-5 fw-bold mb-0">
                                            Rs <?php echo number_format($product['price'], 2); ?>
                                        </p>

                                        <?php if ($product['quantity'] > 0) { ?>
                                            <!-- Add to Cart Button Active -->
                                            <a href="user/add_to_cart.php?id=<?php echo $product['id']; ?>"
                                                class="btn border border-secondary rounded-pill px-3 text-primary">
                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                            </a>
                                        <?php } else { ?>
                                            <!-- Disabled Button when Out of Stock -->
                                            <button class="btn border border-secondary rounded-pill px-3 text-danger" disabled>
                                                Out of Stock
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php }
                } else {
                    echo "<p class='text-center'>No new products available.</p>";
                } ?>

            </div>
        </div>
    </div>
    <!-- New Products Section End -->



    <!-- Banner Section Start-->
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="py-4">
                        <h1 class="display-3 text-white">Elegent New Products</h1>
                        <p class="fw-normal display-3 text-dark mb-4">in Our Store</p>
                        <p class="mb-4 text-dark">One and Only shop that meets your dream's products and we assure that
                            you can feel after connecting with us like a paradise</p>
                        <a href="#"
                            class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">BUY</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="design-assets/img/banner-1.avif" class="img-fluid w-100 rounded" alt="" width="10">
                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute"
                            style="width: 140px; height: 140px; top: 0; left: 0;">
                            <h1 style="font-size: 100px;">1</h1>
                            <div class="d-flex flex-column">
                                <span class="h2 mb-0">50$</span>
                                <span class="h4 text-muted mb-0">piece</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Section End -->


    <!-- Bestsaler Product Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">Bestseller Products</h1>
                <p>Best Product For You</p>
            </div>
            <div class="row g-4">
                <?php foreach ($bestsellers as $product): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <a href="user/product_details.php?id=<?php echo $product['id']; ?>">
                                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                            class="img-fluid rounded-circle w-100"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </a>

                                </div>
                                <div class="col-6">
                                    <a href="user/product_details.php?id=<?php echo $product['id']; ?>"
                                        class="h5"><?php echo htmlspecialchars($product['name']); ?></a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">$<?php echo htmlspecialchars($product['price']); ?></h4>
                                    <a href="user/add_to_cart.php?id=<?php echo $product['id']; ?>"
                                        class="btn border border-secondary rounded-pill px-3 text-primary">
                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Bestsaler Product End -->

    <!-- Fact Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Satisfied Customers</h4>
                            <h1 class="count" data-target="<?php echo $total_users; ?>">0</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Quality of Service</h4>
                            <h1>99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Quality Certificates</h4>
                            <h1>33</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Available Products</h4>
                            <h1 class="count" data-target="<?php echo $total_products; ?>">0</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact End -->



    <!-- Tastimonial Start -->
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h4 class="text-primary">Our Testimonial</h4>
                <h1 class="display-5 mb-5 text-dark">Our Client Saying!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                <div class="testimonial-item img-border-radius bg-light rounded p-4">
                    <div class="position-relative">
                        <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                            style="bottom: 30px; right: 0;"></i>
                        <div class="mb-4 pb-4 border-bottom border-secondary">
                            <p class="mb-0"> After connecting E Clothing Store I have never buy the clothes from other
                                shops. It just Perfect , thank you!
                            </p>
                        </div>
                        <div class="d-flex align-items-center flex-nowrap">
                            <div class="bg-secondary rounded">
                                <img src="design-assets/img/customerboy.jpg" class="img-fluid rounded"
                                    style="width: 100px; height: 100px;" alt="">
                            </div>
                            <div class="ms-4 d-block">
                                <h4 class="text-dark">Sundar pichai</h4>
                                <p class="m-0 pb-3">CEO of google</p>
                                <div class="d-flex pe-5">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item img-border-radius bg-light rounded p-4">
                    <div class="position-relative">
                        <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                            style="bottom: 30px; right: 0;"></i>
                        <div class="mb-4 pb-4 border-bottom border-secondary">
                            <p class="mb-0">To connecting this E Clothing Store just like a paradise because best
                                products easily availabe and better hospitality
                            </p>
                        </div>
                        <div class="d-flex align-items-center flex-nowrap">
                            <div class="bg-secondary rounded">
                                <img src="design-assets/img/bts.webp" class="img-fluid rounded"
                                    style="width: 100px; height: 100px;" alt="">
                            </div>
                            <div class="ms-4 d-block">
                                <h4 class="text-dark"></h4>
                                <p class="m-0 pb-3">BTS</p>
                                <div class="d-flex pe-5">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item img-border-radius bg-light rounded p-4">
                    <div class="position-relative">
                        <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                            style="bottom: 30px; right: 0;"></i>
                        <div class="mb-4 pb-4 border-bottom border-secondary">
                            <p class="mb-0"> I have never ever get such types of facility before that shop like best
                                branded clothes with suitable pricing.
                            </p>
                        </div>
                        <div class="d-flex align-items-center flex-nowrap">
                            <div class="bg-secondary rounded">
                                <img src="design-assets/img/customerboy2.jpg" class="img-fluid rounded"
                                    style="width: 100px; height: 100px;" alt="">
                            </div>
                            <div class="ms-4 d-block">
                                <h4 class="text-dark">Aman Gupta</h4>
                                <p class="m-0 pb-3">CEO of Daraz</p>
                                <div class="d-flex pe-5">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tastimonial End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <a href="#">
                            <h1 class="text-primary mb-0">Clothes</h1>
                            <p class="text-secondary mb-0">Brand New products</p>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative mx-auto">
                            <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number"
                                placeholder="Your Email">
                            <button type="submit"
                                class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white"
                                style="top: 0; right: 0;">Subscribe Now</button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-end pt-3">
                            <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                    class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Why People Like us!</h4>
                        <p class="mb-4">One and Only store where customer can find their dream clothes in their comfort
                            zone .</p>
                        <a href="#" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Shop Info</h4>
                        <a class="btn-link" href="">About Us</a>
                        <a class="btn-link" href="">Contact Us</a>
                        <a class="btn-link" href="">Privacy Policy</a>
                        <a class="btn-link" href="">Terms & Condition</a>
                        <a class="btn-link" href="">Return Policy</a>
                        <a class="btn-link" href="">FAQs & Help</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Account</h4>
                        <a class="btn-link" href="">My Account</a>
                        <a class="btn-link" href="">Shop details</a>
                        <a class="btn-link" href="">Shopping Cart</a>
                        <a class="btn-link" href="">Wishlist</a>
                        <a class="btn-link" href="">Order History</a>
                        <a class="btn-link" href="">International Orders</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Contact</h4>
                        <p>Address: Dhangadhi</p>
                        <p>Email: webdeveloper@gmail.com</p>
                        <p>Phone: +9779865321457</p>
                        <p>Payment Accepted</p>
                        <img src="design-assets/img/payment.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>E-CLothing
                            Store</a>, All right reserved.</span>
                </div>
                <div class="col-md-6 my-auto text-center text-md-end text-white">

                    Designed By <a class="border-bottom" href="#">DLMS Group</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->



    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="design-assets/lib/easing/easing.min.js"></script>
    <script src="design-assets/lib/waypoints/waypoints.min.js"></script>
    <script src="design-assets/lib/lightbox/js/lightbox.min.js"></script>
    <script src="design-assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        $(".clothes-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            center: false,
            dots: false,
            loop: true,
            margin: 25,
            nav: true,
            navText: [
                '<i class="bi bi-arrow-left"></i>',
                '<i class="bi bi-arrow-right"></i>'
            ],
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                768: { items: 3 },
                992: { items: 4 },
                1200: { items: 5 }
            }
        });
    </script>

    <script>
        const counters = document.querySelectorAll('.count');
        const speed = 100; // Adjust the speed (lower is faster)

        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;

                const increment = Math.ceil(target / speed);

                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    </script>

    <script>
        const searchToggle = document.getElementById('searchToggle');
        const searchWrapper = document.getElementById('searchWrapper');
        const searchBox = document.getElementById('searchBox');
        const clearSearch = document.getElementById('clearSearch');

        searchToggle.addEventListener('click', () => {
            searchWrapper.classList.toggle('d-none');
            if (!searchWrapper.classList.contains('d-none')) {
                searchBox.focus();
            } else {
                searchBox.value = '';
                filterProducts('');
            }
        });

        searchBox.addEventListener('input', () => {
            filterProducts(searchBox.value.trim().toLowerCase());
        });

        clearSearch.addEventListener('click', () => {
            searchBox.value = '';
            searchBox.focus();
            filterProducts('');
        });

        function filterProducts(query) {
            const productCards = document.querySelectorAll('.product-card');

            const priceRangeMatch = query.match(/^(\d+)\s*-\s*(\d+)$/);
            let minPrice = null, maxPrice = null;

            productCards.forEach(card => {
                const id = card.dataset.id.toLowerCase();
                const name = card.dataset.name.toLowerCase();
                const price = parseFloat(card.dataset.price);
                const description = card.dataset.description.toLowerCase();
                const category = card.dataset.category.toLowerCase();

                let matches = false;

                if (priceRangeMatch) {
                    minPrice = parseFloat(priceRangeMatch[1]);
                    maxPrice = parseFloat(priceRangeMatch[2]);
                    if (price >= minPrice && price <= maxPrice) {
                        matches = true;
                    }
                } else {
                    if (
                        id.includes(query) ||
                        name.includes(query) ||
                        price.toString().includes(query) ||
                        description.includes(query) ||
                        category.includes(query)
                    ) {
                        matches = true;
                    }
                }

                card.style.display = matches ? '' : 'none';
            });
        }
    </script>

    <!-- Template Javascript -->
    <script src="design-assets/js/main.js"></script>
</body>

</html>