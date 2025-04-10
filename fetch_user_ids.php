<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php'); // Ensure this returns a PDO instance

if (!$conn) {
    die("Database connection failed.");
}


$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname  = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';
$userEmail = isset($_SESSION['useremail']) ? $_SESSION['useremail'] : '';


// Define allowed admin emails

$allowedAdmins = ['muraciromina@gmail.com'];
$isAdmin = in_array($userEmail, $allowedAdmins);  // Check if logged-in user is an allowed admin

$users = [];

try {
    if ($isAdmin) {
        // If the user is an allowed admin, fetch all users
        $sql = "SELECT userid, firstname, lastname FROM users WHERE accesslevel IN ('admin', 'noc', 'other', 'user')";
        $stmt = $conn->query($sql);
    } else {
        // If not an admin, fetch only the logged-in user's data
        $sql = "SELECT userid, firstname, lastname FROM users WHERE userid = :userid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userIdDb, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // Fetch the results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = [
            'userid' => htmlspecialchars($row['userid']),
            'fullname' => htmlspecialchars($row['firstname'] . " " . $row['lastname']),
        ];
    }
} catch (PDOException $e) {
    error_log("Query failed: " . $e->getMessage());
    echo json_encode(['error' => 'Error fetching users.']);
    exit();
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode([
    'allUsers' => $users,
    'loggedInUser' => [
        'userid' => $userIdDb,
        'fullname' => $fullname,
        'isadmin' => $isAdmin  // Now returns true/false based on allowedAdmins
    ]
]);
?>
