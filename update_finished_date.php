<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the root directory and include the database connection
$rootDir = __DIR__ . '../../';
include '../Classes/EmailService.php';
$conn = include($rootDir . 'config/connection.php');

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}
// Fetch user data from session
$userIdDb = $_SESSION['userid'];
$fullname = $_SESSION['login_session'];
$username = $_SESSION['useremail'];

// Validate input
$taskId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$finishedDate = isset($_POST['finished_date']) ? $_POST['finished_date'] : '';

if (empty($taskId) || empty($finishedDate)) {
    echo json_encode(['status' => 'error', 'message' => 'Task ID or Finished Date is missing.']);
    exit;
}

try {
    // Check if the project exists in the database
    $checkQuery = $conn->prepare("SELECT finished_date, name, created_by FROM projects_planning WHERE id = :id");
    $checkQuery->bindParam(':id', $taskId, PDO::PARAM_INT);
    $checkQuery->execute();
    $project = $checkQuery->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo json_encode(['status' => 'error', 'message' => 'Project not found.']);
        exit;
    }

    $currentFinishedDate = $project['finished_date'];
    $name = $project['name'];
    $created_by = $project['created_by'];
    
    // Only update if the finished_date is still '0000-00-00'
    if ($currentFinishedDate == '0000-00-00') {
        $stmt = $conn->prepare("UPDATE projects_planning SET finished_date = :finished_date WHERE id = :id");
        $stmt->bindParam(':finished_date', $finishedDate, PDO::PARAM_STR);
        $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // After updating the finished date, fetch the total hours worked for this project
            $hoursQuery = $conn->prepare("
                SELECT COALESCE(SUM(th.hours_worked), 0) AS total_hours
                FROM task_hours th
                WHERE th.project_id = :project_id
            ");
            
            $hoursQuery->bindParam(':project_id', $taskId, PDO::PARAM_INT);
            $hoursQuery->execute();
            $hoursResult = $hoursQuery->fetch(PDO::FETCH_ASSOC);
            $total_hours = $hoursResult ? $hoursResult['total_hours'] : 0;
            
            
            $sendTo = $username; // Assuming $username contains the recipient email
            $CCs = [];
            

            $subject = "PMS-Project Update: Finished Date Added/Updated for $name";
            $reviewLink = 'https://billing.protech.com.al/billing-system/PMS/projects.php?name=' . urlencode($name);

            // Build email content
            $emailBody = "
            <html>
            <head>
                <style>
                   body {
    font-family: Arial, sans-serif;
    color: #333;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    width: 90%;
    max-width: 600px;
    margin: 30px auto;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.header {
    background: #007bff;
    color: #fff;
    padding: 20px;
    text-align: center;
    border-bottom: 4px solid #0056b3;
}

.header h1 {
    margin: 0;
    font-size: 26px;
}

.content {
    padding: 20px 30px;
}

.content p {
    line-height: 1.6;
}

.footer {
    background: #f8f9fa;
    color: #555;
    text-align: center;
    padding: 15px;
    font-size: 14px;
}

a {
     background: #007bff;

    color: #fff !important;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    display: inline-block;
    margin-top: 20px;
}

a:hover {
   background: rgb(69, 156, 243);
   color: #fff !important;
}

                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Project Update</h1>
                    </div>
                    <div class='content'>
                        <p>Dear Admin,</p>
                        <p>The project <strong>" . htmlspecialchars($name) . "</strong> has been updated by <strong>" . htmlspecialchars($created_by) . "</strong>.</p>
                        <p><strong>New Finished Date:</strong> " . htmlspecialchars($finishedDate) . "</p>
                        <p><strong>Total Hours Worked:</strong> " . htmlspecialchars($total_hours) . "</p>
                        <p>You can review the project details using the link below:</p>
                        <p><a href='" . htmlspecialchars($reviewLink) . "'>Review Project</a></p>
                    </div>
                    <div class='footer'>
                        <p>Thank you for using our Tasks, Planning, and Progress system.</p>
                    </div>
                </div>
            </body>
            </html>";

            // Send email to admin
            $emailSent = EmailService::sendEmail('sendAlert', $sendTo, $CCs, $subject, $emailBody);

            if ($emailSent) {
                echo json_encode(['status' => 'success', 'message' => 'Finished date updated and email sent to admin.']);
            } else {
                echo json_encode(['status' => 'warning', 'message' => 'Finished date updated, but email sending failed.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update finished date.', 'error' => $stmt->errorInfo()]);
        }
    } else {
        echo json_encode(['status' => 'info', 'message' => 'Finished date already set, no update needed.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
