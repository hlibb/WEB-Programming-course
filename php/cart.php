<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';

function calculateDiscount($price, $quantity) {
    if ($quantity >= 10) {
        $discountRate = 0.20;
    } elseif ($quantity >= 5) {
        $discountRate = 0.10;
    } else {
        $discountRate = 0.00;
    }

    $discountAmount = $price * $quantity * $discountRate;
    return [$discountAmount, $discountRate];
}

$userId = $_SESSION['users_id'];

$stmt = $link->prepare("SELECT p.id, p.name, p.price, cb.quantity, (p.price * cb.quantity) AS product_total 
                        FROM `cart-body` cb
                        JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                        JOIN `products` p ON cb.product_id = p.id
                        WHERE ch.users_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;
$totalDiscount = 0;
while ($row = $result->fetch_assoc()) {
    $productId = $row['id'];
    $price = $row['price'];
    $quantity = $row['quantity'];
    $productTotal = $row['product_total'];

    list($discountAmount, $discountRate) = calculateDiscount($price, $quantity);
    $discountDisplay = number_format($discountAmount, 2) . '€ (' . ($discountRate * 100) . '%)';
    $productTotalAfterDiscount = $productTotal - $discountAmount;

    $cartItems[] = [
        'id' => $productId,
        'name' => $row['name'],
        'price' => $price,
        'quantity' => $quantity,
        'product_total' => $productTotalAfterDiscount,
        'discount' => $discountDisplay
    ];

    $totalPrice += $productTotalAfterDiscount;
    $totalDiscount += $discountAmount;
}

// Get user points
$pointsStmt = $link->prepare("SELECT points FROM points WHERE users_id = ?");
$pointsStmt->bind_param("i", $userId);
$pointsStmt->execute();
$pointsResult = $pointsStmt->get_result();
$userPoints = $pointsResult->fetch_assoc()['points'];
$pointsStmt->close();

$pointsValue = $userPoints / 1000;

$stmt->close();
$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Warenkorb</title>
    <?php include '../php/include/headimport.php' ?>
    <style>
        .quantity-wrapper {
            display: flex;
            align-items: center;
        }
        .quantity-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .quantity-display {
            width: 40px;
            text-align: center;
            margin: 0 5px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .remove-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .total-price-row {
            font-weight: bold;
            text-align: right;
        }
        .points-row {
            display: flex;
            align-items: center;
        }
        .form-check-input {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Ihr Warenkorb</h1>
    <?php if (count($cartItems) > 0): ?>
        <table class="table mt-3">
            <thead>
            <tr>
                <th>Produktname</th>
                <th>Preis</th>
                <th>Menge</th>
                <th>Rabatt</th>
                <th>Gesamtpreis</th>
                <th>Aktionen</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr data-product-id="<?php echo htmlspecialchars($item['id']); ?>">
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?>€</td>
                    <td>
                        <div class="quantity-wrapper">
                            <button class="quantity-button decrease" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">-</button>
                            <span class="quantity-display" data-product-id="<?php echo htmlspecialchars($item['id']); ?>"><?php echo htmlspecialchars($item['quantity']); ?></span>
                            <button class="quantity-button increase" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">+</button>
                        </div>
                    </td>
                    <td class="product-discount" data-product-id="<?php echo htmlspecialchars($item['id']); ?>"><?php echo $item['discount']; ?></td>
                    <td class="product-total" data-product-id="<?php echo htmlspecialchars($item['id']); ?>"><?php echo number_format($item['product_total'], 2); ?>€</td>
                    <td>
                        <button class="remove-button" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">X</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><input class="form-check-input ms-2" type="checkbox" id="use-points-checkbox"></td>
                <td>
                    <div class="points-row">
                        Punkte verwenden:
                    </div>
                </td>
                <td></td>
                <td class="total-price-row">Gesamtrabatt:</td>
                <td class="total-price-row" id="total-discount"><?php echo number_format($totalDiscount, 2); ?>€</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" class="total-price-row">Punktewert:</td>
                <td class="total-price-row" id="points-value"><?php echo number_format($pointsValue, 2); ?>€</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" class="total-price-row">Gesamtpreis:</td>
                <td class="total-price-row" id="total-price"><?php echo number_format($totalPrice, 2); ?>€</td>
                <td></td>
            </tr>
            </tbody>
        </table>
        <tr>
            <td colspan="4"></td>
            <td>
                <a href="checkout.php" class="btn btn-primary">Zur Kasse</a>
            </td>
            <td></td>
        </tr>

    <?php else: ?>
        <p>Ihr Warenkorb ist leer.</p>
    <?php endif; ?>
</div>
<?php include "include/footimport.php"; ?>
<script src="cart.js"></script>
</body>
</html>
