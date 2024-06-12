<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';
include 'send_email.php';

$usersId = $_SESSION['users_id']; // Benutzer-ID aus der Sitzung

// Warenkorb-Kopf-ID abrufen
$stmt = $link->prepare("SELECT id FROM `cart-header` WHERE users_id = ?");
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $cartId = $row['id'];

    // Warenkorb-Artikel abrufen
    $stmt = $link->prepare("SELECT cb.product_id, p.name, p.price, cb.quantity, cb.rabatt FROM `cart-body` cb JOIN products p ON cb.product_id = p.id WHERE cb.warenkorb_id = ?");
    $stmt->bind_param("i", $cartId);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartItems = [];
    $totalPrice = 0;
    $totalDiscount = 0; // Gesamtrabatt initialisieren
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $discountedPrice = $row['price'] * (1 - $row['rabatt']);
        $itemTotal = $discountedPrice * $row['quantity'];
        $totalPrice += $itemTotal;
        $totalDiscount += ($row['price'] * $row['quantity']) * $row['rabatt'];
    }
    $stmt->close();

    // Benutzerpunkte abrufen
    $stmt = $link->prepare("SELECT points FROM punkte WHERE users_id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userPoints = 0;
    if ($row = $result->fetch_assoc()) {
        $userPoints = $row['points'];
    }
    $stmt->close();

    // Punkte-Rabatt berechnen
    $pointsDiscount = 0;
    $pointsDiscountValue = 0;
    if (isset($_POST['use_points']) && $_POST['use_points'] == '1') {
        $pointsDiscount = min($userPoints, $totalPrice * 10);
        $pointsDiscountValue = $pointsDiscount * 0.10;
        $totalPrice -= $pointsDiscountValue;
    }

} else {
    $cartItems = [];
    $totalPrice = 0;
    $totalDiscount = 0;
    $userPoints = 0;
    $pointsDiscount = 0;
    $pointsDiscountValue = 0;
}

$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Warenkorb</title>
    <?php include '../php/include/headimport.php' ?>
    <link rel="stylesheet" href="styles.css">
    <style>
        .quantity-controls {
            display: flex;
            align-items: center;
        }

        .quantity-controls form {
            margin: 0 5px;
        }
    </style>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Ihr Warenkorb</h1>
    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "<div class='alert alert-success'>Bezahlung erfolgreich! Eine Bestätigungs-E-Mail wurde gesendet.</div>";
    }
    ?>
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
                        <button type='button' class='btn btn-sm btn-secondary minus-btn' data-article-id='" . htmlspecialchars($item['product_id']) . "'>-</button>
                        <span>" . htmlspecialchars($item['quantity']) . "</span>
                        <button type='button' class='btn btn-sm btn-secondary plus-btn' data-article-id='" . htmlspecialchars($item['product_id']) . "'>+</button>
                      </td>";
                echo "<td class='article-discount'>" . ($discount * 100) . "%</td>";
                echo "<td class='article-total'>" . htmlspecialchars(number_format($itemTotal, 2)) . "€</td>";
                echo "<td><button type='button' class='btn btn-danger remove-from-cart' data-article-id='" . htmlspecialchars($item['product_id']) . "'>&times;</button></td>";
                echo "</tr>";
            }
            if ($pointsDiscount > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Punkterabatt:</strong></td>
                    <td colspan="2"><strong><?php echo htmlspecialchars(number_format($pointsDiscountValue, 2)); ?> €</strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Gesamtrabatt:</strong></td>
                <td colspan="2"><strong><?php echo htmlspecialchars(number_format($totalDiscount + $pointsDiscountValue, 2)); ?> €</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Gesamtpreis:</strong></td>
                <td colspan="2"><strong><?php echo htmlspecialchars(number_format($totalPrice, 2)); ?> €</strong></td>
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
    <form method="post" action="checkout.php">
        <button type="submit" name="pay" class="btn btn-primary">Bezahlen</button>
    </form>
</div>
<?php include "include/footimport.php"; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery einbinden -->
<script src="cart.js"></script>
</body>
</html>
