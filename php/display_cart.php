<?php
include_once 'include/db_connection.php';

function display_cart($link) {
    $usersId = $_SESSION['users_id'] ?? 1;

    // Warenkorb anzeigen
    $stmt = $link->prepare("SELECT sc.product_id, p.name, p.price, sc.quantity, sc.rabatt FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.users_id = ?");
    $stmt->bind_param("i", $usersId);
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
        $totalDiscount += ($row['price'] * $row['quantity']) * $row['rabatt']; // Gesamtrabatt berechnen
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
    if (isset($_POST['use_points']) && $_POST['use_points'] == '1') {
        $pointsDiscount = min($userPoints, $totalPrice * 100); // max 100 Punkte pro 1€
        $pointsDiscountValue = $pointsDiscount * 0.001;
        $totalPrice -= $pointsDiscountValue;
    }

    echo '<div class="table-container">';
    echo '<table class="table table-bordered mt-3">';
    echo '<thead><tr><th>Produkt</th><th>Preis</th><th>Menge</th><th>Rabatt</th><th>Gesamt</th><th>Aktion</th></tr></thead>';
    echo '<tbody>';

    foreach ($cartItems as $item) {
        $discount = $item['rabatt'];
        $discountedPrice = $item['price'] * (1 - $discount);
        $itemTotal = $discountedPrice * $item['quantity'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td>" . htmlspecialchars($item['price']) . "€</td>";
        echo "<td class='quantity-controls'>
                <button type='button' class='btn btn-sm btn-secondary minus-btn' data-article-id='" . htmlspecialchars($item['product_id']) . "'>-</button>
                <span id='quantity-display-" . htmlspecialchars($item['product_id']) . "'>" . htmlspecialchars($item['quantity']) . "</span>
                <button type='button' class='btn btn-sm btn-secondary plus-btn' data-article-id='" . htmlspecialchars($item['product_id']) . "'>+</button>
              </td>";
        echo "<td class='article-discount'>" . ($discount * 100) . "%</td>";
        echo "<td class='article-total'>" . htmlspecialchars(number_format($itemTotal, 2)) . "€</td>";
        echo "<td><button class='btn btn-danger remove-from-cart' data-article-id='" . htmlspecialchars($item['product_id']) . "'>&times;</button></td>";
        echo "</tr>";
    }
    if ($pointsDiscount > 0) {
        echo '<tr>';
        echo '<td colspan="4" class="text-right"><strong>Punkterabatt:</strong></td>';
        echo '<td colspan="2"><strong>' . htmlspecialchars(number_format($pointsDiscount * 0.001, 2)) . ' €</strong></td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td colspan="4" class="text-right"><strong>Gesamtrabatt:</strong></td>';
    echo '<td colspan="2"><strong>' . htmlspecialchars(number_format($totalDiscount + ($pointsDiscount * 0.001), 2)) . ' €</strong></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td colspan="4" class="text-right"><strong>Gesamtpreis:</strong></td>';
    echo '<td colspan="2"><strong>' . htmlspecialchars(number_format($totalPrice, 2)) . ' €</strong></td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    echo '<form method="post" id="use-points-form">';
    echo '<div class="form-group">';
    echo '<input type="checkbox" id="use_points" name="use_points" value="1" ' . (isset($_POST['use_points']) && $_POST['use_points'] == '1' ? 'checked' : '') . '>';
    echo '<label for="use_points">Punkte verwenden (Verfügbar: ' . $userPoints . ' Punkte)</label>';
    echo '</div>';
    echo '<button type="button" id="apply-points" class="btn btn-primary">Rabatt anwenden</button>';
    echo '</form>';

    echo '<form method="post" action="">';
    echo '<button type="submit" name="pay" class="btn btn-primary">Bezahlen</button>';
    echo '</form>';
}
display_cart($link);
?>
