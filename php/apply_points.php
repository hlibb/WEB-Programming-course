<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

header('Content-Type: application/json'); // Setzen des Content-Types für JSON-Antworten

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['users_id'];
    $usePoints = json_decode(file_get_contents("php://input"), true)['use_points'] ?? false;

    // Get total cart amount
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

    // Calculate points discount
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

    echo json_encode([
        'total' => (float) $total,
        'points_discount' => number_format($pointsDiscount, 2) . '€'
    ]);
}

$link->close();
?>
