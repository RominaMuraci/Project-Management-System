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

// Fetch user data from session
$userIdDb = $_SESSION['userid'];
$fullname  = $_SESSION['login_session'];
$username  = $_SESSION['useremail'];

// Define allowed admin emails
$allowedAdmins = ['muraciromina@gmail.com'];
$isAdmin = in_array($username, $allowedAdmins);

// SQL to fetch data from the 'to_do_planning' table
$sql = "SELECT id, project_name, created_by, description, created_at FROM to_do_planning";

// If the user is not an admin, filter the results based on the 'created_by' field
if (!$isAdmin) {
    $sql .= " WHERE created_by = :login_session";
}

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind the parameter if the user is non-admin
if (!$isAdmin) {
    $stmt->bindParam(':login_session', $fullname, PDO::PARAM_STR);
}

// Execute the query
$stmt->execute();

// Fetch the results as an associative array
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the response based on the query results
if ($results) {
    echo json_encode(['status' => 'success', 'data' => $results]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found']);
}

// Close the connection
$conn = null;
?>
