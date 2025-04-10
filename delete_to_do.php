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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = $_POST['id'];

    try {
        $query = "DELETE FROM to_do_planning WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $projectId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete the project.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
