<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['article_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['article_id'];
    $quantity = $_POST['quantity'];
    $usersId = $_SESSION['users_id'] ?? 1;

    $discount = 0;
    if ($quantity >= 10) {
        $discount = 0.20;
    } elseif ($quantity >= 5) {
        $discount = 0.10;
    }

    $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ?, rabatt = ? WHERE users_id = ? AND product_id = ?");
    $stmt->bind_param("idii", $quantity, $discount, $usersId, $productId);
    $stmt->execute();
    $stmt->close();

    // Aktualisieren der Anzahl der Artikel im Warenkorb
    $stmt = $link->prepare("SELECT SUM(quantity) AS cart_count FROM shopping_cart WHERE users_id = ?");
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
?>
