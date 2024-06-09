<?php
session_start();
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

// Funktion zum Hinzufügen von Produkten zum Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Beispiel: Benutzer-ID aus der Session
    $kundenId = $_SESSION['kunden_id'] ?? 1; // Verwenden Sie Ihre Methode zur Ermittlung der Benutzer-ID

    // Überprüfen, ob das Produkt bereits im Warenkorb ist
    $stmt = $link->prepare("SELECT * FROM shopping_cart WHERE kunden_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $kundenId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ? WHERE kunden_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $newQuantity, $kundenId, $productId);
    } else {
        $stmt = $link->prepare("INSERT INTO shopping_cart (kunden_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $kundenId, $productId, $quantity);
    }
    $stmt->execute();
    $stmt->close();
}

// Funktion zum Aktualisieren der Menge eines Artikels im Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $kundenId = $_SESSION['kunden_id'] ?? 1;

    if ($quantity == 0) {
        $stmt = $link->prepare("DELETE FROM shopping_cart WHERE kunden_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $kundenId, $productId);
    } else {
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ? WHERE kunden_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $kundenId, $productId);
    }
    $stmt->execute();
    $stmt->close();
}

// Funktion zum Entfernen von Produkten aus dem Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['remove'])) {
    $productId = $_GET['remove'];

    $kundenId = $_SESSION['kunden_id'] ?? 1;

    $stmt = $link->prepare("DELETE FROM shopping_cart WHERE kunden_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $kundenId, $productId);
    $stmt->execute();
    $stmt->close();
}

// Warenkorb anzeigen
$kundenId = $_SESSION['kunden_id'] ?? 1;

$stmt = $link->prepare("SELECT sc.product_id, p.name, p.price, sc.quantity FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.kunden_id = ?");
$stmt->bind_param("i", $kundenId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

$stmt->close();

// E-Mail senden und Warenkorb leeren, wenn der Bezahlen-Knopf gedrückt wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    // Benutzerinformationen aus der Datenbank abrufen
    $stmt = $link->prepare("SELECT email, name FROM kunden WHERE id = ?");
    $stmt->bind_param("i", $kundenId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $user = $userResult->fetch_assoc();

    $recipientEmail = $user['email'];
    $recipientName = $user['name'];

    // Holen Sie sich die Bestätigungs-E-Mail-Vorlage
    $emailTemplate = getPaymentConfirmationEmail($recipientName);

    // Senden Sie die E-Mail
    sendEmail($recipientEmail, $recipientName, $emailTemplate);

    $stmt->close();

    // Warenkorb leeren
    $stmt = $link->prepare("DELETE FROM shopping_cart WHERE kunden_id = ?");
    $stmt->bind_param("i", $kundenId);
    $stmt->execute();
    $stmt->close();

    // Optionale Weiterleitung nach dem Leeren des Warenkorbs und dem Senden der E-Mail
    header("Location: shopping_cart.php?success=1");
    exit();
}

$link->close(); // Schließe die Verbindung am Ende des Skripts
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Warenkorb</title>
    <?php include '../php/include/headimport.php' ?>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
    <style>
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-controls form {
            display: inline;
        }
        .quantity-controls span {
            margin: 0 5px;
            min-width: 24px;
            text-align: center;
            display: inline-block;
        }
        .table-container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
        }
        .table {
            width: 100%;
            table-layout: fixed;
        }
        @media (max-width: 768px) {
            .table-container {
                width: 100%;
                padding: 0 10px;
            }
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
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $discount = 0;
                if ($item['quantity'] >= 10) {
                    $discount = 0.20;
                } elseif ($item['quantity'] >= 5) {
                    $discount = 0.10;
                }
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
            </tbody>
        </table>
    </div>
    <h3>Gesamtpreis: <?php echo htmlspecialchars(number_format($totalPrice, 2)); ?>€</h3>

    <!-- Bezahl-Formular -->
    <form method="post" action="">
        <button type="submit" name="pay" class="btn btn-primary">Bezahlen</button>
    </form>

</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
