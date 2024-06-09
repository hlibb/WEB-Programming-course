<?php
session_start();
include_once 'include/db_connection.php';
include 'send_email.php';

// Funktion zum Hinzufügen von Produkten zum Warenkorb
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    $userId = $_SESSION['user_id'] ?? 1;
    
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

// E-Mail senden, Bestellung speichern und Warenkorb leeren, wenn das Checkout-Formular abgeschickt wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $paymentMethod = $_POST['paymentMethod'];
    $ccName = $_POST['cc_name'];
    $ccNumber = $_POST['cc_number'];
    $ccExpiration = $_POST['cc_expiration'];
    $ccCVV = $_POST['cc_cvv'];
    $shippingMethod = $_POST['shipping_method'];
    $isExpressShipping = isset($_POST['is_express_shipping']) ? 1 : 0;
    $totalAmount = $_POST['total_amount'];

    // Bestellung in der Datenbank speichern
    $stmt = $link->prepare("INSERT INTO orders (kunden_id, total_amount, shipping_method, is_express_shipping, is_paid) VALUES (?, ?, ?, ?, ?)");
    $isPaid = 1; // Annahme: Zahlung erfolgreich
    $stmt->bind_param("idssi", $userId, $totalAmount, $shippingMethod, $isExpressShipping, $isPaid);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Bestellpositionen speichern
    foreach ($cartItems as $item) {
        $stmt = $link->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

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

    // Weiterleitung nach dem Leeren des Warenkorbs und dem Senden der E-Mail
    header("Location: shopping_cart.php?success=1");
    exit();
}

$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
    <?php include "include/headimport.php"; ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2>Billing address</h2>
            <div class="form-container">
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">First name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Last name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="username">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">@</span>
                            </div>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email <span class="text-muted">(Optional)</span></label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>

                    <div class="mb-3">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>

                    <div class="mb-3">
                        <label for="address2">Address 2 <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" id="address2" name="address2">
                    </div>

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="country">Country</label>
                            <select class="custom-select d-block w-100" id="country" name="country" required>
                                <option value="">Choose...</option>
                                <option>United States</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state">State</label>
                            <select class="custom-select d-block w-100" id="state" name="state" required>
                                <option value="">Choose...</option>
                                <option>California</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="zip">Zip</label>
                            <input type="text" class="form-control" id="zip" name="zip" required>
                        </div>
                    </div>

                    <hr class="mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="same-address" name="same_address">
                        <label class="custom-control-label" for="same-address">Shipping address is the same as my billing address</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="save-info" name="save_info">
                        <label class="custom-control-label" for="save-info">Save this information for next time</label>
                    </div>

                    <hr class="mb-4">

                    <h4 class="mb-3">Payment</h4>

                    <div class="d-block my-3">
                        <div class="custom-control custom-radio">
                            <input id="credit" name="paymentMethod" type="radio" class="custom-control-input" checked required>
                            <label class="custom-control-label" for="credit">Credit card</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="debit" name="paymentMethod" type="radio" class="custom-control-input" required>
                            <label class="custom-control-label" for="debit">Debit card</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="paypal" name="paymentMethod" type="radio" class="custom-control-input" required>
                            <label class="custom-control-label" for="paypal">PayPal</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cc-name">Name on card</label>
                            <input type="text" class="form-control" id="cc-name" name="cc_name" required>
                            <small class="text-muted">Full name as displayed on card</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cc-number">Credit card number</label>
                            <input type="text" class="form-control" id="cc-number" name="cc_number" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cc-expiration">Expiration</label>
                            <input type="text" class="form-control" id="cc-expiration" name="cc_expiration" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cc-cvv">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" name="cc_cvv" required>
                        </div>
                    </div>

                    <hr class="mb-4">
                    <input type="hidden" name="checkout" value="1">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout</button>
                </form>
            </div>
        </div>
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Your cart</span>
                <span class="badge badge-primary badge-pill"><?php echo count($cartItems); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php
                $totalPrice = 0;
                foreach ($cartItems as $item) {
                    $itemTotal = $item['price'] * $item['quantity'];
                    $totalPrice += $itemTotal;
                    echo '<li class="list-group-item d-flex justify-content-between lh-condensed">';
                    echo '<div>';
                    echo '<h6 class="my-0">' . htmlspecialchars($item['name']) . '</h6>';
                    echo '<small class="text-muted">Brief description</small>';
                    echo '</div>';
                    echo '<span class="text-muted">' . htmlspecialchars($item['price']) . '€</span>';
                    echo '</li>';
                }
                ?>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <div class="text-success">
                        <h6 class="my-0">Promo code</h6>
                        <small>EXAMPLECODE</small>
                    </div>
                    <span class="text-success">−$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (USD)</span>
                    <strong><?php echo htmlspecialchars($totalPrice); ?>€</strong>
                </li>
            </ul>

            <form class="card p-2 promo-code-group">
                <input type="text" class="form-control promo-code" placeholder="Promo code">
                <button type="submit" class="btn btn-secondary">Redeem</button>
            </form>
        </div>
    </div>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
