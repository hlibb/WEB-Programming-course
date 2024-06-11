<?php
include_once 'include/db_connection.php';
session_start();

$kundenId = $_SESSION['kunden_id'] ?? 1;

if ($stmt = $link->prepare("SELECT points FROM punkte WHERE kunden_id = ?")) {
    $stmt->bind_param("i", $kundenId);
    $stmt->execute();
    $stmt->bind_result($points);
    $stmt->fetch();
    $stmt->close();
    echo $points;
} else {
    echo "Error: " . $link->error;
}
?>