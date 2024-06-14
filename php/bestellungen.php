<?php
include_once 'include/logged_in.php'; // Ensure this is included at the top
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

$usersId = $_SESSION['users_id'] ?? 1;

// Funktion zum Abrufen der Bestellartikel
function getOrderItems($orderId, $link) {
    $stmt = $link->prepare("SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderItems = [];
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
    }
    $stmt->close();
    return $orderItems;
}

// Bestellung erneut tätigen, wenn der Button gedrückt wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reorder'])) {
    $orderId = $_POST['order_id'];

    // Abrufen der Originalbestellung
    $orderItems = getOrderItems($orderId, $link);

    // Neue Bestellung in der Datenbank speichern
    $stmt = $link->prepare("INSERT INTO orders (users_id, total_amount, shipping_method, is_express_shipping, is_paid) SELECT users_id, total_amount, shipping_method, is_express_shipping, is_paid FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $newOrderId = $stmt->insert_id;
    $stmt->close();

    // Bestellpositionen kopieren
    foreach ($orderItems as $item) {
        $stmt = $link->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $newOrderId, $item['product_id'], $item['quantity'], $item['unit_price']);
        $stmt->execute();
    }
    $stmt->close();

    // Benutzerinformationen aus der Datenbank abrufen
    $stmt = $link->prepare("SELECT email, name FROM users WHERE id = ?");
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $user = $userResult->fetch_assoc();
    $stmt->close();

    $recipientEmail = $user['email'];
    $recipientName = $user['name'];

    // Abrufen der neuen Bestelldaten
    $stmt = $link->prepare("SELECT total_amount, shipping_method, is_express_shipping FROM orders WHERE id = ?");
    $stmt->bind_param("i", $newOrderId);
    $stmt->execute();
    $newOrderResult = $stmt->get_result();
    $newOrder = $newOrderResult->fetch_assoc();
    $stmt->close();

    $shippingCost = $newOrder['is_express_shipping'] ? 10.00 : 5.00; // Beispiel für Versandkosten
    $totalPrice = $newOrder['total_amount'];
    $totalDiscount = $_SESSION['total_discount'] ?? 0;

    // E-Mail-Vorlage abrufen
    $emailTemplate = getPaymentConfirmationEmail1($recipientName, $orderItems, $totalPrice, $newOrder['shipping_method'], $shippingCost, $totalDiscount);

    // E-Mail senden
    sendEmail($recipientEmail, $recipientName, $emailTemplate);

    // Weiterleitung zur Bestätigungsseite oder Aktualisierung der aktuellen Seite
    header("Location: bestellungen.php?success=1");
    exit();
}

// Bestellungen des Benutzers abrufen
$stmt = $link->prepare("SELECT * FROM orders WHERE users_id = ?");
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    // Abrufen der Artikel für jede Bestellung
    $order_id = $row['id'];
    $items = getOrderItems($order_id, $link);
    $row['items'] = $items;
    $orders[] = $row;
}

$stmt->close();
$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellungen</title>
    <?php include '../php/include/headimport.php'; ?>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Ihre Bestellungen</h1>
    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "<div class='alert alert-success'>Bestellung wurde erfolgreich erneut getätigt.</div>";
    }
    ?>
    <table class="table table-bordered mt-3">
        <thead>
        <tr>
            <th>Bestellnummer</th>
            <th>Bestelldatum</th>
            <th>Gesamtbetrag</th>
            <th>Versandmethode</th>
            <th>Expressversand</th>
            <th>Bezahlt</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>
                    <button class="btn btn-link" data-toggle="collapse" data-target="#order-<?php echo $order['id']; ?>" aria-expanded="false" aria-controls="order-<?php echo $order['id']; ?>">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <?php echo htmlspecialchars($order['id']); ?>
                </td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                <td><?php echo htmlspecialchars($order['total_amount']); ?>€</td>
                <td><?php echo htmlspecialchars($order['shipping_method']); ?></td>
                <td><?php echo $order['is_express_shipping'] ? 'Ja' : 'Nein'; ?></td>
                <td><?php echo $order['is_paid'] ? 'Ja' : 'Nein'; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                        <button type="submit" name="reorder" class="btn btn-primary">Erneut Bestellen</button>
                    </form>
                </td>
            </tr>
            <tr class="collapse" id="order-<?php echo $order['id']; ?>">
                <td colspan="7">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Menge</th>
                            <th>Preis</th>
                            <th>Gesamt</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['unit_price']); ?>€</td>
                                <td><?php echo number_format($itemTotal, 2); ?>€</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
