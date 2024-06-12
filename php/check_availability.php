<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['article_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['article_id'];
    $quantity = $_POST['quantity'];

    $stmt = $link->prepare("SELECT quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $available = false;
    $availableQuantity = 0;
    if ($row = $result->fetch_assoc()) {
        $availableQuantity = $row['quantity'];
        if ($quantity <= $availableQuantity) {
            $available = true;
        }
    }
    $stmt->close();

    echo json_encode(['success' => true, 'available' => $available, 'available_quantity' => $availableQuantity]);
} else {
    echo json_encode(['success' => false]);
}
?>
