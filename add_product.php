<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['quantity'])) {
            throw new Exception('All fields are required');
        }

        $image_url = null;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                throw new Exception('Invalid file type. Only JPG, PNG and GIF allowed.');
            }

            // Create uploads directory if it doesn't exist
            $upload_dir = '../uploads/products';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . '/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/products/' . $new_filename;
            } else {
                throw new Exception('Failed to upload image. Error: ' . error_get_last()['message']);
            }
        }

        $stmt = $conn->prepare("INSERT INTO products (farmer_id, name, description, price, quantity, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        
        $result = $stmt->execute([
            $_SESSION['user_id'],
            $_POST['name'],
            $_POST['description'],
            $_POST['price'],
            $_POST['quantity'],
            $image_url
        ]);

        if (!$result) {
            throw new Exception('Failed to insert into database');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Product added successfully',
            'product_id' => $conn->lastInsertId()
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 