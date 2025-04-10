<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root directory and include dependencies
$rootDir = __DIR__ . '../../';
include '../Classes/EmailService.php';

$conn = include($rootDir . 'config/connection.php');

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
}

// Set PDO error mode to exceptions for PHP 5
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch ongoing projects from the database
try {
    $stmt = $conn->prepare("SELECT name, kick_date, planned_duration, created_by FROM projects_planning WHERE (NOW() BETWEEN kick_date AND DATE_ADD(kick_date, INTERVAL planned_duration DAY) OR finished_date IS NULL) AND archived = 0");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($projects)) {
        echo json_encode(['status' => 'success', 'message' => 'No ongoing projects today.']);
        exit();
    }
    
    // Prepare email content with a table layout
    $subject = "PMS-Daily Ongoing Projects Update";
    $emailBody = "<html><head><style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background-color: #eef2f7; margin: 0; padding: 20px; }
        .container { width: 100%; max-width: 700px; margin: auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, rgb(50, 130, 180) 0%, rgb(100, 175, 220) 100%); color: #fff; padding: 20px; border-radius: 12px 12px 0 0; text-align: center; }
        .header h2 { margin: 0; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: rgb(95, 172, 220); color: #fff; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { text-align: center; padding: 15px; font-size: 14px; color: #666; }
    </style></head><body>
    <div class='container'>
        <div class='header'>
            <h2>Ongoing Projects for Today</h2>
        </div>
        <table>
            <tr>
                <th>Project Name</th>
                <th>Kick-Off Date</th>
                <th>Planned Duration</th>
                <th>Created By</th>
            </tr>";
    
    foreach ($projects as $project) {
        $emailBody .= "<tr>
            <td>" . htmlspecialchars($project['name']) . "</td>
            <td>" . htmlspecialchars($project['kick_date']) . "</td>
            <td>" . htmlspecialchars($project['planned_duration']) . "</td>
            <td>" . htmlspecialchars($project['created_by']) . "</td>
        </tr>";
    }
    
    $emailBody .= "</table>
        <div class='footer'>
            <p>Thank you for using our Tasks, Planning, Progress system.</p>
        </div>
    </div></body></html>";
    
    
    $type='sendAlert';
    // Define recipients
    $sendTo = ['muraciromina@gmail.com']; // Replace with actual recipients
    $CCs = ['muraciromina@gmail.com']; // Replace with CCs if needed
    
    // Send email
    $emailFailures = [];
    foreach ($sendTo as $recipient) {
        $emailResult = EmailService::sendEmail($type, $recipient, $CCs, $subject, $emailBody);
        if (!$emailResult) {
            $emailFailures[] = $recipient;
        }
    }
    
    if (empty($emailFailures)) {
        echo json_encode(['status' => 'success', 'message' => 'Daily project update sent successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email to: ' . implode(', ', $emailFailures)]);
    }
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

// Close database connection
$conn = null;
?>
