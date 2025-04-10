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

// Get start_date and end_date from the GET parameters or set defaults
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to the 1st day of the current month
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'); // Default to the last day of the current month

// Get employee ID if provided
$employeeId = isset($_GET['employee_id']) ? $_GET['employee_id'] : null;

try {
    $response = array('status' => 'success', 'data' => array());
    
    // ======================== FETCH ABSENT DATES FOR SPECIFIC EMPLOYEE ========================
    $absentSql = "
    SELECT
        lr.employee_id,
        lr.start_date,
        lr.end_date
    FROM
        leave_requests lr
    WHERE
        lr.status = 'Approved'
        AND lr.employee_id = :employeeId
        AND (
            (lr.start_date BETWEEN :startDate AND :endDate)
            OR
            (lr.end_date BETWEEN :startDate AND :endDate)
            OR
            (lr.start_date <= :startDate AND lr.end_date >= :endDate)
        )
    ";
    
    $stmtAbsent = $conn->prepare($absentSql);
    $stmtAbsent->bindParam(':employeeId', $employeeId, PDO::PARAM_STR);
    $stmtAbsent->bindParam(':startDate', $startDate, PDO::PARAM_STR);
    $stmtAbsent->bindParam(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmtAbsent->execute();  // Execute the query
    
    $absentEmployees = $stmtAbsent->fetchAll(PDO::FETCH_ASSOC);  // Fetch results
    
    $employeeAbsenceData = array();
    
    foreach ($absentEmployees as $absence) {
        $employee = $absence['employee_id'];
        $leaveStart = new DateTime($absence['start_date']);
        $leaveEnd = new DateTime($absence['end_date']);
        
        // Adjust the absence dates to fit within the requested date range
        $adjustedStart = max($leaveStart, new DateTime($startDate)); // Ensure start is within the selected range
        $adjustedEnd = min($leaveEnd, new DateTime($endDate)); // Ensure end is within the selected range
        
        // Loop through each date in the absence period
        while ($adjustedStart <= $adjustedEnd) {
            $currentDate = $adjustedStart->format('Y-m-d');
            
            // Add the absence date to the employee's data
            if (!isset($employeeAbsenceData[$employee])) {
                $employeeAbsenceData[$employee] = [];
            }
            $employeeAbsenceData[$employee][] = $currentDate;
            
            $adjustedStart->modify('+1 day'); // Move to the next day
        }
    }
    
    $response['data']['vacation_employees'] = $employeeAbsenceData;
    
    // ======================== FINAL RESPONSE ========================
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Database Error: ' . $e->getMessage()
    ));
}

// Close the statement and connection
$stmtAbsent = null;
$conn = null;
?>
