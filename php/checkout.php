<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';
include 'send_email.php'; // Include the send email function

// Versandkosten initialisieren
$shippingCost = 0;
$shippingMethod = '';
$isExpressShipping = 0;

// Versandkosten berechnen und in der Session speichern
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['shipping_method'])) {
    $shippingMethod = $_POST['shipping_method'];
    if ($shippingMethod == 'DHL') {
        $shippingCost = 4.5;
    } elseif ($shippingMethod == 'DHL Express') {
        $shippingCost = 4.5 + 6;
    } elseif ($shippingMethod == 'LPD') {
        $shippingCost = 4.5 + 3;
    }
    $_SESSION['shipping_cost'] = $shippingCost;
    $_SESSION['shipping_method'] = $shippingMethod;
}

// Warenkorb anzeigen
$kundenId = $_SESSION['kunden_id'] ?? 1;

$stmt = $link->prepare("SELECT sc.product_id, p.name, p.price, sc.quantity, sc.rabatt FROM shopping_cart sc JOIN products p ON sc.product_id = p.id WHERE sc.kunden_id = ?");
$stmt->bind_param("i", $kundenId);
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

// Versandkosten und Versandart aus der Session holen
if (isset($_SESSION['shipping_cost'])) {
    $shippingCost = $_SESSION['shipping_cost'];
}
if (isset($_SESSION['shipping_method'])) {
    $shippingMethod = $_SESSION['shipping_method'];
}

// E-Mail senden, Bestellung speichern und Warenkorb leeren, wenn das Checkout-Formular abgeschickt wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    if (!isset($_POST['privacy_policy'])) {
        echo "<div class='alert alert-danger'>Sie müssen die Datenschutzrichtlinie akzeptieren.</div>";
    } else {
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
        $isExpressShipping = $shippingMethod === 'DHL Express' ? 1 : 0;

        // Versandkosten berechnen
        if ($shippingMethod == 'DHL') {
            $shippingCost = 4.5;
        } elseif ($shippingMethod == 'DHL Express') {
            $shippingCost = 4.5 + 6;
        } elseif ($shippingMethod == 'LPD') {
            $shippingCost = 4.5 + 3;
        }
        $totalPriceWithShipping = $totalPrice + $shippingCost;

        // Bestellung in der Datenbank speichern
        $stmt = $link->prepare("INSERT INTO orders (kunden_id, total_amount, shipping_method, is_express_shipping, is_paid) VALUES (?, ?, ?, ?, ?)");
        $isPaid = 1; // Annahme: Zahlung erfolgreich
        $stmt->bind_param("idssi", $kundenId, $totalPriceWithShipping, $shippingMethod, $isExpressShipping, $isPaid);
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

        // Log the purchase event with total price
        $log_sql = "INSERT INTO logs (kunden_id, event_type, event_details) VALUES (?, 'purchase', ?)";
        $event_details = "User made a purchase. Total price: " . number_format($totalPriceWithShipping, 2) . "€";
        if ($log_stmt = $link->prepare($log_sql)) {
            $log_stmt->bind_param("is", $kundenId, $event_details);
            $log_stmt->execute();
            $log_stmt->close();
        }

        // Benutzerinformationen aus der Datenbank abrufen
        $stmt = $link->prepare("SELECT email, name FROM kunden WHERE id = ?");
        $stmt->bind_param("i", $kundenId);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $user = $userResult->fetch_assoc();

        $recipientEmail = $user['email'];
        $recipientName = $user['name'];

        // Holen Sie sich die Bestätigungs-E-Mail-Vorlage
        $emailTemplate = getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost);

        // Senden Sie die E-Mail
        sendEmail($recipientEmail, $recipientName, $emailTemplate);

        $stmt->close();

        // Warenkorb leeren
        $stmt = $link->prepare("DELETE FROM shopping_cart WHERE kunden_id = ?");
        $stmt->bind_param("i", $kundenId);
        $stmt->execute();
        $stmt->close();

        // Weiterleitung nach dem Leeren des Warenkorbs und dem Senden der E-Mail
        header("Location: danke.php");
        exit();
    }
}

