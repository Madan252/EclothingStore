<?php
session_start();

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash password with MD5

    // Connect to database
    $con = mysqli_connect("localhost", "root", "", "eclothingstore");

    if ($con) {
        $sql = "SELECT * FROM User WHERE email='$email' AND password='$password' AND deleted_at IS NULL";
        $res = mysqli_query($con, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            $_SESSION['email'] = $email;

        if (!empty($_POST['remember'])) {
                setcookie("email", $email, time() + (86400 * 30), "/"); // Set for 30 days
            } else {
                setcookie("email", "", time() - 3600, "/"); // Clear cookie
            }

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid email or password!');</script>";
        }
    } else {
        echo "<script>alert('Database connection failed!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E Clothing Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form action="" method="post">
            <h1>Login</h1>
            <div class="input-box"> 
                <i class='bx bxs-user'></i>
                <input type="email" placeholder="email" name="email" required>
            </div>
            <div class="input-box">
                <i class='bx bxs-lock-alt'></i>
                <input type="password" placeholder="Password" name="password" required>  
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox"> Remember me</label>
                <a href="passwordreset.php">Forgot password?</a>
            </div>
            
                <button type="submit" class="btn" name="login">Login </button>
            
            <div class="Signup-link">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            </div>
            <div class="divider">
                <hr><span>Or Login with</span><hr>
            </div>
            
                <button type="button" class="google-btn" onclick="alert('Button clicked!')">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png" alt="Google logo">
                    Sign in with Google
                </button>
                
        </form>
    </div>

        <script>
        const rememberCheckbox = document.querySelector('input[name="remember"]');
        const passwordInput = document.querySelector('input[name="password"]');

        rememberCheckbox.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
        
    </script>

</body>
</html>
