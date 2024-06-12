<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['article_id'])) {
    $productId = $_POST['article_id'];
    $usersId = $_SESSION['users_id'] ?? 1;

    $stmt = $link->prepare("DELETE FROM shopping_cart WHERE users_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $usersId, $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt = $link->prepare("SELECT COUNT(*) AS cart_count FROM shopping_cart WHERE users_id = ?");
        $stmt->bind_param("i", $usersId);
        $stmt->execute();
        $result = $stmt->get_result();
        $cartCount = 0;
        if ($row = $result->fetch_assoc()) {
            $cartCount = $row['cart_count'];
        }
        $stmt->close();
        echo json_encode(['success' => true, 'cart_count' => $cartCount]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
