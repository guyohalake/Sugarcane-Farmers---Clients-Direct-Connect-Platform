<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php"); // Redirect to admin login if not authorized
    exit();
}

// Include database connection
require_once '../config/database.php';

// Check if user_id is set in the POST request
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        // Begin a transaction
        $conn->beginTransaction();

        // First, delete related messages
        $deleteMessagesStmt = $conn->prepare("DELETE FROM messages WHERE sender_id = ?");
        $deleteMessagesStmt->execute([$user_id]);

        // Then, delete related farmer settings
        $deleteFarmerSettingsStmt = $conn->prepare("DELETE FROM farmer_settings WHERE farmer_id = ?");
        $deleteFarmerSettingsStmt->execute([$user_id]);

        // Finally, delete the user
        $deleteUserStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteUserStmt->execute([$user_id]);

        // Commit the transaction
        $conn->commit();

        // Redirect back to the manage users page with a success message
        header("Location: delete_user.php?message=User deleted successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollBack();
        header("Location: delete_user.php?error=Failed to delete user: " . $e->getMessage());
        exit();
    }
} else {
    // Redirect back with an error message if user_id is not set
    header("Location: delete_user.php?error=No user ID provided");
    exit();
}
?>
