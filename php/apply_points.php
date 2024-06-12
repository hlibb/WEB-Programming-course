<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['use_points'])) {
    $usersId = $_SESSION['users_id'] ?? 1;
    $usePoints = $_POST['use_points'];

    $stmt = $link->prepare("SELECT points FROM punkte WHERE users_id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userPoints = 0;
    if ($row = $result->fetch_assoc()) {
        $userPoints = $row['points'];
    }
    $stmt->close();

    $pointsDiscountValue = 0;
    if ($usePoints == 1) {
        $stmt = $link->prepare("SELECT SUM(p.price * sc.quantity * (1 - sc.rabatt)) AS totalPrice FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.users_id = ?");
        $stmt->bind_param("i", $usersId);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalPrice = 0;
        if ($row = $result->fetch_assoc()) {
            $totalPrice = $row['totalPrice'];
        }
        $stmt->close();

        $pointsDiscount = min($userPoints, $totalPrice * 10); // max 10 Punkte pro 1€
        $pointsDiscountValue = $pointsDiscount * 0.10;
    }

    echo json_encode(['success' => true, 'points_discount' => $pointsDiscountValue]);
} else {
    echo json_encode(['success' => false]);
}
?>
