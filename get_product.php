<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch product details']);
    }
}
?> 