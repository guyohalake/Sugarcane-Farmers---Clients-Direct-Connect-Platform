<?php
session_start();
require_once '../config/database.php';

// Redirect if user is not a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.html");
    exit();
}

// Fetch orders for the logged-in user
$stmt = $conn->prepare("SELECT o.id, o.quantity, o.total_price, o.status, o.created_at, p.name as product_name 
                         FROM orders o 
                         JOIN products p ON o.product_id = p.id 
                         WHERE o.customer_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders - CaneLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .order-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .order-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .order-status {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Track Your Orders</h1>
    <?php if (empty($orders)): ?>
        <p>You have no orders to track.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-container">
                <div class="order-header">Order ID: <?php echo htmlspecialchars($order['id']); ?></div>
                <div>Product: <?php echo htmlspecialchars($order['product_name']); ?></div>
                <div>Quantity: <?php echo htmlspecialchars($order['quantity']); ?></div>
                <div>Total Price: KSH <?php echo number_format($order['total_price'], 2); ?></div>
                <div class="order-status">Status: <?php echo htmlspecialchars($order['status']); ?></div>
                <div>Order Date: <?php echo htmlspecialchars($order['created_at']); ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>