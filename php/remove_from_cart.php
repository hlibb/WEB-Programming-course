<?php
session_start();
include_once 'include/db_connection.php';

$response = array('success' => false);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $usersId = $_SESSION['users_id']; // Benutzer-ID aus der Sitzung

    // Warenkorb-Kopf-ID abrufen
    $stmt = $link->prepare("SELECT id FROM `cart-header` WHERE users_id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cartId = $row['id'];

        // Warenkorb-Artikel entfernen
        $stmt = $link->prepare("DELETE FROM `cart-body` WHERE warenkorb_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $cartId, $productId);
        $stmt->execute();
        $stmt->close();

        // Gesamtsumme und Artikelanzahl neu berechnen
        $stmt = $link->prepare("SELECT SUM(p.price * cb.quantity) AS total, SUM(cb.quantity) AS count FROM `cart-body` cb JOIN products p ON cb.product_id = p.id WHERE cb.warenkorb_id = ?");
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $response['newTotal'] = $row['total'];
            $response['cartCount'] = $row['count'];
            $response['success'] = true;
        }
        $stmt->close();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
