<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once 'include/db_connection.php';

$response = [
    'success' => false,
    'message' => '',
    'newTotalPrice' => 0,
    'discountAmount' => 0
];

session_start();

if (isset($_POST['couponCode']) && !empty($_POST['couponCode'])) {
    $couponCode = trim($_POST['couponCode']);
    $userId = $_SESSION['users_id'];

    $stmt = $link->prepare("SELECT discount_percentage FROM coupons WHERE code = ?");
    if ($stmt === false) {
        $response['message'] = 'Prepare failed: ' . htmlspecialchars($link->error);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("s", $couponCode);
    if (!$stmt->execute()) {
        $response['message'] = 'Execute failed: ' . htmlspecialchars($stmt->error);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    $stmt->bind_result($discountPercentage);
    $stmt->fetch();
    $stmt->close();

    if ($discountPercentage) {
        // Fetch the current total price after applying product discounts
        $stmt = $link->prepare("SELECT SUM((p.price * cb.quantity) - (p.price * cb.quantity * (cb.rabatt / 100))) AS total
                                FROM `cart-body` cb
                                JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                                JOIN `products` p ON cb.product_id = p.id
                                WHERE ch.users_id = ?");
        if ($stmt === false) {
            $response['message'] = 'Prepare failed: ' . htmlspecialchars($link->error);
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }

        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            $response['message'] = 'Execute failed: ' . htmlspecialchars($stmt->error);
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }

        $stmt->bind_result($totalPrice);
        $stmt->fetch();
        $stmt->close();

        // Check if points are active and apply points discount if applicable
        $pointsStmt = $link->prepare("SELECT points, is_active FROM points WHERE users_id = ?");
        $pointsStmt->bind_param("i", $userId);
        $pointsStmt->execute();
        $pointsResult = $pointsStmt->get_result();
        $pointsData = $pointsResult->fetch_assoc();
        $pointsStmt->close();

        $userPoints = $pointsData['points'];
        $pointsActive = $pointsData['is_active'];
        $pointsValue = $pointsActive ? $userPoints / 1000 : 0;
        $totalPrice -= $pointsValue; // Subtract points value from total price

        if ($totalPrice) {
            $discountAmount = $totalPrice * ($discountPercentage / 100);
            $newTotalPrice = $totalPrice - $discountAmount;

            $response['success'] = true;
            $response['newTotalPrice'] = number_format($newTotalPrice, 2);
            $response['discountAmount'] = number_format($discountAmount, 2);
        } else {
            $response['message'] = 'Could not calculate total price.';
        }
    } else {
        $response['message'] = 'Invalid coupon code.';
    }
} else {
    $response['message'] = 'Coupon code is required.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
