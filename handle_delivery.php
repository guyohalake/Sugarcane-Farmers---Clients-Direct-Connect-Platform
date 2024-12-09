<?php
session_start();
require_once 'config/database.php'; // Ensure you have your database connection here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deliveryMethod = json_decode(file_get_contents('php://input'))->delivery;

    // Here you can save the delivery method to the database or process it as needed
    // For example, you might want to save it to the user's order details

    // Example response
    echo json_encode(['success' => true, 'message' => "Delivery method '$deliveryMethod' selected."]);
} else {
    echo json_encode(['success' => false, 'message' => "Invalid request method."]);
}
?>
