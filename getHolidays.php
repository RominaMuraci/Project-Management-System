<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';

// Include database connection (ensure the file returns a PDO instance)
$conn = include($rootDir . 'config/connection.php');

try {
  
    
    // Get the start and end dates from the request (optional)
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Default to the 1st day of the month
//    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : "$year-$month-" . date('t', strtotime("$year-$month-01")); // Default to the last day of the month
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
    
    // Adjust the query to filter holidays within the given start and end date range
    $query = "SELECT date, name FROM holidays WHERE date BETWEEN :start_date AND :end_date";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    $stmt->execute();
    
    // Fetch all holidays
    $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return holidays as JSON
    header('Content-Type: application/json');
    echo json_encode(['holidays' => $holidays]);
    
} catch (PDOException $e) {
    // Handle query or connection errors
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
