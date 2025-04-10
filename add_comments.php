<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the root directory and include the database connection
$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php');

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Redirect to login page if the user is not authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request
    $projectId = $_POST['id'];  // Project ID
    $comment = $_POST['comment'];  // Comment content

    // Validate the input data
    if (empty($projectId) || empty($comment)) {
        echo json_encode(['status' => 'error', 'message' => 'Project ID and comment are required.']);
        exit;
    }

    try {
        // Insert the comment into the comments table
        $query = "INSERT INTO comments_planning (project_id, comment) VALUES (:project_id, :comment)";
        $stmt = $conn->prepare($query);

        // Bind the parameters and execute
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Comment added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add comment.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
