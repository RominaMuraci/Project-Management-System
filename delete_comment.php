<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php');

// Check if database connection is established
if (!$conn) {
    error_log('Database connection failed.');
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Ensure the user is authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $commentId = filter_var($_POST['comment_id'], FILTER_VALIDATE_INT);
    $projectId = filter_var($_POST['project_id'], FILTER_VALIDATE_INT);



    try {
        // Prepare the DELETE query
        $query = "DELETE FROM comments_planning WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $commentId, PDO::PARAM_INT);

        // Execute the query and return the appropriate response
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Comment successfully deleted.']);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log('SQL Error: ' . print_r($errorInfo, true));
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete the comment.']);
        }
    } catch (PDOException $e) {
        error_log('PDO Exception: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while deleting the comment.']);
    }
} else {
    // If the request method is not POST
    error_log('Invalid request method.');
    die(json_encode(['status' => 'error', 'message' => 'Invalid request method.']));
}
?>
