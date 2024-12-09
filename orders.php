<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../login.html");
    exit();
}

// Include the database configuration
require_once '../config/database.php'; // Ensure this file returns a valid $conn object

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders - CaneLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .orders-container {
            padding: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <h2>Your Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Delivery Method</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch orders from the database
                if (isset($conn)) {
                    $query = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.customer_id = u.id"; // Join to get customer details
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($orders) {
                        foreach ($orders as $order) {
                            echo "<tr>
                                    <td>{$order['id']}</td>
                                    <td>{$order['username']}</td>
                                    <td>{$order['email']}</td>
                                    <td>{$order['quantity']}</td>
                                    <td>KSH {$order['total_price']}</td>
                                    <td>{$order['status']}</td>
                                    <td>{$order['delivery_method']}</td>
                                    <td>
                                        <button onclick=\"contactCustomer('{$order['email']}');\">Contact</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No orders found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Database connection error.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <button style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;" onclick="window.location.href='dashboard.php';">Return to Dashboard</button>
        </div>
    </div>
    <script>
        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this order?')) {
                window.location.href = 'delete_order.php?id=' + orderId; 
            }
        }

        function contactCustomer(email) {
            window.location.href = 'mailto:' + email;
        }
    </script>
</body>
</html>
