<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $link->prepare("SELECT quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['quantity'] >= $quantity) {
            echo json_encode(['success' => true, 'available' => true]);
        } else {
            echo json_encode(['success' => true, 'available' => false, 'available_quantity' => $row['quantity']]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
}

$link->close();
?>
