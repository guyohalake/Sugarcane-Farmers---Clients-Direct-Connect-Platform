<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php"); // Redirect to admin login if not authorized
    exit();
}

// Include database connection
require_once '../config/database.php';

// Fetch the number of users and products for display
$userCountStmt = $conn->prepare("SELECT COUNT(*) FROM users");
$userCountStmt->execute();
$userCount = $userCountStmt->fetchColumn();

$productCountStmt = $conn->prepare("SELECT COUNT(*) FROM products");
$productCountStmt->execute();
$productCount = $productCountStmt->fetchColumn();

// Get the username from the session
$username = $_SESSION['username']; // Assuming you set this during login
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Admin Dashboard Styles */
        .admin-container {
            padding: 20px; /* Padding for the container */
            background: white; /* White background for the dashboard */
            border-radius: 5px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            max-width: 600px; /* Max width for the dashboard */
            margin: auto; /* Center the dashboard */
            text-align: center;
            margin-top: 100px; /* Center text */
        }

        .logo {
            width: 150px; /* Adjust the width of the logo */
            margin-bottom: 15px; /* Space below the logo */
        }

        h1 {
            color: #333; /* Dark text color */
        }

        /* Stats Styles */
        .stats {
            display: flex; /* Flexbox for stats layout */
            justify-content: space-between; /* Space between stats */
            margin-bottom: 20px; /* Space below stats */
        }

        .stat {
            background: #f4f4f4; /* Light background for stats */
            padding: 15px; /* Padding inside stats */
            border-radius: 5px; /* Rounded corners */
            text-align: center; /* Center text */
            flex: 1; /* Flex grow */
            margin: 0 10px; /* Margin between stats */
        }

        .stat h2 {
            margin: 0; /* Remove margin */
            font-size: 24px; /* Font size for count */
        }

        .stat p {
            margin: 5px 0 0; /* Margin for description */
            color: #666; /* Gray color for description */
        }

        /* Button Styles */
        .button-container {
            display: flex; /* Flexbox for button layout */
            justify-content: center; /* Center buttons */
            gap: 20px; /* Space between buttons */
            margin-top: 20px; /* Space above buttons */
        }

        .button {
            background-color: #007bff; /* Primary button color */
            color: white; /* White text color */
            padding: 10px 20px; /* Padding inside buttons */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners */
            text-decoration: none; /* Remove underline */
            font-size: 16px; /* Font size for the button */
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }

        .button:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        /* Error Message Styles */
        p {
            color: red; /* Red color for error messages */
            text-align: center; /* Center the error message */
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <img src="assets/logo2.png" alt="Logo" class="logo"> 
        <div class="welcome-message">
                <h1>Welcome back, Admin</h1>
            </div>
        <h3>This is your administrator's dashboard</h3> 

        <div class="stats">
            <div class="stat">
                <h2><?php echo $userCount; ?></h2>
                <p>Total Users</p>
            </div>
            <div class="stat">
                <h2><?php echo $productCount; ?></h2>
                <p>Total Products</p>
            </div>
        </div>

        <h2>Manage Options</h2>
        <div class="button-container">
            <a href="manage_products.php" 
            class="button">Manage Products</a>
            <a href="delete_user.php" class="button">Manage Users</a>
            <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
</body>
</html>
