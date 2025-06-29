<?php
include '../includes/header.php';
$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("DB connection failed: " . mysqli_connect_error());
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total users
$totalResult = mysqli_query($con, "SELECT COUNT(*) AS total FROM user WHERE user_type != 'admin' AND deleted_at IS NULL");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalUsers = $totalRow['total'];
$totalPages = ceil($totalUsers / $limit);

// Fetch customers
$sql = "SELECT id, name, email, image, created_at FROM user WHERE user_type != 'admin' AND deleted_at IS NULL ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
$res = mysqli_query($con, $sql);
?>

<link rel="stylesheet" href="../assets/css/product.css">

<div class="dashboard-content">
    <header class="page-header center-content text-center">
        <h1><i class="fas fa-users"></i> Customer Details</h1>
    </header>

    <div class="search-wrapper">
        <input type="search" id="searchInput" placeholder="Search by ID, Username, Joined Date..." autocomplete="off" />
        <button type="button" class="page-close-btn" onclick="window.location.href='Admindashboard.php'">&times;</button>
    </div>

    <div class="table-container">
        <table id="usertTable" class="user-table">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>User ID</th>
                    <th>Profile Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res && mysqli_num_rows($res) > 0): $sn = $offset + 1; ?>
                    <?php while ($user = mysqli_fetch_assoc($res)): 
                        $jsonData = htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8');
                        $imagePath = '../design-assets/img/' . $user['image'];
                        $imgSrc = (!empty($user['image']) && file_exists($imagePath)) ? $imagePath : '../assets/images/avatar.jpg';
                    ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><img src="<?= $imgSrc ?>" class="profile-img" alt="Profile"></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('Y-m-d h:i A', strtotime($user['created_at'])) ?></td>
                        <td class="text-center">
                            <div class="actions">
                                <button class="btn view-btn" data-user='<?= $jsonData ?>' onclick="viewUserDetails(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="customerdelete.php?id=<?= $user['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this customer?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">No customers found.</td></tr>
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
</div>

<!-- Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeOrderModal()">&times;</span>
        <h2 class="modal-title"><i class="fas fa-user"></i> Customer Details </h2>
        <p><strong>Customer Name:</strong> <span id="modalUserName"></span></p>
        <p><strong>Email Address:</strong> <span id="modalEmail"></span></p>
        <p><strong>Joined Date:</strong> <span id="modalDate"></span></p>
        <div class="modal-users-images" id="modalImages"></div>
    </div>
</div>

<script>
function viewUserDetails(button) {
    const user = JSON.parse(button.getAttribute('data-user'));
    document.getElementById('modalUserName').innerText = user.name;
    document.getElementById('modalEmail').innerText = user.email;
    document.getElementById('modalDate').innerText = user.created_at;

    const imageContainer = document.getElementById('modalImages');
    imageContainer.innerHTML = '';

    const imagePath = user.image ? `../design-assets/img/${user.image}` : '../assets/images/avatar.jpg';
    imageContainer.innerHTML = `<img src="${imagePath}" alt="User Image">`;

    document.getElementById('userModal').style.display = 'block';
}

function closeOrderModal() {
    document.getElementById('userModal').style.display = 'none';
}

document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usertTable tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
