<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php');

if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Get start_date and end_date from the GET parameters or set defaults
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to the 1st day of the current month
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'); // Default to the last day of the current month

$employeeId = isset($_GET['employee_id']) ? $_GET['employee_id'] : null;

if (!$employeeId) {
    die(json_encode(['status' => 'error', 'message' => 'Employee ID is required']));
}

if (!$startDate || !$endDate) {
    die(json_encode(['status' => 'error', 'message' => 'Start date and end date are required']));
}

try {
    // Query to fetch task hours along with comments for the selected employee within the date range
    $query = "SELECT th.work_date, th.project_id, SUM(th.hours_worked) as hours, th.comments
              FROM task_hours th
              INNER JOIN projects_planning pp ON th.project_id = pp.id
              WHERE th.work_date BETWEEN :start_date AND :end_date
              AND pp.user_id = :employee_id
              GROUP BY th.work_date, th.project_id, th.comments";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
    $stmt->execute();
    
    $task_hours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $task_hours,
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data: ' . $e->getMessage()]);
}
?>
