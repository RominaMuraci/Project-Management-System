<?php
session_start();

// Enable error reporting for debugging
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

// Read raw JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Debugging output (log the received JSON data)
error_log("Received JSON: " . print_r($data, true));

// Validate JSON structure
if (!isset($data['date']) || !isset($data['projectData']) || !is_array($data['projectData'])) {
    echo json_encode(['status' => 'error', 'message' => 'Date or projectData is missing']);
    exit();
}
// Extract sanitized data
$date = htmlspecialchars($data['date']);
$comments = isset($data['comments']) ? htmlspecialchars($data['comments']) : '';
$projects = isset($data['projectData']) ? $data['projectData'] : array(); // Fallback to an empty array if not set


try {
    // Prepare SQL statements
    $checkStmt = $conn->prepare("SELECT id FROM task_hours WHERE project_id = :project_id AND work_date = :work_date");
    $updateStmt = $conn->prepare("
        UPDATE task_hours 
        SET hours_worked = :hours_worked, comments = :comments 
        WHERE project_id = :project_id AND work_date = :work_date
    ");
    $insertStmt = $conn->prepare("
        INSERT INTO task_hours (project_id, work_date, hours_worked, comments) 
        VALUES (:project_id, :work_date, :hours_worked, :comments)
    ");

    // Loop through each project entry
    foreach ($projects as $project) {
        if (!isset($project['project_id']) || !isset($project['hours_worked'])) {
            continue; // Skip invalid entries
        }

        $project_id = (int) $project['project_id'];
        $hours_worked = (float) $project['hours_worked'];

        // Check if a record exists
        $checkStmt->execute([':project_id' => $project_id, ':work_date' => $date]);
        $exists = $checkStmt->fetch();

        if ($exists) {
            // Update existing record
            $updateStmt->execute([
                ':hours_worked' => $hours_worked,
                ':comments' => $comments,
                ':project_id' => $project_id,
                ':work_date' => $date,
            ]);
        } else {
            // Insert new record
            $insertStmt->execute([
                ':project_id' => $project_id,
                ':work_date' => $date,
                ':hours_worked' => $hours_worked,
                ':comments' => $comments,
            ]);
        }
    }

    // Success response
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    // Error handling
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
}

// Close database connection
$conn = null;
?>
