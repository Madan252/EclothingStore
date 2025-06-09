<?php
include(__DIR__ . '/../Sidebar/Header.php');
// db connection
$con = mysqli_connect("localhost", "root", "", "eclothingstore");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    $upload_dir = "../assets/image/";
    $upload_file = $upload_dir . basename($_FILES["userfile"]["name"]);
    $image = "";

    if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $upload_file)) {
        $image = $_FILES["userfile"]["name"];
    }

    $name = $_POST["name"];
    $desc = $_POST["description"];
    $price = $_POST["price"];
    $qty = $_POST["quantity"];
    $sku = $_POST["sku"];
    $c_id = intval($_POST["category_id"]);

    $sql = "INSERT INTO product (name, description, price, sku, quantity, category_id, image)
            VALUES ('$name', '$desc', $price, '$sku', '$qty', $c_id, '$image')";

    $result = mysqli_query($con, $sql);

    if ($result) {
        echo "<script>alert('Product added successfully.');</script>";
    } else {
        echo "<script>alert('Error inserting data: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/add_product.css" />
    <script>
        function goToDashboard() {
            window.location.href = '../Admin/Admindashboard.php';
        }
    </script>
</head>
 <?php include '../Sidebar/Header.php'; ?>
<body>
    <main class="main-content">
        <h1 class="page-title"><i class="fas fa-boxes"></i> Add Product</h1>
        
        <div class="form-container">
            <form id="addProductForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Product Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price"><i class="fas fa-dollar-sign"></i> Price</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity"><i class="fas fa-boxes"></i> Quantity</label>
                        <input type="number" id="quantity" name="quantity" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sku"><i class="fas fa-barcode"></i> SKU</label>
                        <input type="text" id="sku" name="sku" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="category_id"><i class="fas fa-list"></i> Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="" disabled selected>Select Category</option>
                        <option value="1">Men</option>
                        <option value="2">Women</option>
                        <option value="3">Babies</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="userfile"><i class="fas fa-image"></i> Product Image</label>
                    <input type="file" id="userfile" name="userfile" required>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="submit" class="submit-btn">
                        <i class="fas fa-plus-circle"></i> Add Product
                    </button>
                    <button type="reset" class="reset-btn">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <a href="view_product.php" class="view-btn">
                        <i class="fas fa-eye"></i> View Products
                    </a>
                </div>
            </form>
        </div>
    </main>
    
   <?php include '../Sidebar/Footer.php'; ?>
    
    <script>
        document.getElementById('addProductForm').addEventListener('submit', function (e) {
            const priceInput = document.getElementById('price');
            if (parseFloat(priceInput.value) < 0) {
                alert("Price cannot be negative!");
                priceInput.focus();
                e.preventDefault();
            }
        });
    </script>
</body>
</html>