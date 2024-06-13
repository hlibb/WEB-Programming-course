<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

$response = ['success' => false, 'message' => 'Ungültiger Gutscheincode.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['coupon_code'])) {
    $couponCode = $_POST['coupon_code'];

    // Check if the coupon exists in the database
    $stmt = $link->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1");
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();
        $discountPercentage = $coupon['discount_percentage'];

        // Fetch cart items
        $userId = $_SESSION['users_id'];
        $stmt = $link->prepare("SELECT p.id, p.name, p.price, cb.quantity, (p.price * cb.quantity) AS product_total 
                                FROM `cart-body` cb
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
            $productTotal = $row['product_total'];

            list($discountAmount, $discountRate) = calculateDiscount($price, $quantity);
            $productTotalAfterDiscount = $productTotal - $discountAmount;

            $totalPrice += $productTotalAfterDiscount;
        }

        // Apply coupon discount
        $totalPriceAfterCoupon = $totalPrice * (1 - $discountPercentage / 100);

        $response = ['success' => true, 'new_total_price' => $totalPriceAfterCoupon];
    }

    $stmt->close();
}

echo json_encode($response);
?>
