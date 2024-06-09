<?php
session_start();
include_once 'include/db_connection.php';

$userId = $_SESSION['user_id'] ?? 1;

// Bestellungen des Benutzers abrufen
$stmt = $link->prepare("SELECT * FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
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
    <?php include "include/headimport.php"; ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Ihre Bestellungen</h1>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Bestellnummer</th>
                <th>Bestelldatum</th>
                <th>Gesamtbetrag</th>
                <th>Versandmethode</th>
                <th>Expressversand</th>
                <th>Bezahlt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($order['total_amount']); ?>€</td>
                    <td><?php echo htmlspecialchars($order['shipping_method']); ?></td>
                    <td><?php echo $order['is_express_shipping'] ? 'Ja' : 'Nein'; ?></td>
                    <td><?php echo $order['is_paid'] ? 'Ja' : 'Nein'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
