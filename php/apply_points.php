<?php
session_start();
include_once 'include/db_connection.php';

$response = array('success' => false);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['use_points'])) {
    $usePoints = $_POST['use_points'];
    $usersId = $_SESSION['users_id']; // Benutzer-ID aus der Sitzung

    // Warenkorb-Kopf-ID abrufen
    $stmt = $link->prepare("SELECT id FROM `cart-header` WHERE users_id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cartId = $row['id'];

        // Benutzerpunkte abrufen
        $stmt = $link->prepare("SELECT points FROM points WHERE users_id = ?");
        $stmt->bind_param("i", $usersId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userPoints = 0;
        if ($row = $result->fetch_assoc()) {
            $userPoints = $row['points'];
        }
        $stmt->close();

        // Warenkorb-Artikel abrufen
        $stmt = $link->prepare("SELECT SUM(p.price * cb.quantity) AS total FROM `cart-body` cb JOIN products p ON cb.product_id = p.id WHERE cb.warenkorb_id = ?");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalPrice = 0;
        if ($row = $result->fetch_assoc()) {
            $totalPrice = $row['total'];
        }
        $stmt->close();

        // Punkte-Rabatt berechnen
        $pointsDiscountValue = 0;
        if ($usePoints == '1') {
            $pointsDiscount = min($userPoints, $totalPrice * 10);
            $pointsDiscountValue = $pointsDiscount * 0.10;
            $totalPrice -= $pointsDiscountValue;
        }

        // Rückgabe der neuen Gesamtsumme
        $response['newTotal'] = $totalPrice;
        $response['pointsDiscountValue'] = $pointsDiscountValue;
        $response['success'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
