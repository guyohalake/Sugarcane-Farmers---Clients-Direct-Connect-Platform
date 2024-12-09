<?php
require_once '../config/database.php'; // Include database connection

function notifyOrderConfirmation($userId) {
    global $conn; // Use the global database connection

    $message = "Your order has been confirmed.";
    $query = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
}

function notifyFarmerContact($userId, $farmerName) {
    global $conn; // Use the global database connection

    $message = "You have a new message from farmer: " . htmlspecialchars($farmerName);
    $query = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
}
?>
