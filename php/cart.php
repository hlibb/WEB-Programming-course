<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

$cartItems = [];
$totalPrice = 0;
$userPoints = 0;
$totalDiscount = 0; // Gesamtrabatt initialisieren

// Warenkorb anzeigen
$usersId = $_SESSION['users_id'] ?? 1;

$stmt = $link->prepare("SELECT sc.product_id, p.name, p.price, sc.quantity, sc.rabatt FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.users_id = ?");
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $discountedPrice = $row['price'] * (1 - $row['rabatt']);
    $itemTotal = $discountedPrice * $row['quantity'];
    $totalPrice += $itemTotal;
    $totalDiscount += ($row['price'] * $row['quantity']) * $row['rabatt']; // Gesamtrabatt berechnen
}
$stmt->close();

// Benutzerpunkte abrufen
$stmt = $link->prepare("SELECT points FROM punkte WHERE users_id = ?");
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $userPoints = $row['points'];
}
$stmt->close();

// Punkte-Rabatt berechnen
$pointsDiscount = 0;
if (isset($_POST['use_points']) && $_POST['use_points'] == '1') {
    $pointsDiscount = min($userPoints, $totalPrice * 1000); // max 1000 Punkte pro 1€
    $pointsDiscountValue = $pointsDiscount * 0.001;
    $totalPrice -= $pointsDiscountValue;
}

// Weiterleitung zur Checkout-Seite, wenn der Bezahlen-Knopf gedrückt wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    header("Location: checkout.php");
    exit();
}

$link->close(); // Schließe die Verbindung am Ende des Skripts
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Warenkorb</title>
    <?php include '../php/include/headimport.php' ?>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Ihr Warenkorb</h1>
    <div class="table-container">
        <table class="table table-bordered mt-3">
            <thead>
            <tr>
                <th>Produkt</th>
                <th>Preis</th>
                <th>Menge</th>
                <th>Rabatt</th>
                <th>Gesamt</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($cartItems as $item) {
                $discount = $item['rabatt'];
                $discountedPrice = $item['price'] * (1 - $discount);
                $itemTotal = $discountedPrice * $item['quantity'];
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                echo "<td>" . htmlspecialchars($item['price']) . "€</td>";
                echo "<td class='quantity-controls'>
                        <button class='minus-btn btn btn-sm btn-secondary' data-article-id='" . htmlspecialchars($item['product_id']) . "'>-</button>
                        <input type='number' class='quantity form-control d-inline' value='" . htmlspecialchars($item['quantity']) . "'>
                        <button class='plus-btn btn btn-sm btn-secondary' data-article-id='" . htmlspecialchars($item['product_id']) . "'>+</button>
                      </td>";
                echo "<td class='article-discount'>" . ($discount * 100) . "%</td>";
                echo "<td class='article-total'>" . htmlspecialchars(number_format($itemTotal, 2)) . "€</td>";
                echo "<td><button class='remove-from-cart btn btn-danger' data-article-id='" . htmlspecialchars($item['product_id']) . "'>&times;</button></td>";
                echo "</tr>";
            }
            ?>
            <?php if ($pointsDiscount > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Punkterabatt:</strong></td>
                    <td colspan="2"><strong><?php echo htmlspecialchars(number_format($pointsDiscount * 0.001, 2)); ?> €</strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Gesamtrabatt:</strong></td>
                <td colspan="2"><strong><?php echo htmlspecialchars(number_format($totalDiscount + ($pointsDiscount * 0.001), 2)); ?> €</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Gesamtpreis:</strong></td>
                <td colspan="2"><strong id="cart-total"><?php echo htmlspecialchars(number_format($totalPrice, 2)); ?> €</strong></td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Punkte verwenden -->
    <form method="post" action="">
        <div class="form-group">
            <input type="checkbox" id="use_points" name="use_points" value="1" <?php if (isset($_POST['use_points']) && $_POST['use_points'] == '1') echo 'checked'; ?>>
            <label for="use_points">Punkte verwenden (Verfügbar: <?php echo $userPoints; ?> Punkte)</label>
        </div>
        <button type="submit" name="apply_points" class="btn btn-primary">Rabatt anwenden</button>
    </form>

    <!-- Bezahl-Formular -->
    <form method="post" action="">
        <button type="submit" name="pay" class="btn btn-primary">Bezahlen</button>
    </form>
</div>
<script src="cart.js"></script>
<?php include "include/footimport.php"; ?>
</body>
</html>
