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
$userIdDb = $_SESSION['userid'];  // Logged-in user's ID
$fullname = $_SESSION['login_session'];
$username = $_SESSION['useremail'];

// Define allowed admin emails
$allowedAdmins = ['muraciromina@gmail.com'];

// Check if the logged-in user is an admin
$isAdmin = in_array($username, $allowedAdmins);

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Get start_date and end_date from the GET parameters or set defaults
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to the 1st day of the current month
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');         // Default to the last day of the current month

$employeeId = isset($_GET['employee_id']) ? $_GET['employee_id'] : null;

// Access control: Non-admin users cannot access other employees' data
if (!$isAdmin && $employeeId && $employeeId != $userIdDb) {
    die(json_encode(['status' => 'error', 'message' => 'Access denied. You do not have permission to view this data.']));
}

// Validate date format if both start and end dates are provided
if ($startDate && $endDate) {
    // Check if start_date is earlier than end_date
    if (strtotime($startDate) > strtotime($endDate)) {
        die(json_encode(['status' => 'error', 'message' => 'Start date cannot be later than end date.']));
    }
}

// Prepare the SQL query based on user role
if ($isAdmin) {
    // Admin can see both their own projects and the selected employee's projects
    if ($employeeId) {
        // If employee_id is passed (non-null), get projects for the selected employee
        $sql = "SELECT id, name, created_by, user_id, kick_date, finished_date, archived
                FROM projects_planning WHERE user_id = :employee_id";
    } else {
        // If no employee_id is passed, show all projects created by the admin user
        $sql = "SELECT id, name, created_by, user_id, kick_date, finished_date, archived
                FROM projects_planning WHERE user_id = :userid";
    }
} else {
    // Non-admin users can only see their own projects
    $sql = "SELECT id, name, created_by, user_id, kick_date, finished_date, archived
            FROM projects_planning WHERE user_id = :userid";
}

if ($startDate && $endDate) {
    // Updated condition:
    // For finished projects: only include if they overlap the filter period.
    // For unfinished projects: include if they started on or before the filter's end date.
    $sql .= " AND (
                (finished_date <> '0000-00-00' AND kick_date <= :end_date AND finished_date >= :start_date)
                OR
                (finished_date = '0000-00-00' AND kick_date <= :end_date)
             )";
} else {
    $sql .= " AND archived = 0";
}

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->errorInfo()[2]]));
}

// Bind the parameters based on the query
if ($isAdmin && $employeeId) {
    // Bind :employee_id for admins accessing other users' projects
    $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
} else {
    // Bind :userid for all other cases (own projects)
    $stmt->bindParam(':userid', $userIdDb, PDO::PARAM_STR);
}

// Bind date range parameters if provided
if ($startDate && $endDate) {
    $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
}

// Execute the query
$stmt->execute();

// Fetch the results
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'created_by' => $row['created_by'],
        'kick_date' => $row['kick_date'],
        'finished_date' => $row['finished_date'],
        'archived' => $row['archived']
    ];
}

// Close the statement and connection
$stmt = null;
$conn = null;

// Return the response as JSON
echo json_encode(['status' => 'success', 'data' => $data]);
?>
