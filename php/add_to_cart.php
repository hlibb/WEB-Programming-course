<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $usersId = $_SESSION['users_id'] ?? 1;

    $discount = 0;
    if ($quantity >= 10) {
        $discount = 0.20;
    } elseif ($quantity >= 5) {
        $discount = 0.10;
    }

    $stmt = $link->prepare("SELECT * FROM shopping_cart WHERE users_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $usersId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        if ($newQuantity >= 10) {
            $discount = 0.20;
        } elseif ($newQuantity >= 5) {
            $discount = 0.10;
        } else {
            $discount = 0;
        }
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ?, rabatt = ? WHERE users_id = ? AND product_id = ?");
        $stmt->bind_param("idii", $newQuantity, $discount, $usersId, $productId);
    } else {
        $stmt = $link->prepare("INSERT INTO shopping_cart (users_id, product_id, quantity, rabatt) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $usersId, $productId, $quantity, $discount);
    }
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
