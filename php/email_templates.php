<?php
function getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost) {
    $subject = 'Payment Confirmation';

    $itemsHtml = '';
    foreach ($cartItems as $item) {
        $discountedPrice = $item['unit_price'];
        $itemTotal = $discountedPrice * $item['quantity'];
        $itemsHtml .= "
            <tr>
                <td>{$item['product_id']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['unit_price'], 2) . "€</td>
                <td>0%</td>
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
                background-color: #282847;
                color: #f5f6f7;
                font-family: Tahoma, sans-serif;
                font-size: 16px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #3b3f7;
                background-color: #282847;
                color: #f5f6f7;
            }
            .header {
                background-color: #3b3b4f;
                color: #fff;
                padding: 10px;
                text-align: center;
            }
            .content {
                margin: 20px 0;
            }
            .footer {
                background-color: #3b3b4f;
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
                <h1 style='color: #fff;'>Payment Confirmation</h1>
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

function getRegistrationEmail($recipientName, $username, $temporaryPassword) {
    $subject = 'Account Registration';

    $body = "
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Account Registration</title>
        <style>
            body {
                width: 100%;
                margin: 0;
                background-color: #282847;
                color: #f5f6f7;
                font-family: Tahoma, sans-serif;
                font-size: 16px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #3b3f7;
                background-color: #282847;
                color: #f5f6f7;
            }
            .header {
                background-color: #3b3b4f;
                color: #fff;
                padding: 10px;
                text-align: center;
            }
            .content {
                margin: 20px 0;
            }
            .footer {
                background-color: #3b3b4f;
                color: #fff;
                text-align: center;
                padding: 10px;
                font-size: 0.8em;
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
                <h1 style='color: #fff;'>Accounterstellung</h1>
            </div>
            <div class='content'>
                <p>Liebe/r $recipientName,</p>
                <p>Vielen Dank für Ihre Registrierung. Hier sind Ihre Login-Daten:</p>
                <p>Benutzername: $username</p>
                <p>Temporäres Passwort: $temporaryPassword</p>
                <p>Bitte loggen Sie sich mit ihrem temporärem Passwort ein und ändern sie dies.</p>
            </div>
            <div class='footer'>
                <p>&copy; Ink & Inspiration. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    return ['subject' => $subject, 'body' => $body];
}
function getResetPasswordEmail($recipientName, $newPassword) {
    $subject = 'Passwort zurückgesetzt';

    $body = "
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Passwort zurückgesetzt</title>
        <style>
            body {
                width: 100%;
                margin: 0;
                background-color: #282847;
                color: #f5f6f7;
                font-family: Tahoma, sans-serif;
                font-size: 16px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #3b3f7;
                background-color: #282847;
                color: #f5f6f7;
            }
            .header {
                background-color: #3b3b4f;
                color: #fff;
                padding: 10px;
                text-align: center;
            }
            .content {
                margin: 20px 0;
            }
            .footer {
                background-color: #3b3b4f;
                color: #fff;
                text-align: center;
                padding: 10px;
                font-size: 0.8em;
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
                <h1 style='color: #fff;'>Passwort zurückgesetzt</h1>
            </div>
            <div class='content'>
                <p>Hallo $recipientName,</p>
                <p>Ihr Passwort wurde zurückgesetzt. Hier ist Ihr neues temporäres Passwort:</p>
                <p><strong>$newPassword</strong></p>
                <p>Bitte loggen Sie sich ein und ändern Sie Ihr Passwort sofort.</p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Ihr Unternehmen. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </body>
    </html>";

    return ['subject' => $subject, 'body' => $body];
}