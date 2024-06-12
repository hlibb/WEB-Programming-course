<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $kundenId = $_SESSION['kunden_id'] ?? 1;

    if ($quantity == 0) {
        $stmt = $link->prepare("DELETE FROM shopping_cart WHERE kunden_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $kundenId, $productId);
    } else {
        // Berechnung des Rabatts basierend auf der aktualisierten Menge
        $discount = 0;
        if ($quantity >= 10) {
            $discount = 0.20;
        } elseif ($quantity >= 5) {
            $discount = 0.10;
        }
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ?, rabatt = ? WHERE kunden_id = ? AND product_id = ?");
        $stmt->bind_param("idii", $quantity, $discount, $kundenId, $productId);
    }
    $stmt->execute();
    $stmt->close();
}
?>
