<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // First verify the product belongs to this farmer
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
        $stmt->execute([$_POST['id'], $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit();
        }

        $image_url = null;
        
        // Handle image upload if new image is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../uploads/products/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = 'uploads/products/' . $new_filename;
                }
            }
        }

        // Update query based on whether new image was uploaded
        if ($image_url) {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, unit = ?, image_url = ? WHERE id = ? AND farmer_id = ?");
            $params = [
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['quantity'],
                $_POST['unit'],
                $image_url,
                $_POST['id'],
                $_SESSION['user_id']
            ];
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, unit = ? WHERE id = ? AND farmer_id = ?");
            $params = [
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['quantity'],
                $_POST['unit'],
                $_POST['id'],
                $_SESSION['user_id']
            ];
        }
        
        $stmt->execute($params);
        
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update product: ' . $e->getMessage()
        ]);
    }
}
?> 