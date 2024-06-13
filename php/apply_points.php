<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['users_id'];
    $usePoints = json_decode(file_get_contents("php://input"), true)['use_points'] ?? false;

    // Get user points
    $pointsStmt = $link->prepare("SELECT points FROM points WHERE users_id = ?");
    $pointsStmt->bind_param("i", $userId);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    $points = $pointsResult->fetch_assoc()['points'];
    $pointsStmt->close();

    $pointsValue = $points / 1000;

    echo json_encode([
        'points' => $points,
        'points_value' => $pointsValue,
    ]);
}

$link->close();
?>
