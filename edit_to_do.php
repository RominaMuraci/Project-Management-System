<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';

// Include database connection
$conn = include($rootDir . 'config/connection.php');

// Redirect to login page if the user is not authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Retrieve POST data
$projectId = isset($_POST['id']) ? $_POST['id'] : null;
$project_name = isset($_POST['name']) ? trim($_POST['name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';


$sql = "UPDATE to_do_planning 
        SET project_name = :project_name, description = :description 
        WHERE id = :id";

// Prepare and execute the query
$stmt = $conn->prepare($sql);

try {
    // Execute the statement with parameters
    $stmt->execute([
        ':project_name' => $project_name,
        ':description' => $description,
        ':id' => $projectId
    ]);

    // Check if rows were updated
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Project updated successfully']);
    } else {
        // Handle the case where no rows were updated (could be due to invalid ID)
        echo json_encode(['status' => 'error', 'message' => 'No project found with the provided ID']);
    }
} catch (PDOException $e) {
    // Log the error for debugging
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while updating the project']);
}
?>
