<?php
session_start();
require_once '../config/database.php';
require_once 'notification_functions.php'; // Include notification functions

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.html");
    exit();
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $userId = $_SESSION['user_id'];

    // Insert the order into the database
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, status) VALUES (?, ?, ?, 'Pending')");
    if ($stmt->execute([$userId, $productId, $quantity])) {
        // Notify user about order confirmation
       
    } else {
        echo "Error placing order.";
    }
}
?> 