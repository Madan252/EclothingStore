<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id_token'])) {
    echo json_encode(['success' => false, 'message' => 'No ID token provided']);
    exit;
}

$id_token = $input['id_token'];
$client_id = '743361665961-vinqrjm4md449kiu3lqehqi92c8bd5mq.apps.googleusercontent.com'; // Updated client ID

// Verify token with Google
$verify_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
$response = file_get_contents($verify_url);
$data = json_decode($response, true);

if (!$data || $data['aud'] !== $client_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid token or client ID']);
    exit;
}

$email = $data['email'] ?? '';
$name = $data['name'] ?? 'Google User';
$picture = $data['picture'] ?? 'avatar.jpg';

$con = mysqli_connect("localhost", "root", "", "E_Clothing_Store");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

$email_escaped = mysqli_real_escape_string($con, $email);

$stmt = mysqli_prepare($con, "SELECT id, name, email, user_type, image FROM user WHERE email=? AND deleted_at IS NULL");
mysqli_stmt_bind_param($stmt, "s", $email_escaped);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    // User exists, fetch data
    $user = mysqli_fetch_assoc($result);
} else {
    // Insert new user with random password
    $random_password = md5(bin2hex(random_bytes(8)));
    $default_user_type = 'user';

    $stmtInsert = mysqli_prepare($con, "INSERT INTO user (name, email, password, image, user_type) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmtInsert, "sssss", $name, $email, $random_password, $picture, $default_user_type);
    if (!mysqli_stmt_execute($stmtInsert)) {
        echo json_encode(['success' => false, 'message' => 'Failed to register user']);
        exit;
    }
    $new_user_id = mysqli_insert_id($con);
    mysqli_stmt_close($stmtInsert);

    // Fetch new user
    $stmt = mysqli_prepare($con, "SELECT id, name, email, user_type, image FROM user WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $new_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}

mysqli_stmt_close($stmt);
mysqli_close($con);

// Set session for logged in user
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['user_image'] = $user['image'];

// Return JSON success
echo json_encode(['success' => true]);
exit;
?>
