<?php
function addToCart($userId, $productId, $quantity, $link) {
    // Check if cart-header already exists for the user
    $stmt = $link->prepare("SELECT id FROM `cart-header` WHERE users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Get the cart-header id
        $row = $result->fetch_assoc();
        $cartHeaderId = $row['id'];
    } else {
        // Create a new cart-header
        $stmt = $link->prepare("INSERT INTO `cart-header` (users_id) VALUES (?)");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cartHeaderId = $stmt->insert_id;
    }
    $stmt->close();

    // Check if product is already in the cart
    $stmt = $link->prepare("SELECT quantity FROM `cart-body` WHERE warenkorb_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cartHeaderId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the quantity if product is already in the cart
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        $stmt = $link->prepare("UPDATE `cart-body` SET quantity = ? WHERE warenkorb_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $newQuantity, $cartHeaderId, $productId);
    } else {
        // Insert into cart-body
        $stmt = $link->prepare("INSERT INTO `cart-body` (warenkorb_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cartHeaderId, $productId, $quantity);
    }
    $stmt->execute();
    $stmt->close();
}
?>
