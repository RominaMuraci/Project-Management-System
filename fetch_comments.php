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

// Check if the request is a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get the project ID from the request, if provided
        $projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;

        // Fetch comments for the specific project or all comments if no project ID is provided
        if ($projectId) {
            $query = "SELECT * FROM comments_planning WHERE project_id = :project_id ORDER BY created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM comments_planning ORDER BY created_at DESC";
            $stmt = $conn->prepare($query);
        }

        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the comments as a JSON response
        echo json_encode(['status' => 'success', 'data' => $comments]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
