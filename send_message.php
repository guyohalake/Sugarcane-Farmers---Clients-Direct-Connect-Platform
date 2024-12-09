<?php
session_start();
require_once '../config/database.php'; // Include database connection
require_once 'notification_functions.php'; // Include notification functions

// Get the posted data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required data is present
if (isset($data['senderId'], $data['receiverId'], $data['message'])) {
    $senderId = $data['senderId'];
    $receiverId = $data['receiverId'];
    $message = $data['message'];

    // Insert the message into the database
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$senderId, $receiverId, $message])) {
        
        notifyFarmerContact($receiverId, $senderId); // Assuming senderId is the farmer's name or you can fetch the farmer's name from the database
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}
?>
