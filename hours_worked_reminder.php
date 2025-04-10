<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$rootDir = __DIR__ . '../../';
include $rootDir . 'Classes/EmailService.php';
$conn = include($rootDir . 'config/connection.php');

if (!$conn) {
    die("Database connection failed.");
}

try {
    $startDate = date('Y-m-d', strtotime('-2 days'));
    $endDate = date('Y-m-d', strtotime('-1 day'));
    
    // Fetch all distinct users who have projects
    $userQuery = "SELECT DISTINCT u.userid, u.firstname, u.lastname, u.email
                  FROM users u
                  INNER JOIN projects_planning pp ON u.userid = pp.user_id";
    
    $userStmt = $conn->query($userQuery);
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Array to store all missing dates per user (for admin email)
    $adminMissingDates = [];
    
    foreach ($users as $user) {
        $userId = $user['userid'];
        $fullname = trim($user['firstname'] . ' ' . $user['lastname']); // Concatenate first and last name
        $userEmail = $user['email'];
        
        // Fetch work dates for this user
        $query = "SELECT th.work_date
                  FROM task_hours th
                  INNER JOIN projects_planning pp ON th.project_id = pp.id
                  WHERE th.work_date BETWEEN :start_date AND :end_date
                  AND pp.user_id = :employee_id
                  GROUP BY th.work_date";
        
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->bindValue(':employee_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $filledDates = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Generate missing dates
        $period = new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate . ' +1 day'));
        $missingDates = array_diff(array_map(function ($d) {
            return $d->format('Y-m-d');
        }, iterator_to_array($period)), $filledDates);
        
        if (!empty($missingDates)) {
            // Store missing dates for admin
            $adminMissingDates[$fullname] = $missingDates;
            
            // Send individual email to user
            $emailBody = "<div style='font-family: Arial, sans-serif; padding: 15px; border: 1px solid #ccc; background-color: #f9f9f9; max-width: 500px;'>
                            <h2 style='color: #333; text-align: center;'>Dear $fullname,</h2>
                            <p style='text-align: center;'>We noticed that you haven't filled in your work hours for the following dates:</p>";
            
            $emailBody .= "<table style='width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.2);'>
                            <thead>
                                <tr style='background-color: #2575fc; color: white; text-align: center;'>
                                    <th style='padding: 10px; font-size: 14px;'>Missing Dates</th>
                                </tr>
                            </thead>
                            <tbody>";
            
            foreach ($missingDates as $date) {
                $emailBody .= "<tr>
                                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center; font-size: 13px;'>$date</td>
                               </tr>";
            }
            
            $emailBody .= "</tbody></table>
                            <p style='color: #555; text-align: center; margin-top: 15px;'>Please update them at your earliest convenience.</p>
                            <br><p style='font-weight: bold; text-align: center;'>Thank you,<br>Project Management Team</p>
                          </div>";
            
            // Send email to user
            $subject_email = "PMS-Reminder: Please Fill Your Work Hours";
            $CCs = [];
            
            $emailSent = EmailService::sendEmail('sendAlert', $userEmail, $CCs, $subject_email, $emailBody);
            echo $emailSent ? "Reminder sent to $userEmail<br>" : "Failed to send reminder to $userEmail<br>";
        }
    }
    
    if (!empty($adminMissingDates)) {
        $adminEmailBody = "<div style='font-family: Arial, sans-serif; padding: 15px; border: 1px solid #ccc; background-color: #f9f9f9; max-width: 600px;'>
                            <h2 style='color: #333; text-align: center;'>Project Hours Report</h2>
                            <p style='text-align: center;'>Here are the employees with missing work hours:</p>";
        
        $adminEmailBody .= "<table style='width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.2);'>
                            <thead>
                                <tr style='background-color: #2575fc; color: white; text-align: center;'>
                                    <th style='padding: 10px; font-size: 14px;'>Employee</th>
                                    <th style='padding: 10px; font-size: 14px;'>Missing Dates</th>
                                </tr>
                            </thead>
                            <tbody>";
        
        foreach ($adminMissingDates as $employeeName => $dates) {
            $adminEmailBody .= "<tr>
                                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center; font-size: 13px; font-weight: bold;'>$employeeName</td>
                                <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center; font-size: 13px;'>" . implode(", ", $dates) . "</td>
                               </tr>";
        }
        
        $adminEmailBody .= "</tbody></table>
                            <p style='color: #555; text-align: center; margin-top: 15px;'>Please follow up accordingly.</p>
                            <br><p style='font-weight: bold; text-align: center;'>Thank you,<br>Project Management Team</p>
                          </div>";
        
        // Send email to the admin (Only ONCE after processing all users)
        $adminSubject = "PMS-Admin Report: Missing Work Hours";
        $adminCCs = ["muraciromina@gmail.com"];
        
        $adminEmail = 'muraciromina@gmail.com'; // Explicit admin email
        $adminEmailSent = EmailService::sendEmail('sendAlert', $adminEmail, $adminCCs, $adminSubject, $adminEmailBody);
        if (!$adminEmailSent) {
            echo "Failed to send admin email. Check EmailService logs.";
        } else {
            echo "Admin report sent successfully.";
        }
    }
    
    echo "Task hours check completed.<br>";
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}
?>
