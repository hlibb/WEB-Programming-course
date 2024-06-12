<?php
include_once 'include/db_connection.php';
session_start();

$usersId = $_SESSION['users_id'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['use_points'])) {
    $usePoints = $_POST['use_points'];

    // Calculate total price from the shopping cart
    $stmt = $link->prepare("SELECT sc.product_id, p.name, p.price, sc.quantity, sc.rabatt FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.users_id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalPrice = 0;
    while ($row = $result->fetch_assoc()) {
        $discountedPrice = $row['price'] * (1 - $row['rabatt']);
        $totalPrice += $discountedPrice * $row['quantity'];
    }
    $stmt->close();

    // Fetch user points
    $stmt = $link->prepare("SELECT points FROM punkte WHERE users_id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $stmt->bind_result($points);
    $stmt->fetch();
    $stmt->close();

    // Calculate discount
    $pointsDiscount = 0;
    if ($usePoints) {
        $pointsDiscount = $points / 1000;
    }

    // Calculate the new total price with discount
    $newTotalPrice = $totalPrice - $pointsDiscount;

    // Return the new total price as the response
    echo number_format($newTotalPrice, 2);
}
?>
