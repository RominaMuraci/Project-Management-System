<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php');

// Redirect if user is not authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch user data from session
$userIdDb = $_SESSION['userid'];
$fullname  = $_SESSION['login_session'];
$username  = $_SESSION['useremail'];


$allowedAdmins = ['muraciromina@gmail.com'];
$isAdmin = in_array($username, $allowedAdmins);

if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Base SQL query to fetch projects with total hours worked
$sql = "SELECT p.id, p.name, p.kick_date, p.planned_duration, p.finished_date,
               p.created_by, COALESCE(SUM(th.hours_worked), 0) AS total_hours
        FROM projects_planning p
        LEFT JOIN task_hours th ON p.id = th.project_id
        WHERE 1=1";

// Filter by non-archived projects unless admin
$sql .= " AND p.archived = 0";

// Filter by user if not admin
if (!$isAdmin) {
    $sql .= " AND p.created_by = :login_session";
}

$sql .= " GROUP BY p.id";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->errorInfo()[2]]));
}

if (!$isAdmin) {
    $stmt->bindParam(':login_session', $fullname, PDO::PARAM_STR);
}

$stmt->execute();
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'id'                => $row['id'],
        'name'              => $row['name'],
        'kick_date'         => $row['kick_date'],
        'planned_duration'  => $row['planned_duration'],
        'finished_date'     => $row['finished_date'],
        'created_by'        => $row['created_by'],
        'total_hours'       => $row['total_hours']
    ];
}

$stmt = null;
$conn = null;

echo json_encode(['status' => 'success', 'data' => $data]);
?>
