<?php
 
$con = mysqli_connect("localhost", "root", "", "eclothingstore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all non-deleted products with their categories
$sql = "SELECT * FROM product WHERE deleted_at IS NULL";
$result = $con->query($sql);

// Initialize variables
$products = [];
$hasProducts = false;
$categories = ['Men', 'Women', 'Babies']; // Define your categories

// Process results
if ($result->num_rows > 0) {
    $hasProducts = true;
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'name' => htmlspecialchars($row['name']),
            'description' => htmlspecialchars($row['description']),
            'price' => number_format($row['price'], 2),
            'image' => htmlspecialchars($row['image']),
             ];
    }
}
?>
      

<!DOCTYPE html>
<html lang="en">

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
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div>
        <!-- Spinner End -->

 <?php include '../Webpage/Home/header.php'; ?>
        
         

        <!-- Hero Start -->
        <div class="container-fluid py-5 mb-5 hero-header">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-md-12 col-lg-7">
                        <h4 class="mb-3 text-secondary">"100% फेसन — 0% झन्झट"</h4>
                        <h1 class="mb-5 display-3 text-primary">"100% Real Fashion, Made for Nepal"</h1>
                        <div class="position-relative mx-auto">
                            <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="number" placeholder="Search">
                            <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; right: 25%;">Submit Now</button>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-5">
    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
        <div class="carousel-inner" role="listbox">

            <div class="carousel-item active rounded">
                <img src="../assets/image/black-tshirt.jpg" class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                <a href="#" class="btn px-4 py-2 text-white rounded">black-tshirt</a>
            </div>

            <div class="carousel-item rounded">
                <img src="../assets/image/black.jpg.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                <a href="#" class="btn px-4 py-2 text-white rounded">black-tshirt</a>
            </div>

            <div class="carousel-item rounded">
                <img src="../assets/image/frouck.jpg.jpg" class="img-fluid w-100 h-100 rounded" alt="Third slide">
                <a href="#" class="btn px-4 py-2 text-white rounded">Frouck</a>
            </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

   
  <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Cloths</h1>
                    </div>
                    <!-- Your category tabs remain the same -->
                </div>
                
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <?php if ($hasProducts): ?>
                                        <?php foreach ($products as $product): ?>
                                            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                                <div class="rounded position-relative fruite-item">
                                                    <div class="fruite-img">
                                                        <img src="../assets/image/<?= $product['image'] ?>" class="img-fluid w-100 rounded-top" alt="<?= $product['name'] ?>">
                                                    </div>
                                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">Clothes</div>
                                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                        <h4><?= $product['name'] ?></h4>
                                                        <p><?= $product['description'] ?></p>
                                                        <div class="d-flex justify-content-between flex-lg-wrap">
                                                            <p class="text-dark fs-5 fw-bold mb-0">Rs. <?= $product['price'] ?></p>
                                                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class='col-12'><p>No active products found.</p></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Your other tab content remains the same -->
                </div>
            </div>
        </div>
    </div>
                                     
 <?php include '../Webpage/Home/footer.php'; ?>
        
        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    </body>

</html>