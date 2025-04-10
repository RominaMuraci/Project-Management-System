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

// Fetch user data from session
$userIdDb = $_SESSION['userid'];
$fullname = $_SESSION['login_session'];
$username = $_SESSION['useremail']; // Get the email from session


$allowedAdmins = ['muraciromina@gmail.com'];

// Check if the logged-in user is an admin
$isAdmin = in_array($username, $allowedAdmins);

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Prepare the base SQL query to fetch archived projects with total hours
$sql = "SELECT p.id, p.name, p.kick_date, p.planned_duration, p.finished_date,
               p.status, p.created_by, p.archived, COALESCE(SUM(th.hours_worked), 0) AS total_hours
        FROM projects_planning p
        LEFT JOIN task_hours th ON p.id = th.project_id
        WHERE p.archived = 1";

// If the user is NOT an admin, filter projects based on their email (created_by field)
if (!$isAdmin) {
    $sql .= " AND p.created_by = :login_session"; // Non-admin users can only see their own archived projects
}

// Group by project ID to aggregate total hours correctly
$sql .= " GROUP BY p.id";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->errorInfo()[2]]));
}

// Bind the parameter for non-admin users (if applicable)
if (!$isAdmin) {
    $stmt->bindParam(':login_session', $fullname, PDO::PARAM_STR); // Bind email to filter by created_by
}

// Execute the query
$stmt->execute();

// Fetch the results
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'id'                => $row['id'],
        'name'              => $row['name'],
        'kick_date'         => $row['kick_date'],
        'planned_duration'  => $row['planned_duration'],
        'finished_date'     => $row['finished_date'],
        'status'            => $row['status'],
        'created_by'        => $row['created_by'],
        'archived'          => $row['archived'],
        'total_hours'       => $row['total_hours']  // Include total hours worked
    ];
}

// Close the statement and connection
$stmt = null;
$conn = null;

// Return the response as JSON
echo json_encode(['status' => 'success', 'data' => $data]);
?>
