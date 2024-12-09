<?php
session_start();
require_once '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Fetch order details from the database
$userId = $_SESSION['user_id'];
$orderId = $_GET['order_id']; // Assuming you pass the order ID in the URL

$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? AND id = ?");
$stmt->execute([$userId, $orderId]);
$order = $stmt->fetch();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch order items
$stmtItems = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmtItems->execute([$orderId]);
$orderItems = $stmtItems->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - CaneLink</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .main-content {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2E7D32;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        .total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
        <p>Your order ID is: <strong><?php echo htmlspecialchars($order['id']); ?></strong></p>

        <h2>Order Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>KSH <?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="total">Total Amount: KSH <?php echo number_format($order['total_price'], 2); ?></p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>
