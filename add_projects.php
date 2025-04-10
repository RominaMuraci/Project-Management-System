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

// Fetch user data from session
$userIdDb = $_SESSION['userid'];
$fullname = $_SESSION['login_session'];
$username = $_SESSION['useremail'];


// Simple email validation (check if it contains '@' and '.')
if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email address: ' . $username,
        'failed_recipients' => [$username]
    ]);
    exit();
}

try {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);


// Sanitize and validate POST inputs
$name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
$kick_date = isset($_POST['kick_date']) ? trim($_POST['kick_date']) : '';
$planned_duration = isset($_POST['planned_duration']) ? trim($_POST['planned_duration']) : '';
$created_by = $fullname; // Using session value for the created_by field
$user_id = $userIdDb;

// Validate required fields
if (empty($name) || empty($kick_date) || empty($planned_duration)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

try {
    // Prepare SQL query using parameterized queries to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO projects_planning (name, kick_date, planned_duration, created_by, user_id) 
                            VALUES (:name, :kick_date, :planned_duration, :created_by,:user_id)");

    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':kick_date', $kick_date);
    $stmt->bindParam(':planned_duration', $planned_duration);
    $stmt->bindParam(':created_by', $created_by);
    $stmt->bindParam(':user_id', $user_id);

    // Execute query
    if ($stmt->execute()) {
        $type='sendAlert';
        // Email details // Replace with admin email
  
        $sendTo = [$username]; // Assuming $username contains the recipient email
        $CCs = [];

        // Initialize an array to store failed emails
        $failedRecipients = [];
        
        foreach ($CCs as $email) {
            // Validate each email address
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // If invalid, add to the failed recipients array
                $failedRecipients[] = $email;
            }
        }
        // Check if there were any invalid emails
if (!empty($failedRecipients)) {
    // If there are failed recipients, return an error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email address(es): ' . implode(', ', $failedRecipients),
        'failed_recipients' => $failedRecipients
    ]);
    exit();
}


        $subject = "PMS-New Project Created: $name";
        $reviewLink = 'https://billing.protech.com.al/billing-system/PMS/projects.php?name=' . urlencode($name);

        // Build email content
        $emailBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; background-color: #f4f4f9; margin: 0; padding: 0; }
                .container { width: 90%; max-width: 600px; margin: 30px auto; background: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 8px; }
                .header { background: #007bff; color: #fff; padding: 20px; text-align: center; border-bottom: 4px solid #0056b3; }
                .header h1 { margin: 0; font-size: 26px; }
                .content { padding: 20px 30px; }
                .content p { line-height: 1.6; }
                .content ul { margin: 15px 0; padding-left: 20px; }
                .footer { background: #f8f9fa; color: #555; text-align: center; padding: 15px; font-size: 14px; }
           a {
    background: #007bff;  /* Primary blue */
    color: #ffffff !important;  /* Force white text */
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 6px;
    display: inline-block;
    margin-top: 20px;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
}

/* 🔥 Improved Hover Effect */
a:hover {
    background: #0056b3 !important;  /* Darker blue */
    color: #ffffff !important;  /* Ensure white text */
    transform: translateY(-3px);
    box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.3);
}


            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>New Project Notification</h1>
                </div>
                <div class='content'>
                    <p>Dear Admin,</p>
                    <p>A new project has been created by <strong>" . htmlspecialchars($created_by) . "</strong>.</p>
                    <p><strong>Project Details:</strong></p>
                    <ul>
                        <li><strong>Name:</strong> " . htmlspecialchars($name) . "</li>
                        <li><strong>Kick-Off Date:</strong> " . htmlspecialchars($kick_date) . "</li>
                        <li><strong>Planned Duration:</strong> " . htmlspecialchars($planned_duration) . "</li>
                    </ul>
                    <p>You can review the project details using the link below:</p>
                    <p><a href='" . htmlspecialchars($reviewLink) . "'>Review Project</a></p>
                </div>
                <div class='footer'>
                    <p>Thank you for using our Tasks,Planning,Progress system.</p>
                </div>
            </div>
        </body>
        </html>";

        $emailSent = true;
        $emailFailures = []; // Initialize an array to collect failed recipients
        
        foreach ($sendTo as $recipient) {
            $emailResult = EmailService::sendEmail($type, $recipient, $CCs, $subject, $emailBody);
            
            if (!$emailResult) {
                // Collect failed recipient and add it to the array
                $emailFailures[] = $recipient;
            }
        }
        
        if ($emailSent && empty($emailFailures)) {
            echo json_encode(['status' => 'success', 'message' => 'Project added successfully, and email notification sent.']);
        } else {
            $failureMessage = 'Email notification failed for the following recipients: ' . implode(', ', $emailFailures);
            echo json_encode(['status' => 'success', 'message' => $failureMessage, 'failed_recipients' => $emailFailures]);
        }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add project. Please try again later.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
} catch (Exception $e) {
    // Handle errors gracefully
    echo json_encode([
        'status' => 'error',
        'message' => 'Session validation failed: ' . $e->getMessage()
    ]);
    exit();
}

// Close database connection
$conn = null;
?>
