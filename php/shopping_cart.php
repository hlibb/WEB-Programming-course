<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

// Funktion zum Hinzufügen von Produkten zum Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Beispiel: Benutzer-ID aus der Session
    $usersId = $_SESSION['users_id'] ?? 1; // Verwenden Sie Ihre Methode zur Ermittlung der Benutzer-ID

    // Berechnung des Rabatts
    $discount = 0;
    if ($quantity >= 10) {
        $discount = 0.20;
    } elseif ($quantity >= 5) {
        $discount = 0.10;
    }

    // Überprüfen, ob das Produkt bereits im Warenkorb ist
    $stmt = $link->prepare("SELECT * FROM shopping_cart WHERE users_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $usersId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        // Aktualisieren Sie den Rabatt entsprechend der neuen Menge
        if ($newQuantity >= 10) {
            $discount = 0.20;
        } elseif ($newQuantity >= 5) {
            $discount = 0.10;
        } else {
            $discount = 0;
        }
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ?, rabatt = ? WHERE users_id = ? AND product_id = ?");
        $stmt->bind_param("idii", $newQuantity, $discount, $usersId, $productId);
    } else {
        $stmt = $link->prepare("INSERT INTO shopping_cart (users_id, product_id, quantity, rabatt) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $usersId, $productId, $quantity, $discount);
    }
    $stmt->execute();
    $stmt->close();
}

// Funktion zum Aktualisieren der Menge eines Artikels im Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $usersId = $_SESSION['users_id'] ?? 1;

    if ($quantity == 0) {
        $stmt = $link->prepare("DELETE FROM shopping_cart WHERE users_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $usersId, $productId);
    } else {
        // Berechnung des Rabatts basierend auf der aktualisierten Menge
        $discount = 0;
        if ($quantity >= 10) {
            $discount = 0.20;
        } elseif ($quantity >= 5) {
            $discount = 0.10;
        }
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ?, rabatt = ? WHERE users_id = ? AND product_id = ?");
        $stmt->bind_param("idii", $quantity, $discount, $usersId, $productId);
    }
    $stmt->execute();
    $stmt->close();
}

// Funktion zum Entfernen von Produkten aus dem Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['remove'])) {
    $productId = $_GET['remove'];

    $usersId = $_SESSION['users_id'] ?? 1;

    $stmt = $link->prepare("DELETE FROM shopping_cart WHERE users_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $usersId, $productId);
    $stmt->execute();
    $stmt->close();
}

// Warenkorb anzeigen
$usersId = $_SESSION['users_id'] ?? 1;

$stmt = $link->prepare("SELECT sc.product_id, p.name, p.price, sc.quantity, sc.rabatt FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.users_id = ?");
$stmt->bind_param("i", $usersId);
$discountedPrice = 0;
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
    $pointsDiscount = min($userPoints, $totalPrice * 10); // max 10 Punkte pro 1€
    $pointsDiscountValue = $pointsDiscount * 0.10;
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
            $totalPrice = 0; // Ensure totalPrice is reset before calculating
            foreach ($cartItems as $item) {
                $discount = $item['rabatt'];
                $discountedPrice = $item['price'] * (1 - $discount);
                $itemTotal = $discountedPrice * $item['quantity'];
                $totalPrice += $itemTotal;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                echo "<td>" . htmlspecialchars($item['price']) . "€</td>";
                echo "<td class='quantity-controls'>
                        <form method='post' action=''>
                            <input type='hidden' name='product_id' value='" . htmlspecialchars($item['product_id']) . "'>
                            <input type='hidden' name='quantity' value='" . ($item['quantity'] - 1) . "'>
                            <button type='submit' name='update_quantity' class='btn btn-sm btn-secondary'>-</button>
                        </form>
                        <span>" . htmlspecialchars($item['quantity']) . "</span>
                        <form method='post' action=''>
                            <input type='hidden' name='product_id' value='" . htmlspecialchars($item['product_id']) . "'>
                            <input type='hidden' name='quantity' value='" . ($item['quantity'] + 1) . "'>
                            <button type='submit' name='update_quantity' class='btn btn-sm btn-secondary'>+</button>
                        </form>
                      </td>";
                echo "<td>" . ($discount * 100) . "%</td>";
                echo "<td>" . htmlspecialchars(number_format($itemTotal, 2)) . "€</td>";
                echo "<td><a href='shopping_cart.php?remove=" . htmlspecialchars($item['product_id']) . "' class='btn btn-danger'>&times;</a></td>";
                echo "</tr>";
            }
            ?>
            <?php if ($pointsDiscount > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Punkterabatt:</strong></td>
                    <td colspan="2"><strong><?php echo htmlspecialchars(number_format($pointsDiscount * 0.10, 2)); ?> €</strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Gesamtrabatt:</strong></td>
                <td colspan="2"><strong><?php echo htmlspecialchars(number_format($totalDiscount + ($pointsDiscount * 0.10), 2)); ?> €</strong></td>
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
    <form method="post" action="">
        <button type="submit" name="pay" class="btn btn-primary">Bezahlen</button>
    </form>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
