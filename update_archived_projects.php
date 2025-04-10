<?php
session_start();

// Enable error reporting for debugging purposes (in production, consider disabling display_errors)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log(print_r($_POST, true));

// Set the header to return JSON
header('Content-Type: application/json');

$rootDir = __DIR__ . '../../'; // Adjust this path as needed

// Include database connection
$conn = include($rootDir . 'config/connection.php');

// Check if the user is logged in
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

// Fetch user data from session
$username = $_SESSION['useremail'];

// Define allowed admin emails

$allowedAdmins = ['muraciromina@gmail.com'];

// Check if the logged-in user is an admin
$isAdmin = in_array($username, $allowedAdmins);

// Check if project ID is provided
if (!isset($_POST['project_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Project ID is required']));
}

$projectId = intval($_POST['project_id']);

// Prepare SQL to update the `archived` column
$sql = "UPDATE projects_planning SET archived = 1 WHERE id = :project_id";

// If the user is NOT an admin, they can only archive their own projects
if (!$isAdmin) {
    $sql .= " AND created_by = :login_session";
}

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->errorInfo()[2]]));
}

// Bind parameters
$stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
if (!$isAdmin) {
    $stmt->bindParam(':login_session', $_SESSION['login_session'], PDO::PARAM_STR);
}

// Execute the query and return a JSON response
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Project archived successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to archive project']);
}

// Close the connection
$stmt = null;
$conn = null;
?>
