<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("
            SELECT o.*, p.name as product_name, u.username as customer_name 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            JOIN users u ON o.customer_id = u.id 
            WHERE o.id = ? AND p.farmer_id = ?
        ");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            echo json_encode($order);
        } else {
            echo json_encode(['error' => 'Order not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch order details']);
    }
}
?> 