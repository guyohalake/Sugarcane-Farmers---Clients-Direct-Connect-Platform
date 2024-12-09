<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['orderId']) && isset($data['status'])) {
    try {
        // Verify the order belongs to a product owned by this farmer
        $stmt = $conn->prepare("
            SELECT o.* FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.id = ? AND p.farmer_id = ?
        ");
        $stmt->execute([$data['orderId'], $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['orderId']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Order not found'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update order status: ' . $e->getMessage()
        ]);
    }
}
?> 