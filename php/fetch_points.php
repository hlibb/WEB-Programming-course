<?php
include_once 'include/db_connection.php';
session_start();

$usersId = $_SESSION['users_id'] ?? 1;

if ($stmt = $link->prepare("SELECT points FROM punkte WHERE users_id = ?")) {
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $stmt->bind_result($points);
    $stmt->fetch();
    $stmt->close();
    echo $points;
} else {
    echo "Error: " . $link->error;
}
?>