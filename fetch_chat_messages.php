<?php
session_start();
require_once '../config/database.php'; // Include database connection

// Check if senderId and receiverId are set in the GET request
if (!isset($_GET['senderId']) || !isset($_GET['receiverId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

$senderId = $_GET['senderId'];
$receiverId = $_GET['receiverId'];

// Prepare the SQL statement to fetch messages between the sender and receiver
$stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");

// Execute the query and check for errors
if (!$stmt->execute([$senderId, $receiverId, $receiverId, $senderId])) {
    echo json_encode(['status' => 'error', 'message' => 'Query execution failed: ' . implode(", ", $stmt->errorInfo())]);
    exit;
}

// Fetch all messages
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if any messages were found
if (empty($messages)) {
    echo json_encode(['messages' => [], 'status' => 'no_messages']);
} else {
    echo json_encode(['messages' => $messages, 'status' => 'success']);
}
?>
