<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['article_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['article_id'];
    $quantity = $_POST['quantity'];

    $discount = 0;
    if ($quantity >= 10) {
        $discount = 0.20;
    } elseif ($quantity >= 5) {
        $discount = 0.10;
    }

    echo json_encode(['success' => true, 'discount' => $discount]);
} else {
    echo json_encode(['success' => false]);
}
?>
