<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php'); // Database connection

if (!$conn) {
    die("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use isset() to check for the presence of keys in $_POST, as PHP 5 doesn't support null coalescing operator
    $adminId = isset($_POST['admin_id']) ? $_POST['admin_id'] : 0; // Get admin ID
    $userIds = isset($_POST['user_ids']) ? $_POST['user_ids'] : array(); // Get user IDs
    
    // Ensure userIds is an array
    if (!is_array($userIds)) {
        $userIds = [$userIds]; // Convert single value to array
    }
    
    if (empty($userIds)) {
        echo json_encode(['status' => 'error', 'message' => 'No members selected.']);
        exit;
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO members_pms (admin_id, user_id) VALUES (:admin_id, :user_id)");
        
        foreach ($userIds as $userId) {
            // Check if the user already exists in the members_pms table
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM members_pms WHERE admin_id = :admin_id AND user_id = :user_id");
            $checkStmt->execute([
                ':admin_id' => $adminId,
                ':user_id' => $userId
            ]);
            $existingMember = $checkStmt->fetchColumn();
            
            if ($existingMember > 0) {
                echo json_encode(['status' => 'error', 'message' => "Member with ID $userId already exists."]);
                exit;
            }
            
            // Insert member if not already in the list
            $stmt->execute([
                ':admin_id' => $adminId,
                ':user_id' => $userId
            ]);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Members added successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
