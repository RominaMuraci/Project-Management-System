<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php'); // Database connection

if (!$conn) {
    die("Database connection failed.");
}

header('Content-Type: application/json');

try {
    // Prepare and execute the query
    $query = "SELECT
                u.userid,
                CONCAT(u.firstname, ' ', u.lastname) AS fullname,
                u.email,
                m.added_at
              FROM members_pms m
              JOIN users u ON m.user_id = u.userid";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return JSON response
    echo json_encode(['status' => 'success', 'data' => $members]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
