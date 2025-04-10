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


$projectId = $_POST['id'];
$name = $_POST['name'];
$kickDate = $_POST['kickOffDate'];  // Kick-off date from the POST request
$plannedDuration = $_POST['plannedDuration'];  // Planned duration

$sql = "UPDATE projects_planning 
        SET name = :name, kick_date = :kick_date, planned_duration = :planned_duration 
        WHERE id = :id";

// Prepare and execute the query
$stmt = $conn->prepare($sql);

try {
    $stmt->execute([
        ':name' => $name,
        ':kick_date' => $kickDate,  // Correctly bind the kick-off date
        ':planned_duration' => $plannedDuration,  // Correctly bind the planned duration
        ':id' => $projectId
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Project updated successfully']);
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while updating the project']);
}

?>
