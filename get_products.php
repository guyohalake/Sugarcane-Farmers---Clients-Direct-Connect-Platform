<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}
?> 