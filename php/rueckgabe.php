<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

$userId = $_SESSION['users_id'];

$stmt = $link->prepare("SELECT o.id, o.order_date, o.total_amount, p.name, oi.quantity
                        FROM orders o
                        JOIN order_items oi ON o.id = oi.order_id
                        JOIN products p ON oi.product_id = p.id
                        WHERE o.users_id = ? AND o.is_returned = FALSE");
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
    <title>Rückgabe</title>
    <?php include '../php/include/headimport.php'; ?>
    <style>
        .form-control.reason-textarea {
            width: 100%;
            max-width: 250px;
            margin-bottom: 10px;
        }
        .return-form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        @media (max-width: 768px) {
            .table thead {
                display: none;
            }
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            .table tr {
                margin-bottom: 15px;
            }
            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: calc(50% - 20px);
                padding-right: 10px;
                text-align: left;
                font-weight: bold;
            }
            .return-form {
                align-items: center;
            }
        }
    </style>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container mt-5">
    <h1>Ihre Bestellungen</h1>
    <?php if (!empty($orders)): ?>
        <table class="table mt-3">
            <thead>
            <tr>
                <th>Bestellnummer</th>
                <th>Bestelldatum</th>
                <th>Gesamtbetrag</th>
                <th>Produkt</th>
                <th>Menge</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td data-label="Bestellnummer"><?php echo htmlspecialchars($order['id']); ?></td>
                    <td data-label="Bestelldatum"><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td data-label="Gesamtbetrag"><?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?>€</td>
                    <td data-label="Produkt"><?php echo htmlspecialchars($order['name']); ?></td>
                    <td data-label="Menge"><?php echo htmlspecialchars($order['quantity']); ?></td>
                    <td data-label="Aktion">
                        <form method="post" action="process_return.php" class="return-form">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                            <div class="mb-3">
                                <label for="reason" class="form-label" style="color: black">Grund für die Rückgabe</label>
                                <textarea class="form-control reason-textarea" id="reason" name="reason" rows="2" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger">Rückgabe</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Sie haben keine Bestellungen, die Sie rückgeben können.</p>
    <?php endif; ?>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
