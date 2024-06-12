<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

function getUserPoints($userId, $link) {
    $stmt = $link->prepare("SELECT points FROM punkte WHERE users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $points = 0;
    if ($row = $result->fetch_assoc()) {
        $points = $row['points'];
    }
    $stmt->close();
    return $points;
}

function getCartItems($userId, $link) {
    $cartItems = [];
    $stmt = $link->prepare("
        SELECT cb.id AS cart_item_id, p.id AS product_id, p.name, p.price, cb.quantity, cb.rabatt 
        FROM `cart-body` cb 
        JOIN products p ON cb.product_id = p.id 
        JOIN `cart-header` ch ON cb.warenkorb_id = ch.id 
        WHERE ch.users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    $stmt->close();
    return $cartItems;
}

$userId = $_SESSION['user_id'] ?? 1;
$userPoints = getUserPoints($userId, $link);
$cartItems = getCartItems($userId, $link);
$totalPrice = array_reduce($cartItems, function($carry, $item) {
    $discountedPrice = $item['price'] * (1 - $item['rabatt']);
    return $carry + ($discountedPrice * $item['quantity']);
}, 0);

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
            <tbody id="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <tr data-cart-item-id="<?= htmlspecialchars($item['cart_item_id']) ?>">
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['price']) ?>€</td>
                    <td class='quantity-controls'>
                        <button class="btn btn-sm btn-secondary minus-btn" data-article-id="<?= htmlspecialchars($item['cart_item_id']) ?>">-</button>
                        <span class="quantity"><?= htmlspecialchars($item['quantity']) ?></span>
                        <button class="btn btn-sm btn-secondary plus-btn" data-article-id="<?= htmlspecialchars($item['cart_item_id']) ?>">+</button>
                    </td>
                    <td><?= ($item['rabatt'] * 100) ?>%</td>
                    <td class="article-total"><?= number_format($item['price'] * (1 - $item['rabatt']) * $item['quantity'], 2) ?>€</td>
                    <td><button class='btn btn-danger remove-from-cart' data-article-id="<?= htmlspecialchars($item['cart_item_id']) ?>">&times;</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-right"><strong>Gesamtpreis: </strong><span id="cart-total"><?= number_format($totalPrice, 2) ?>€</span></div>
    </div>

    <!-- Punkte verwenden -->
    <form method="post" action="apply_points.php">
        <div class="form-group">
            <input type="checkbox" id="use_points" name="use_points" value="1">
            <label for="use_points">Punkte verwenden (Verfügbar: <?= $userPoints ?> Punkte)</label>
        </div>
    </form>

    <!-- Bezahl-Formular -->
    <form method="post" action="checkout.php">
        <button type="submit" name="pay" class="btn btn-primary">Bezahlen</button>
    </form>
</div>
<?php include "include/footimport.php"; ?>
<script src="cart.js"></script>
</body>
</html>
