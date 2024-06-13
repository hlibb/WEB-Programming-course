<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

header('Content-Type: application/json'); // Setzen des Content-Types für JSON-Antworten

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['users_id'];
    $data = json_decode(file_get_contents("php://input"), true);
    $productId = $data['product_id'];

    // Delete the entry from the cart-body table
    $stmt = $link->prepare("DELETE cb 
                            FROM `cart-body` cb 
                            JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                            WHERE ch.users_id = ? AND cb.product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $stmt->close();

    // Calculate the new total price
    $stmt = $link->prepare("SELECT SUM(p.price * cb.quantity) AS total
                            FROM `cart-body` cb
                            JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                            JOIN `products` p ON cb.product_id = p.id
                            WHERE ch.users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    $stmt->close();

    echo json_encode(['total' => (float) $total]);
}

$link->close();
?>
