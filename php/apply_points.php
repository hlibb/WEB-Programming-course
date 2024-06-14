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
    $pointsData = $pointsResult->fetch_assoc();
    $points = $pointsData['points'];
    $pointsStmt->close();

    $pointsValue = $points / 1000;

    // Update is_active based on use_points
    $isActive = $usePoints ? 1 : 0;
    $updateStmt = $link->prepare("UPDATE points SET is_active = ? WHERE users_id = ?");
    $updateStmt->bind_param("ii", $isActive, $userId);
    $updateStmt->execute();
    $updateStmt->close();

    echo json_encode([
        'points' => $points,
        'points_value' => $pointsValue,
    ]);
}

$link->close();
?>