$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Checkout</title>
    <?php include '../php/include/headimport.php' ?>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2>Rechnungsadresse</h2>
            <div class="form-container">
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">Vorname</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Nachname</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="username">Benutzername</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">@</span>
                            </div>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email">E-Mail <span class="text-muted">(Optional)</span></label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>

                    <div class="mb-3">
                        <label for="address">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>

                    <div class="mb-3">
                        <label for="address2">Adresse 2 <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" id="address2" name="address2">
                    </div>

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="country">Land</label>
                            <select class="custom-select d-block w-100" id="country" name="country" required>
                                <option value="">Auswählen...</option>
                                <option>Deutschland</option>
                                <option>Österreich</option>
                                <option>Schweiz</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state">Bundesland</label>
                            <select class="custom-select d-block w-100" id="state" name="state" required>
                                <option value="">Auswählen...</option>
                                <option>Bayern</option>
                                <option>Berlin</option>
                                <option>Hamburg</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="zip">PLZ</label>
                            <input type="text" class="form-control" id="zip" name="zip" required>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <h4 class="mb-3">Versandart</h4>
                    <div class="d-block my-3">
                        <div class="custom-control custom-radio">
                            <input id="dhl" name="shipping_method" type="radio" class="custom-control-input" value="DHL"
                                   checked required>
                            <label class="custom-control-label" for="dhl">DHL (4,5€)</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="dhl_express" name="shipping_method" type="radio" class="custom-control-input"
                                   value="DHL Express" required>
                            <label class="custom-control-label" for="dhl_express">DHL Express (+6€)</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="lpd" name="shipping_method" type="radio" class="custom-control-input" value="LPD"
                                   required>
                            <label class="custom-control-label" for="lpd">LPD (+3€)</label>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <h4 class="mb-3">Zahlungsmethode</h4>
                    <div class="d-block my-3">
                        <div class="custom-control custom-radio">
                            <input id="credit" name="paymentMethod" type="radio" class="custom-control-input" checked
                                   required>
                            <label class="custom-control-label" for="credit">Kreditkarte</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="debit" name="paymentMethod" type="radio" class="custom-control-input" required>
                            <label class="custom-control-label" for="debit">Debitkarte</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="paypal" name="paymentMethod" type="radio" class="custom-control-input" required>
                            <label class="custom-control-label" for="paypal">PayPal</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cc-name">Name auf der Karte</label>
                            <input type="text" class="form-control" id="cc-name" name="cc_name" required>
                            <small class="text-muted">Vollständiger Name, wie auf der Karte angezeigt</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cc-number">Kreditkartennummer</label>
                            <input type="text" class="form-control" id="cc-number" name="cc_number" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cc-expiration">Ablaufdatum</label>
                            <input type="text" class="form-control" id="cc-expiration" name="cc_expiration" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cc-cvv">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" name="cc_cvv" required>
                        </div>
                    </div>

                    <hr class="mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="privacy_policy" name="privacy_policy"
                               required>
                        <label class="custom-control-label" for="privacy_policy">Ich akzeptiere die
                            Datenschutzrichtlinie</label>
                    </div>
                    <hr class="mb-4">
                    <input type="hidden" name="checkout" value="1">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Bezahlen</button>
                </form>
            </div>
        </div>
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Ihr Warenkorb</span>
                <span class="badge badge-primary badge-pill"><?php echo count($cartItems); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php
                $totalPrice = 0; // Reset totalPrice before recalculating
                foreach ($cartItems as $item) {
                    $discountedPrice = $item['price'] * (1 - $item['rabatt']);
                    $itemTotal = $discountedPrice * $item['quantity'];
                    echo '<li class="list-group-item d-flex justify-content-between lh-condensed">';
                    echo '<div>';
                    echo '<h6 class="my-0">' . htmlspecialchars($item['name']) . ' (' . htmlspecialchars($item['quantity']) . ')</h6>'; // Anzahl des Produktes neben dem Namen anzeigen
                    echo '<small class="text-muted">Preis: ' . htmlspecialchars(number_format($item['price'], 2)) . '€</small><br>';
                    echo '<small class="text-muted">- Rabatt: ' . htmlspecialchars(number_format($item['price'] * $item['rabatt'] * $item['quantity'], 2)) . '€</small>'; // Gesamtrabatt anzeigen
                    echo '</div>';
                    echo '<span class="text-muted">' . htmlspecialchars(number_format($itemTotal, 2)) . '€</span>';
                    echo '</li>';
                    $totalPrice += $itemTotal;
                }
                ?>
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Versandkosten</h6>
                        <small class="text-muted"><?php echo htmlspecialchars($shippingMethod); ?></small>
                    </div>
                    <span class="text-muted"><?php echo htmlspecialchars(number_format($shippingCost, 2)); ?>€</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Gesamt (EUR)</span>
                    <strong><?php echo htmlspecialchars(number_format($totalPrice + $shippingCost, 2)); ?>€</strong>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
