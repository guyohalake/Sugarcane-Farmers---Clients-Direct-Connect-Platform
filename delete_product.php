<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $product_id = $_POST['product_id'];
        
        // Verify the product belongs to this farmer
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
        $stmt->execute([$product_id, $_SESSION['user_id']]);
        $product = $stmt->fetch();
        
        if (!$product) {
            throw new Exception('Product not found or unauthorized');
        }

        // Step 1: Delete dependent orders
        $deleteOrdersQuery = "DELETE FROM orders WHERE product_id = ?";
        $stmt = $conn->prepare($deleteOrdersQuery);
        $stmt->execute([$product_id]);

        // Step 2: Delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND farmer_id = ?");
        $stmt->execute([$product_id, $_SESSION['user_id']]);
        
        // Delete the image file if it exists
        if ($product['image_url'] && file_exists('../' . $product['image_url'])) {
            unlink('../' . $product['image_url']);
        }
        
        echo json_encode(['success' => true, 'message' => 'Product and its associated orders deleted successfully']);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?> 