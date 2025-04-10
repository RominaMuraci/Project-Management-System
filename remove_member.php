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
    // Get the user IDs to be removed
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
        // Prepare the DELETE statement
        $stmt = $conn->prepare("DELETE FROM members_pms WHERE user_id = :user_id");
        
        foreach ($userIds as $userId) {
            // Check if the user exists in the members_pms table
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM members_pms WHERE user_id = :user_id");
            $checkStmt->execute([
                ':user_id' => $userId
            ]);
            $existingMember = $checkStmt->fetchColumn();
            
            if ($existingMember > 0) {
                // Delete the member if exists
                $stmt->execute([
                    ':user_id' => $userId
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Member with ID $userId does not exist."]);
                exit;
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Members removed successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
