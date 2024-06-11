<?php
function getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost) {
    $subject = 'Payment Confirmation';

    $itemsHtml = '';
    foreach ($cartItems as $item) {
        $discountedPrice = $item['unit_price']; // Da unit_price in der Datenbank bereits der Rabattpreis sein sollte
        $itemTotal = $discountedPrice * $item['quantity'];
        $itemsHtml .= "
            <tr>
                <td>{$item['product_id']}</td> <!-- Produktname sollte hier eingefügt werden -->
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['unit_price'], 2) . "€</td>
                <td>0%</td> <!-- Rabatt ist nicht bekannt -->
                <td>" . number_format($itemTotal, 2) . "€</td>
            </tr>";
    }

    $totalPriceWithShipping = $totalPrice + $shippingCost;

    $body = "
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Payment Confirmation</title>
        <style>
            body {
                width: 100%;
                margin: 0;
                background-color: #1b1b32;
                color: #f5f6f7;
                font-family: Tahoma, sans-serif;
                font-size: 16px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
                background-color: #f9f9f9;
                color: #1b1b32;
            }
            .header {
                background-color: #007bff;
                color: #fff;
                padding: 10px;
                text-align: center;
            }
            .content {
                margin: 20px 0;
            }
            .footer {
                background-color: #007bff;
                color: #fff;
                text-align: center;
                padding: 10px;
                font-size: 0.8em;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                background-color: #282847;
                color: #f5f6f7;
            }
            table, th, td {
                border: 1px solid #3b3f7;
            }
            th, td {
                padding: 10px;
                text-align: left;
            }
            thead th {
                background-color: #3b3b4f;
            }
            h1, p {
                margin: 1em auto;
                text-align: center;
                font-family: Apple Chancery, sans-serif, cursive;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Payment Confirmation</h1>
            </div>
            <div class='content'>
                <p>Dear $recipientName,</p>
                <p>Thank you for your payment. Here are the details of your order:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        $itemsHtml
                        <tr>
                            <td colspan='4'><strong>Shipping ({$shippingMethod})</strong></td>
                            <td><strong>" . number_format($shippingCost, 2) . "€</strong></td>
                        </tr>
                        <tr>
                            <td colspan='4'><strong>Total</strong></td>
                            <td><strong>" . number_format($totalPriceWithShipping, 2) . "€</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Ihr Unternehmen. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </body>
    </html>";

    return ['subject' => $subject, 'body' => $body];
}
?>
