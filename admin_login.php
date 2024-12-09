<?php
session_start();
require_once '../config/database.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && $admin['password'] === $password) {
        $_SESSION['user_id'] = $admin['id']; // Set a session variable for admin
        $_SESSION['user_type'] = 'admin'; // Set user type to admin
        header("Location: admin.php"); // Redirect to the admin panel
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/style.css"> 
</head>
<body>
    <div class="login-container">
        <h1>Welcome to</h1>
        <img src="assets/logo2.png" alt="Logo" class="logo">
        <p> Admin Panel</p>
        <p>Please log in as an administrator</p>     
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
       <form action="admin_login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
