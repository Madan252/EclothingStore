<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // For production, use password_hash()

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password' AND deleted_at IS NULL";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_image'] = $user['image'];

        if (!empty($_POST['remember'])) {
            setcookie("email", $email, time() + (86400 * 30), "/");
        } else {
            setcookie("email", "", time() - 3600, "/");
        }

        $success = "Login successful! Redirecting...";

        $redirectUrl = '../index.php';
        if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
            $allowedPages = ['chackout.php', 'index.php'];
            $requestedPage = basename($_GET['redirect']);
            if (in_array($requestedPage, $allowedPages)) {
                $redirectUrl = ($requestedPage == 'chackout.php') ? 'chackout.php' : '../index.php';
            }
        } elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'login.php') === false) {
            $referer = parse_url($_SERVER['HTTP_REFERER']);
            if ($referer['host'] === $_SERVER['HTTP_HOST']) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
            }
        }

        echo '<script>
            setTimeout(function() {
                window.location.href = "' . $redirectUrl . '";
            }, 2000);
        </script>';
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login - E-Clothing Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .message-container {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
        .error-msg {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ef9a9a;
        }
        .success-msg {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
    </style>
</head>
<body>
    <form class="authForm" action="" method="post" novalidate>
        <h2>E-Clothing Store</h2>

        <?php if ($error): ?>
            <div class="message-container error-msg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message-container success-msg">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <input type="email" name="email" id="email" placeholder=" " required
                   value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>">
            <label for="email">Email</label>
        </div>

        <div class="form-group">
            <i class="bx bx-show toggle-icon" id="togglePassword"></i>
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">Password</label>
        </div>

        <div class="form-options">
            <label><input type="checkbox" name="remember" <?php if (isset($_COOKIE['email'])) echo 'checked'; ?>> Remember me</label>
            <a href="passwordforgot.php">Forgot Password?</a>
        </div>

        <div class="button-group">
            <button type="submit" name="login">Login</button>
        </div>

        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>

        <!-- Google Login Button -->
        <div class="google-login">
            <div id="g_id_onload"
                 data-client_id="731513726294-dtfa773a7fpbuhc4d543f20a36m7pt7n.apps.googleusercontent.com"
                 data-callback="handleCredentialResponse"
                 data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="signin_with"
                 data-size="large"
                 data-logo_alignment="left">
            </div>
        </div>
    </form>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;
            togglePassword.classList.toggle('bx-show');
            togglePassword.classList.toggle('bx-hide');
        });

        togglePassword.addEventListener("keydown", e => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                togglePassword.click();
            }
        });

        function handleCredentialResponse(response) {
            const idToken = response.credential;

            fetch('googleloginhandler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_token: idToken })
            })
            .then(res => res.json())
            .then(data => {
                const form = document.querySelector('.authForm');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message-container';

                if (data.success) {
                    messageDiv.classList.add('success-msg');
                    messageDiv.textContent = 'Google login successful! Redirecting...';
                    form.insertBefore(messageDiv, form.firstChild);

                    setTimeout(() => {
                        window.location.href = document.referrer && !document.referrer.includes('login.php')
                            ? document.referrer
                            : '../index.php';
                    }, 2000);
                } else {
                    messageDiv.classList.add('error-msg');
                    messageDiv.textContent = 'Google login failed: ' + data.message;
                    form.insertBefore(messageDiv, form.firstChild);
                }
            })
            .catch(err => {
                const form = document.querySelector('.authForm');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message-container error-msg';
                messageDiv.textContent = 'Google login error, please try again.';
                form.insertBefore(messageDiv, form.firstChild);
                console.error(err);
            });
        }
    </script>
</body>
</html>
