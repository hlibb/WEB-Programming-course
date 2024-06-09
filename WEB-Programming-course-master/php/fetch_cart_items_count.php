<?php
session_start();
include_once 'db_connection.php';

$userId = $_SESSION['user_id'] ?? 1;

$stmt = $link->prepare("SELECT COUNT(*) as item_count FROM shopping_cart WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$itemCount = $row['item_count'];

$stmt->close();
$link->close();

echo $itemCount;
?>
