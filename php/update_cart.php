<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

header('Content-Type: application/json');

function calculateDiscount($price, $quantity) {
    if ($quantity >= 10) {
        $discountRate = 0.20;
    } elseif ($quantity >= 5) {
        $discountRate = 0.10;
    } else {
        $discountRate = 0.00;
    }

    $discountAmount = $price * $quantity * $discountRate;
    return [$discountAmount, $discountRate];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['users_id'];
    $data = json_decode(file_get_contents("php://input"), true);
    $productId = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? null;
    $usePoints = $data['use_points'] ?? false;

    if ($productId !== null && $quantity !== null) {
        if ($quantity > 0) {
            $stmt = $link->prepare("UPDATE `cart-body` cb 
                                    JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                                    SET cb.quantity = ? 
                                    WHERE ch.users_id = ? AND cb.product_id = ?");
            $stmt->bind_param("iii", $quantity, $userId, $productId);
        } else {
            $stmt = $link->prepare("DELETE cb 
                                    FROM `cart-body` cb 
                                    JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                                    WHERE ch.users_id = ? AND cb.product_id = ?");
            $stmt->bind_param("ii", $userId, $productId);
        }
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $link->prepare("SELECT p.id, p.price, cb.quantity, (p.price * cb.quantity) AS product_total
                            FROM `cart-body` cb
                            JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                            JOIN `products` p ON cb.product_id = p.id
                            WHERE ch.users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartItems = [];
    $total = 0;
    $totalDiscount = 0;
    while ($row = $result->fetch_assoc()) {
        $productId = $row['id'];
        $price = $row['price'];
        $quantity = $row['quantity'];
        $productTotal = $row['product_total'];

        list($discountAmount, $discountRate) = calculateDiscount($price, $quantity);
        $discountDisplay = number_format($discountAmount, 2) . '€ (' . ($discountRate * 100) . '%)';
        $productTotalAfterDiscount = $productTotal - $discountAmount;

        $cartItems[] = [
            'id' => $productId,
            'quantity' => $quantity,
            'product_total' => $productTotalAfterDiscount,
            'discount' => $discountDisplay
        ];

        $total += $productTotalAfterDiscount;
        $totalDiscount += $discountAmount;
    }

    $pointsDiscount = 0;
    if ($usePoints) {
        $stmt = $link->prepare("SELECT points FROM points WHERE users_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $points = $result->fetch_assoc()['points'];
        $stmt->close();

        $pointsDiscount = min($points / 1000, $total);
        $total -= $pointsDiscount;
    }

    $stmt->close();

    echo json_encode([
        'total' => (float) $total,
        'total_discount' => number_format($totalDiscount, 2) . '€',
        'points_discount' => number_format($pointsDiscount, 2) . '€',
        'cart_items' => $cartItems
    ]);
}

$link->close();
?>
