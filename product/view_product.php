<?php
// Database connection inside this file
$con = mysqli_connect("localhost", "root", "", "eclothingstore");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle delete (soft delete)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $con->prepare("UPDATE product SET deleted_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_product.php");
    exit;
}

// Handle edit form submission
if (isset($_POST['edit_submit'])) {
    $edit_id = intval($_POST['edit_id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $sku = $_POST['sku'];

    $stmt = $con->prepare("UPDATE product SET name=?, description=?, price=?, quantity=?, sku=? WHERE id=?");
    $stmt->bind_param("ssdssi", $name, $description, $price, $quantity, $sku, $edit_id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_product.php");
    exit;
}

// Handle search and fetch products
$search = '';
$whereClause = "WHERE deleted_at IS NULL";

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = trim($_GET['search']);
    $search_esc = mysqli_real_escape_string($con, $search);
    $whereClause = "WHERE deleted_at IS NULL AND 
        (CAST(id AS CHAR) LIKE '%$search_esc%' OR name LIKE '%$search_esc%' OR sku LIKE '%$search_esc%')";
}

// Sorted by id ascending for clarity
$query = "SELECT * FROM product $whereClause ORDER BY id ASC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Products - EClothingStore</title>
<link rel="stylesheet" href="../assets/css/view_product.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<h1 class="page-title"><i class="fas fa-boxes"></i> All Products</h1>

<!-- Search Form -->
<form method="GET" action="view_product.php" class="search-form">
    <input type="text" name="search" placeholder="Search by ID, Name or SKU" value="<?php echo htmlspecialchars($search); ?>" />
    <button type="submit"><i class="fas fa-search"></i> Search</button>
    <?php if ($search !== ''): ?>
        <a href="view_product.php" class="clear-btn" title="Clear Search"><i class="fas fa-times-circle"></i></a>
    <?php endif; ?>
</form>

<div class="table-container">
    <table class="product-table" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price ($)</th>
                <th>Quantity</th>
                <th>SKU</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sn = 1;
            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)) { 
                    $imgPath = "../assets/image/" . htmlspecialchars($row['image']);
            ?>
            <tr>
                <td><?php echo $sn++; ?></td>
                <td><img src="<?php echo $imgPath; ?>" alt="Product Image" class="table-img"></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td class="desc-cell" title="<?php echo htmlspecialchars($row['description']); ?>">
                    <?php
                    $desc = htmlspecialchars($row['description']);
                    echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                    ?>
                </td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['sku']); ?></td>
                <td><?php echo date("Y-m-d H:i", strtotime($row['created_at'])); ?></td>
                <td>
                    <button class="view-btn" 
                        data-image="<?php echo htmlspecialchars($row['image'], ENT_QUOTES); ?>"
                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                        data-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
                        data-price="<?php echo number_format($row['price'], 2); ?>"
                        data-quantity="<?php echo htmlspecialchars($row['quantity']); ?>"
                        data-sku="<?php echo htmlspecialchars($row['sku'], ENT_QUOTES); ?>"
                        onclick="openViewModal(this)"> 
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="edit-btn" 
                        data-id="<?php echo $row['id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                        data-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
                        data-price="<?php echo $row['price']; ?>"
                        data-quantity="<?php echo $row['quantity']; ?>"
                        data-sku="<?php echo htmlspecialchars($row['sku'], ENT_QUOTES); ?>"
                        onclick="openEditModal(this)"> 
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="view_product.php?delete_id=<?php echo $row['id']; ?>" 
                        class="delete-btn" 
                        onclick="return confirm('Are you sure you want to delete this product?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php 
                }
            else: ?>
            <tr>
                <td colspan="9" style="text-align:center;">No products found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- View Modal -->
<div class="modal" id="viewModal">
    <div class="modal-box">
        <span class="close" onclick="closeViewModal()">&times;</span>
        <h2 id="view_name"></h2>
        <img id="view_image" src="" alt="Product Image" class="view-image" onclick="openImageModal()" />
        <p id="view_description"></p>
        <p><strong>Price:</strong> $<span id="view_price"></span></p>
        <p><strong>Quantity:</strong> <span id="view_quantity"></span></p>
        <p><strong>SKU:</strong> <span id="view_sku"></span></p>
    </div>
</div>

<!-- Image Enlarged Modal -->
<div class="modal" id="imageModal" onclick="closeImageModal()">
    <span class="close" onclick="closeImageModal()">&times;</span>
    <img id="imageModalContent" src="" alt="Enlarged Product Image" />
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-box">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Product</h2>
        <form method="POST" action="view_product.php" class="edit-form">
            <input type="hidden" name="edit_id" id="edit_id" />
            <label>Name:</label>
            <input type="text" name="name" id="edit_name" required />
            <label>Description:</label>
            <textarea name="description" id="edit_description" rows="4" required></textarea>
            <label>Price ($):</label>
            <input type="number" step="0.01" min="0" name="price" id="edit_price" required />
            <label>Quantity:</label>
            <input type="number" min="0" name="quantity" id="edit_quantity" required />
            <label>SKU:</label>
            <input type="text" name="sku" id="edit_sku" required />
            <div class="modal-buttons">
                <button type="submit" name="edit_submit" class="save-btn"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
// View Modal
function openViewModal(btn) {
    document.getElementById('view_name').innerText = btn.getAttribute('data-name');
    document.getElementById('view_image').src = "../assets/image/" + btn.getAttribute('data-image'); // fixed path
    document.getElementById('view_description').innerText = btn.getAttribute('data-description');
    document.getElementById('view_price').innerText = btn.getAttribute('data-price');
    document.getElementById('view_quantity').innerText = btn.getAttribute('data-quantity');
    document.getElementById('view_sku').innerText = btn.getAttribute('data-sku');
    document.getElementById('viewModal').style.display = 'flex';
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function openEditModal(btn) {
    document.getElementById('edit_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_name').value = btn.getAttribute('data-name');
    document.getElementById('edit_description').value = btn.getAttribute('data-description');
    document.getElementById('edit_price').value = btn.getAttribute('data-price');
    document.getElementById('edit_quantity').value = btn.getAttribute('data-quantity');
    document.getElementById('edit_sku').value = btn.getAttribute('data-sku');
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openImageModal() {
    const imgSrc = document.getElementById('view_image').src;
    document.getElementById('imageModalContent').src = imgSrc;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}
</script>

</body>
</html>
