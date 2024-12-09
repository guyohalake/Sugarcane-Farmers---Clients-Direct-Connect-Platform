<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $productId = $_POST['product_id'] ?? null;
        $customerId = $_POST['customer_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        $price = $_POST['price'] ?? 0;

        if ($_POST['action'] == 'add') {
            // Add product to cart
            $_SESSION['cart'][$productId] = [
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $quantity * $price 
            ];

            // Insert order into the database
            $stmtOrder = $conn->prepare("INSERT INTO orders (product_id, customer_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'pending checkout')");
            $stmtOrder->execute([$productId, $customerId, $quantity, $quantity * $price]);
        } elseif ($_POST['action'] == 'update') {
            // Update product quantity
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                // Remove product if quantity is 0
                unset($_SESSION['cart'][$productId]);
            }
        } elseif ($_POST['action'] == 'remove') {
            // Remove product from cart
            unset($_SESSION['cart'][$productId]);
        } elseif ($_POST['action'] == 'checkout') {
            // Get the selected delivery method
            $deliveryMethod = $_POST['delivery'] ?? 'self-pick';
            $productIds = $_POST['product_id'] ?? [];

            foreach ($productIds as $productId) {
                // Ensure the product ID is valid and exists in the products table
                $stmtCheckProduct = $conn->prepare("SELECT COUNT(*) FROM products WHERE id = ?");
                $stmtCheckProduct->execute([$productId]);
                $productExists = $stmtCheckProduct->fetchColumn();

                if ($productExists) {
                    // Insert order into the database for each product
                    $stmtOrder = $conn->prepare("INSERT INTO orders (product_id, customer_id, quantity, total_price, status, delivery_method) VALUES (?, ?, ?, ?, 'pending checkout', ?)");
                    $stmtOrder->execute([$productId, $customerId, $_SESSION['cart'][$productId]['quantity'], $_SESSION['cart'][$productId]['total_price'], $deliveryMethod]);
                } else {
                    echo "Product ID $productId does not exist.";
                }
            }
        }
    }
}

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['quantity'] * $item['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - CaneLink</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body{
            margin-left: 100px;
        }
        .main-content {
            max-width: 800px; /* Set a max width for the cart */
            margin: 0 auto; /* Center the content */
            padding: 20px;
            background: #f9f9f9; /* Light background for contrast */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 50px;
        }

        table {
            width: 100%; /* Full width for the table */
            border-collapse: collapse; /* Remove gaps between cells */
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd; /* Add a bottom border */
        }

        th {
            background-color: #2E7D32; /* Header background color */
            color: white; /* Header text color */
        }

        .btn-primary {
            background-color: #2E7D32; /* Primary button color */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #1B5E20; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'template.php'; ?>
        
        <div class="main-content">
            <h1>Your Cart</h1>
            <?php if (empty($_SESSION['cart'])): ?>
                <p>Your cart is empty. <a href="products.php">Browse products</a></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Farmer</th>
                            <th>Quantity</th>
                            <th>Price per KG</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                            <?php
                            // get details from the database
                            $stmt = $conn->prepare("SELECT name, farmer_id FROM products WHERE id = ?");
                            $stmt->execute([$productId]);
                            $product = $stmt->fetch();

                            // get farmer details
                            $stmtFarmer = $conn->prepare("SELECT username FROM users WHERE id = ?");
                            $stmtFarmer->execute([$product['farmer_id']]);
                            $farmer = $stmtFarmer->fetch();

                            $itemTotal = $item['quantity'] * $item['price'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($farmer['username']); ?></td>
                                <td>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                        <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                                <td>KSH <?php echo number_format($item['price'], 2); ?></td>
                                <td>KSH <?php echo number_format($itemTotal, 2); ?></td>
                                <td>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit">Remove</button> 
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h3>Total Price: KSH <?php echo number_format($totalPrice, 2); ?></h3>
                
                <!-- Delivery Options Section -->
                <div class="delivery-options">
                    <h3>Select Delivery Method</h3>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="customer_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                            <input type="hidden" name="product_id[]" value="<?php echo $productId; ?>">
                        <?php endforeach; ?>
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
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit">Checkout</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 