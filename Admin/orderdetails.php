<?php
include '../includes/header.php';

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("DB connection failed: " . mysqli_connect_error());
}

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_condition = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $search_condition = "AND (o.id LIKE '%$search%' OR u.name LIKE '%$search%' OR p.name LIKE '%$search%')";
}

// Count total for pagination
$countSql = "
    SELECT COUNT(DISTINCT o.id) AS total
    FROM orders o
    LEFT JOIN user u ON o.user_id = u.id
    LEFT JOIN orderdetail od ON od.order_id = o.id
    LEFT JOIN product p ON od.product_id = p.id
    WHERE o.deleted_at IS NULL $search_condition
";
$totalResult = mysqli_query($con, $countSql);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalOrders = $totalRow['total'];
$totalPages = ceil($totalOrders / $limit);

// Fetch orders with pagination
$sql = "
    SELECT 
        o.id AS order_id,
        o.name AS order_name,
        o.order_status,
        o.shipping_charge,
        o.created_at,
        s.billing_address,
        s.shipping_address,
        u.name AS user_name,
        u.email AS user_email,
        GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_names,
        GROUP_CONCAT(DISTINCT p.image SEPARATOR ', ') AS product_images,
        SUM(od.quantity) AS total_quantity,
        SUM(od.unit_price * od.quantity) + o.shipping_charge AS total_price
    FROM orders o
    LEFT JOIN user u ON o.user_id = u.id
    LEFT JOIN shipping s ON s.order_id = o.id
    LEFT JOIN orderdetail od ON od.order_id = o.id
    LEFT JOIN product p ON od.product_id = p.id
    WHERE o.deleted_at IS NULL $search_condition
    GROUP BY o.id
    ORDER BY o.created_at ASC
    LIMIT $limit OFFSET $offset
";
$res = mysqli_query($con, $sql);
?>

<link rel="stylesheet" href="../assets/css/product.css">

<div class="dashboard-content">
    <header class="page-header center-content text-center">
        <h1><i class="fas fa-box-open"></i> Order Details</h1>
    </header>

    <div class="search-wrapper">
        <input type="search" id="searchInput" placeholder="Search by ID, product Name or Username..." autocomplete="off" />
        <button type="button" class="page-close-btn" onclick="window.location.href='Admindashboard.php'">&times;</button>
    </div>

    <div class="table-container">
        <table id="usertTable" class="user-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product Name(s)</th>
                    <th>Email</th>
                    <th>Order Status</th>
                    <th>Billing Address</th>
                    <th>Shipping Address</th>
                    <th>Shipping Charge (Rs)</th>
                    <th>Total Quantity</th>
                    <th>Total (Rs)</th>
                    <th>Order Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res && mysqli_num_rows($res) > 0): ?>
                    <?php while ($order = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= $order['order_id'] ?></td>
                            <td><?= htmlspecialchars($order['order_name'] ?: $order['user_name']) ?></td>
                            <td><?= htmlspecialchars($order['product_names']) ?></td>
                            <td><?= htmlspecialchars($order['user_email']) ?></td>
                            <td><?= htmlspecialchars($order['order_status']) ?></td>
                            <td><?= htmlspecialchars($order['billing_address']) ?></td>
                            <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                            <td><?= number_format($order['shipping_charge'], 2) ?></td>
                            <td><?= (int) $order['total_quantity'] ?></td>
                            <td><?= number_format($order['total_price'], 2) ?></td>
                            <td><?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></td>
                            <td class="text-center">
                                <div class="actions">
                                    <button class="btn view-btn" onclick='openOrderModal(<?= json_encode($order) ?>)'><i class="fas fa-eye"></i></button>
                                    <a href="Orderdetailsdelete.php?id=<?= $order['order_id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this Order Details?');"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="12" class="text-center text-muted">No order details found.</td></tr>
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

    <!-- Order Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeOrderModal()">&times;</span>
            <h2 class="modal-title"><i class="fas fa-box"></i> Order Details</h2>
            <p><strong>Customer:</strong> <span id="modalUserName"></span></p>
            <p><strong>Billing Address:</strong> <span id="modalBilling"></span></p>
            <p><strong>Shipping Address:</strong> <span id="modalShipping"></span></p>
            <div id="modalProducts"></div>
            <div class="modal-product-images" id="modalImages"></div>
            <p><strong>Shipping Charge:</strong> Rs <span id="modalShippingCharge"></span></p>
            <p><strong>Subtotal:</strong> Rs <span id="modalSubtotal"></span></p>
            <p><strong>Total:</strong> Rs <span id="modalTotal"></span></p>
        </div>
    </div>
</div>

<script>
function openOrderModal(order) {
    document.getElementById('modalUserName').innerText = order.user_name;
    document.getElementById('modalBilling').innerText = order.billing_address;
    document.getElementById('modalShipping').innerText = order.shipping_address;
    document.getElementById('modalShippingCharge').innerText = parseFloat(order.shipping_charge).toFixed(2);
    document.getElementById('modalSubtotal').innerText = (order.total_price - order.shipping_charge).toFixed(2);
    document.getElementById('modalTotal').innerText = parseFloat(order.total_price).toFixed(2);

    document.getElementById('modalProducts').innerHTML = `
        <p><strong>Products:</strong> ${order.product_names}</p>
        <p><strong>Total Quantity:</strong> ${order.total_quantity}</p>
    `;

    const imageContainer = document.getElementById('modalImages');
    imageContainer.innerHTML = '';
    if (order.product_images) {
        const images = order.product_images.split(', ');
        images.forEach(img => {
            imageContainer.innerHTML += `<img src="../assets/images/${img}" alt="Product Image">`;
        });
    }

    document.getElementById('orderModal').style.display = 'block';
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}

document.getElementById('searchInput').addEventListener('input', function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#usertTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
