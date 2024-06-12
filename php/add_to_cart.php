<?php
include_once 'include/db_connection.php';

function addToCart($userId, $productId, $quantity, $link) {
    // Check if the user has an existing cart header
    $stmt = $link->prepare("SELECT id FROM `cart-header` WHERE users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Cart header exists, use its ID
        $cartId = $row['id'];
    } else {
        // No cart header exists, create a new one
        $stmt = $link->prepare("INSERT INTO `cart-header` (users_id) VALUES (?)");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cartId = $stmt->insert_id;
    }
    $stmt->close();

    // Check if the product is already in the cart
    $stmt = $link->prepare("SELECT id, quantity FROM `cart-body` WHERE warenkorb_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cartId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Product is already in the cart, update the quantity
        $newQuantity = $row['quantity'] + $quantity;
        $stmt = $link->prepare("UPDATE `cart-body` SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $newQuantity, $row['id']);
    } else {
        // Product is not in the cart, insert a new row
        $stmt = $link->prepare("INSERT INTO `cart-body` (warenkorb_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cartId, $productId, $quantity);
    }
    $stmt->execute();
    $stmt->close();
}
?>
