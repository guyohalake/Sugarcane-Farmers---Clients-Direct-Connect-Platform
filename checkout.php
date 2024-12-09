<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.html");
    exit();
}

// Check if the delivery method is set
$deliveryMethod = $_POST['delivery'] ?? 'self-pick'; // Default to 'self-pick' if not set

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $item) {
            $itemTotal = $item['quantity'] * $item['price']; 
           
            $stmt = $conn->prepare("
                INSERT INTO orders (customer_id, product_id, quantity, total_price, status, delivery_method) 
                VALUES (?, ?, ?, ?, 'pending', ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $productId,
                $item['quantity'],
                $itemTotal,
                $deliveryMethod // Include the delivery method
            ]);
        }
       
        header("Location: payment.php");
        exit();
    } else {
        echo "Your cart is empty.";
    }
} elseif ($_POST['action'] == 'checkout') {
    // Get the selected delivery method
    $deliveryMethod = $_POST['delivery'] ?? 'self-pick'; // Default to 'self-pick' if not set
    $productIds = $_POST['product_id'] ?? []; // Get the array of product IDs

    foreach ($productIds as $productId) {
        // Insert order into the database for each product
        $stmtOrder = $conn->prepare("INSERT INTO orders (product_id, customer_id, quantity, total_price, status, delivery_method) VALUES (?, ?, ?, ?, 'pending checkout', ?)");
        $stmtOrder->execute([$productId, $customerId, $_SESSION['cart'][$productId]['quantity'], $_SESSION['cart'][$productId]['total_price'], $deliveryMethod]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CaneLink</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-content">
        <h1>Checkout</h1>
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>KSH <?php echo number_format($item['price'], 2); ?></td>
                            <td>KSH <?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form method="POST" action="checkout.php">
                <h3>Select Delivery Method</h3>
                <div class="delivery-option">
                    <input type="radio" id="self-pick" name="delivery" value="self-pick" required>
                    <label for="self-pick">Self Pick</label>
                </div>
                <div class="delivery-option">
                    <input type="radio" id="delivery-car" name="delivery" value="delivery-car">
                    <label for="delivery-car">Delivery by Car</label>
                </div>
                <div class="delivery-option">
                    <input type="radio" id="farmer-delivery" name="delivery" value="farmer-delivery">
                    <label for="farmer-delivery">Farmer Will Deliver</label>
                </div>
                <button type="submit">Complete Purchase</button>
            </form>
        <?php else: ?>
            <p>You have no items in your cart to checkout.</p>
        <?php endif; ?>
    </div>
</body>
</html> 