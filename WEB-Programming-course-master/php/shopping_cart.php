<?php
session_start();
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

// Funktion zum Hinzufügen von Produkten zum Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Beispiel: Benutzer-ID aus der Session
    $userId = $_SESSION['user_id'] ?? 1; // Verwenden Sie Ihre Methode zur Ermittlung der Benutzer-ID
    
    // Überprüfen, ob das Produkt bereits im Warenkorb ist
    $stmt = $link->prepare("SELECT * FROM shopping_cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        $stmt = $link->prepare("UPDATE shopping_cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $newQuantity, $userId, $productId);
    } else {
        $stmt = $link->prepare("INSERT INTO shopping_cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $productId, $quantity);
    }
    $stmt->execute();
    $stmt->close();
}

// Funktion zum Entfernen von Produkten aus dem Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    
    $userId = $_SESSION['user_id'] ?? 1;
    
    $stmt = $link->prepare("DELETE FROM shopping_cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $stmt->close();
}

// Warenkorb anzeigen
$userId = $_SESSION['user_id'] ?? 1;

$stmt = $link->prepare("SELECT sc.id, p.name, p.price, sc.quantity FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.user_id = ?");
$stmt->bind_param("i", $userId);
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
    $stmt = $link->prepare("SELECT email, name FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
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
    $stmt = $link->prepare("DELETE FROM shopping_cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
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
    <?php include "include/headimport.php"; ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
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
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Produkt</th>
                <th>Preis</th>
                <th>Menge</th>
                <th>Gesamt</th>
                <th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $totalPrice += $itemTotal;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                echo "<td>" . htmlspecialchars($item['price']) . "€</td>";
                echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                echo "<td>" . htmlspecialchars($itemTotal) . "€</td>";
                echo "<td><a href='shopping_cart.php?remove=" . htmlspecialchars($item['id']) . "' class='btn btn-danger'>Entfernen</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <h3>Gesamtpreis: <?php echo htmlspecialchars($totalPrice); ?>€</h3>
    
    <!-- Bezahl-Formular -->
    <form method="get" action="checkout.php">
        <button type="submit" class="btn btn-primary">Bezahlen</button>
    </form>

</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
