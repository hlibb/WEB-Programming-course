<?php
include_once 'include/logged_in.php'; // Ensure this is included at the top
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

$kundenId = $_SESSION['kunden_id'] ?? 1;

// Bestellung erneut tätigen, wenn der Button gedrückt wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reorder'])) {
    $orderId = $_POST['order_id'];

    // Abrufen der Originalbestellung
    $stmt = $link->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $orderItemsResult = $stmt->get_result();

    $orderItems = [];
    while ($row = $orderItemsResult->fetch_assoc()) {
        $orderItems[] = $row;
    }
    $stmt->close();

    // Neue Bestellung in der Datenbank speichern
    $stmt = $link->prepare("INSERT INTO orders (kunden_id, total_amount, shipping_method, is_express_shipping, is_paid) SELECT kunden_id, total_amount, shipping_method, is_express_shipping, is_paid FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $newOrderId = $stmt->insert_id;
    $stmt->close();

    // Bestellpositionen kopieren
    foreach ($orderItems as $item) {
        $stmt = $link->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $newOrderId, $item['product_id'], $item['quantity'], $item['unit_price']);
        $stmt->execute();
        $stmt->close();
    }

    // Benutzerinformationen aus der Datenbank abrufen
    $stmt = $link->prepare("SELECT email, name FROM kunden WHERE id = ?");
    $stmt->bind_param("i", $kundenId);
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

    // E-Mail-Vorlage abrufen
    $emailTemplate = getPaymentConfirmationEmail($recipientName, $orderItems, $totalPrice, $newOrder['shipping_method'], $shippingCost);

    // E-Mail senden
    sendEmail($recipientEmail, $recipientName, $emailTemplate);

    // Weiterleitung zur Bestätigungsseite oder Aktualisierung der aktuellen Seite
    header("Location: bestellungen.php?success=1");
    exit();
}

// Bestellungen des Benutzers abrufen
$stmt = $link->prepare("SELECT * FROM orders WHERE kunden_id = ?");
$stmt->bind_param("i", $kundenId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    // Abrufen der Artikel für jede Bestellung
    $order_id = $row['id'];
    $item_stmt = $link->prepare("SELECT oi.*, p.name AS product_name, sc.rabatt AS rabatt FROM order_items oi JOIN products p ON oi.product_id = p.id LEFT JOIN shopping_cart sc ON sc.product_id = oi.product_id WHERE oi.order_id = ?");
    $item_stmt->bind_param("i", $order_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    $items = [];
    while ($item_row = $item_result->fetch_assoc()) {
        $items[] = $item_row;
    }
    $row['items'] = $items;
    $orders[] = $row;
    $item_stmt->close();
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
                            <th>Rabatt</th>
                            <th>Gesamt</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['unit_price']); ?>€</td>
                                <td><?php echo isset($item['rabatt']) ? htmlspecialchars($item['rabatt']) . '%' : '0%'; ?></td>
                                <?php
                                $rabatt = isset($item['rabatt']) ? $item['rabatt'] : 0;
                                $discountedPrice = $item['unit_price'] * (1 - $rabatt / 100);
                                $itemTotal = $discountedPrice * $item['quantity'];
                                ?>
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
