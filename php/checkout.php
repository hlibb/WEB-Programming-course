<?php
include_once 'include/logged_in.php';
include_once 'include/db_connection.php';
include_once 'send_email.php';

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

// Fetch cart items
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
$totalPrice -= $pointsValue; // Subtract points value from total price

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $address2 = $_POST['address2'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $paymentMethod = $_POST['paymentMethod'];
    $nameOnCard = $_POST['nameOnCard'];
    $cardNumber = $_POST['cardNumber'];
    $expiration = $_POST['expiration'];
    $cvv = $_POST['cvv'];
    $userId = $_SESSION['users_id'];

    // Insert order
    $stmt = $link->prepare("INSERT INTO orders (users_id, order_date, total_amount, shipping_method, is_express_shipping, is_paid) VALUES (?, NOW(), ?, ?, ?, ?)");
    $stmt->bind_param("idssi", $userId, $totalAmount, $shippingMethod, $isExpressShipping, $isPaid);

    $totalAmount = $totalPrice; // Calculate the total amount from the cart
    $shippingMethod = 'Standard'; // Example value
    $isExpressShipping = 0; // Example value
    $isPaid = 1; // Example value, assuming payment is successful

    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    $stmt = $link->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
    $stmt->close();

    // Clear cart
    $stmt = $link->prepare("DELETE FROM `cart-body` WHERE warenkorb_id = (SELECT id FROM `cart-header` WHERE users_id = ?)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Reset user points and add 25 points for completed order
    $stmt = $link->prepare("UPDATE points SET points = 25 WHERE users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Send email confirmation
    $emailTemplate = [
        'subject' => 'Order Confirmation',
        'body' => '<p>Thank you for your order!</p><p>Your order ID is ' . $orderId . '</p>'
    ];
    sendEmail($email, $firstName, $emailTemplate);

    header("Location: danke.php");
    exit();
}

$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <?php include "include/headimport.php"; ?>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container mt-5 checkout-container">
    <div class="row g-5">
        <div class="col-md-7 col-lg-8">
            <h4 class="mb-3 checkout-header">Billing address</h4>
            <form class="needs-validation form-container" novalidate method="POST">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="firstName" class="form-label">First name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label for="lastName" class="form-label">Last name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback" style="width: 100%;">
                                Your username is required.
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com">
                        <div class="invalid-feedback">
                            Please enter a valid email address for shipping updates.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                        <div class="invalid-feedback">
                            Please enter your shipping address.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" id="address2" name="address2">
                    </div>

                    <div class="col-md-5">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="">Choose...</option>
                            <option>Germany</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid country.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state" required>
                            <option value="">Choose...</option>
                            <option>Berlin</option>
                        </select>
                        <div class="invalid-feedback">
                            Please provide a valid state.
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="zip" class="form-label">Zip</label>
                        <input type="text" class="form-control" id="zip" name="zip" required>
                        <div class="invalid-feedback">
                            Zip code required.
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="same-address">
                    <label class="form-check-label" for="same-address">Shipping address is the same as my billing address</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="save-info">
                    <label class="form-check-label" for="save-info">Save this information for next time</label>
                </div>

                <hr class="my-4">

                <h4 class="mb-3">Payment</h4>

                <div class="my-3">
                    <div class="form-check">
                        <input id="credit" name="paymentMethod" type="radio" class="form-check-input" value="Credit card" required>
                        <label class="form-check-label" for="credit">Credit card</label>
                    </div>
                    <div class="form-check">
                        <input id="debit" name="paymentMethod" type="radio" class="form-check-input" value="Debit card" required>
                        <label class="form-check-label" for="debit">Debit card</label>
                    </div>
                    <div class="form-check">
                        <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" value="PayPal" required>
                        <label class="form-check-label" for="paypal">PayPal</label>
                    </div>
                </div>

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="cc-name" class="form-label">Name on card</label>
                        <input type="text" class="form-control" id="cc-name" name="nameOnCard" required>
                        <small class="text-muted">Full name as displayed on card</small>
                        <div class="invalid-feedback">
                            Name on card is required.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="cc-number" class="form-label">Credit card number</label>
                        <input type="text" class="form-control" id="cc-number" name="cardNumber" required>
                        <div class="invalid-feedback">
                            Credit card number is required.
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cc-expiration" class="form-label">Expiration</label>
                        <input type="text" class="form-control" id="cc-expiration" name="expiration" required>
                        <div class="invalid-feedback">
                            Expiration date required.
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cc-cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cc-cvv" name="cvv" required>
                        <div class="invalid-feedback">
                            Security code required.
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
            </form>
        </div>
        <div class="col-md-5 col-lg-4 order-md-last cart-summary">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Your cart</span>
                <span class="badge bg-primary rounded-pill"><?php echo count($cartItems); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php foreach ($cartItems as $item): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?>(<?php echo htmlspecialchars($item['quantity']); ?>)</h6>
                            <small class="text-muted"><?php echo $item['discount']; ?></small>
                        </div>
                        <span class="text-muted"><?php echo number_format($item['product_total'], 2); ?>€</span>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Rabatt</span>
                    <strong id="total-discount"><?php echo number_format($totalDiscount, 2); ?>€</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Punktewert</span>
                    <strong id="points-value"><?php echo number_format($pointsValue, 2); ?>€</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (EUR)</span>
                    <strong id="total-price"><?php echo number_format($totalPrice, 2); ?>€</strong>
                </li>
            </ul>

            <form class="card p-2 promo-code-group">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Promo code" id="promo-code-input">
                    <button type="button" class="btn btn-secondary" id="redeem-btn">Redeem</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include "include/footimport.php"; ?>
<script src="https://getbootstrap.com/docs/5.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="form-validation.js"></script>
<script>
    document.getElementById('redeem-btn').addEventListener('click', function() {
        var couponCode = document.getElementById('promo-code-input').value;

        if (couponCode.trim() === '') {
            alert('Please enter a promo code.');
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'redeem_coupon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.getElementById('total-price').textContent = response.newTotalPrice + '€';
                    var totalDiscountElement = document.getElementById('total-discount');
                    var currentTotalDiscount = parseFloat(totalDiscountElement.textContent);
                    var newTotalDiscount = currentTotalDiscount + parseFloat(response.discountAmount);
                    totalDiscountElement.textContent = newTotalDiscount.toFixed(2) + '€';
                    alert('Promo code applied. Discount: ' + response.discountAmount + '€');
                } else {
                    alert(response.message || 'An error occurred. Please try again.');
                }
            }
        };

        xhr.send('couponCode=' + encodeURIComponent(couponCode));
    });
</script>
</body>
</html>
