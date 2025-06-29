<?php
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("DB connection failed: " . mysqli_connect_error());
}

// Set limit and page
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total product count for pagination
$totalResult = mysqli_query($con, "SELECT COUNT(*) AS total FROM product WHERE deleted_at IS NULL");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// Fetch products for current page
$result = mysqli_query($con, "SELECT * FROM product WHERE deleted_at IS NULL ORDER BY created_at ASC LIMIT $limit OFFSET $offset");
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/product.css">

<div class="dashboard-content">

    <header class="page-header center-content text-center">
        <h1><i class="fas fa-box-open"></i> Our Products</h1>
    </header>

    <div class="search-wrapper">
        <input type="search" id="searchInput" placeholder="Search by ID, Name or SKU..." autocomplete="off" />
        <button type="button" class="page-close-btn" onclick="window.location.href='../admin/Admindashboard.php'">&times;</button>
    </div>

    <div class="table-container">
        <table id="productTable" class="product-table">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price (Rs)</th>
                    <th>Quantity</th>
                    <th>SKU</th>
                    <th>Created At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0):
                    $sn = $offset + 1;
                    foreach ($products as $p):
                        $descShort = strlen($p['description']) > 80 ? substr($p['description'], 0, 80) . '...' : $p['description'];
                        $imgPath = "../assets/images/" . htmlspecialchars($p['image']);
                ?>
                <tr data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" data-sku="<?= htmlspecialchars(strtolower($p['sku'])) ?>">
                    <td><?= $sn++ ?></td>
                    <td><img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="table-img" /></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td title="<?= htmlspecialchars($p['description']) ?>"><?= htmlspecialchars($descShort) ?></td>
                    <td><?= number_format($p['price'], 2) ?></td>
                    <td><?= htmlspecialchars($p['quantity']) ?></td>
                    <td><?= htmlspecialchars($p['sku']) ?></td>
                    <td><?= date("Y-m-d H:i", strtotime($p['created_at'])) ?></td>
                    <td class="text-center">
                        <div class="actions">
                            <button class="btn view-btn" onclick='openProductModal(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'><i class="fas fa-eye"></i></button>
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn edit-btn"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $p['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="9" class="no-data">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-controls">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="pagination-btn">Back</a>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="pagination-btn">Next</a>
            <?php else: ?>
                <a class="pagination-btn disabled">Next</a>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($totalPages > 1): ?>
                <a href="?page=<?= $page + 1 ?>" class="pagination-btn">Next</a>
            <?php else: ?>
                <a class="pagination-btn disabled">Next</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal" style="display:none;">
        <div class="modal-content">
            <button class="modal-close-btn" onclick="closeProductModal()"><i class="fas fa-times"></i></button>
            <div class="modal-body">
                <img id="modalImage" src="" alt="Product Image" class="modal-image" onclick="openImageView()" />
                <div class="modal-details">
                    <h2 id="modalName"></h2>
                    <p id="modalDesc"></p>
                    <p><strong>Price:</strong> Rs <span id="modalPrice"></span></p>
                    <p><strong>Quantity:</strong> <span id="modalQty"></span></p>
                    <p><strong>SKU:</strong> <span id="modalSKU"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div id="imageViewModal" class="modal" onclick="closeImageView(event)" style="display:none;">
        <button class="modal-close-btn close-image" onclick="closeImageView(event)"><i class="fas fa-times"></i></button>
        <img id="largeImage" src="" alt="Large product image view" />
    </div>

</div>

<?php include '../includes/footer.php'; ?>

<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productTable tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

function openProductModal(product) {
    document.getElementById('modalImage').src = '../assets/images/' + product.image;
    document.getElementById('modalName').textContent = product.name;
    document.getElementById('modalDesc').textContent = product.description;
    document.getElementById('modalPrice').textContent = parseFloat(product.price).toFixed(2);
    document.getElementById('modalQty').textContent = product.quantity;
    document.getElementById('modalSKU').textContent = product.sku;
    document.getElementById('productModal').style.display = 'flex';
    document.getElementById('modalName').focus();
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function openImageView() {
    const modalImage = document.getElementById('modalImage');
    const largeImage = document.getElementById('largeImage');
    largeImage.src = modalImage.src;
    largeImage.alt = modalImage.alt;
    document.getElementById('imageViewModal').style.display = 'flex';
}

function closeImageView(event) {
    if (!event || event.target === document.getElementById('imageViewModal') || event.target.classList.contains('close-image')) {
        document.getElementById('imageViewModal').style.display = 'none';
    }
}
</script>
