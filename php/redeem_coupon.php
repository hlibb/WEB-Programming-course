<?php
session_start();
include_once 'include/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $couponCode = $_POST['couponCode'];
    $userId = $_SESSION['users_id'];

    // Überprüfen, ob der Gutscheincode existiert und gültig ist
    $stmt = $link->prepare("SELECT discount_percentage FROM coupons WHERE code = ? AND expiry_date >= CURDATE()");
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $stmt->bind_result($discountPercentage);
    $stmt->fetch();
    $stmt->close();

    if ($discountPercentage) {
        // Berechnen Sie den neuen Gesamtpreis
        $stmt = $link->prepare("SELECT p.price, cb.quantity FROM `cart-body` cb
                                JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                                JOIN `products` p ON cb.product_id = p.id
                                WHERE ch.users_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $totalPrice = 0;
        while ($row = $result->fetch_assoc()) {
            $price = $row['price'];
            $quantity = $row['quantity'];
            $totalPrice += $price * $quantity;
        }
        $stmt->close();

        $discountAmount = $totalPrice * ($discountPercentage / 100);
        $newTotalPrice = $totalPrice - $discountAmount;

        echo json_encode([
            'success' => true,
            'newTotalPrice' => number_format($newTotalPrice, 2),
            'discountAmount' => number_format($discountAmount, 2)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
