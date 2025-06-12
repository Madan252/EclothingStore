<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="rounded position-relative fruite-item">
        <a href="user/product_details.php?id=<?php echo $row_all['id'] ?? $row_men['id'] ?? $row_women['id'] ?? $row_babies['id'] ?? $row_free['id']; ?>">
            <img src="design-assets/img/<?php echo $row_all['image'] ?? $row_men['image'] ?? $row_women['image'] ?? $row_babies['image'] ?? $row_free['image']; ?>" class="img-fluid w-100 rounded-top" alt="">
        </a>
        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;"><?php echo $row_all['category_name'] ?? $row_men['category_name'] ?? $row_women['category_name'] ?? $row_babies['category_name'] ?? $row_free['category_name']; ?></div>
        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
            <h4><?php echo $row_all['name'] ?? $row_men['name'] ?? $row_women['name'] ?? $row_babies['name'] ?? $row_free['name']; ?></h4>
            <p><?php echo $row_all['description'] ?? $row_men['description'] ?? $row_women['description'] ?? $row_babies['description'] ?? $row_free['description']; ?></p>
            <div class="d-flex justify-content-between flex-lg-wrap">
                <p class="text-dark fs-5 fw-bold mb-0">$<?php echo $row_all['price'] ?? $row_men['price'] ?? $row_women['price'] ?? $row_babies['price'] ?? $row_free['price']; ?></p>
                <a href="user/add_to_cart.php?id=<?php echo $row_all['id'] ?? $row_men['id'] ?? $row_women['id'] ?? $row_babies['id'] ?? $row_free['id']; ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                </a>
            </div>
        </div>
    </div>
</div>
