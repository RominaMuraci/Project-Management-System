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

// Redirect to login page if the user is not authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}

$userIdDb = $_SESSION['userid'];
$fullname = $_SESSION['login_session'];
$username = $_SESSION['useremail'];

$project_name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
$description = isset($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : '';
$created_by = $fullname;
$created_at = date('Y-m-d H:i:s');

try {
    $stmt = $conn->prepare("INSERT INTO to_do_planning (project_name, created_by, description, created_at)
                            VALUES (:name, :created_by, :description, :created_at)");
    
    $stmt->bindParam(':name', $project_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':created_by', $created_by);
    $stmt->bindParam(':created_at', $created_at);
    
    if ($stmt->execute()) {
        
        $type='sendAlert';
        $sendTo = [$username];
        
        $CCs = [];
        
        // Validate email addresses
        $failedRecipients = [];
        foreach ($CCs as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failedRecipients[] = $email;
            }
        }
        
        if (!empty($failedRecipients)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email address(es): ' . implode(', ', $failedRecipients),
                'failed_recipients' => $failedRecipients
            ]);
            exit();
        }
        
        $subject = "PMS - New Project Added on To DO : $project_name";
        $reviewLink = 'https://billing.protech.com.al/billing-system/PMS/to_do_tasks.php?name=' . urlencode($project_name);
        
        $emailBody = "
        <html>
        <head>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    color: #333;
                    background-color: #f9f9f9;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: #4A90E2;
                    color: #ffffff;
                    padding: 20px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: bold;
                }
                .content {
                    padding: 20px;
                }
                .content p {
                    line-height: 1.6;
                    margin: 0 0 15px;
                    font-size: 14px;
                }
                .content strong {
                    color: #4A90E2;
                }
                .button {
                    display: inline-block;
                    background: #4A90E2;
                     color: #ffffff !important;  /* Force white text */
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 4px;
                    font-size: 14px;
                    margin-top: 10px;
                }
                .button:hover {
                    background: #357ABD;
                }
                .footer {
                    text-align: center;
                    padding: 15px;
                    font-size: 12px;
                    color: #777;
                    background: #f1f1f1;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>New Project Notification for TO DO </h1>
                </div>
                <div class='content'>
                    <p>Dear Admin,</p>
                    <p>A new project has been created by <strong>$created_by</strong>.</p>
                    <p><strong>Project Name:</strong> $project_name</p>
                    <p><strong>Project Description:</strong> $description</p>
                    <p>You can review the project using the link below:</p>
                    <p><a href='$reviewLink' class='button'>Review Project</a></p>
                </div>
                <div class='footer'>
                    <p>This is an automated notification. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Email sending logic
        $emailFailures = [];
        foreach ($sendTo as $recipient) {
            $emailResult = EmailService::sendEmail($type, $recipient, $CCs, $subject, $emailBody);
            
            if (!$emailResult) {
                $emailFailures[] = $recipient;
            }
        }
        
        if (!empty($emailFailures)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send email to: ' . implode(', ', $emailFailures)
            ]);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Project created and emails sent successfully']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}

// Close database connection
$conn = null;
?>
