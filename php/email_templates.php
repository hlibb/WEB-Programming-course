<?php
function getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost, $totalDiscount) {
    $subject = 'Payment Confirmation';

    $itemsHtml = '';
    foreach ($cartItems as $item) {
        $discountRate = 0; // Set to 0% as the discount is applied to the total
        $itemTotal = $item['product_total'];
        $itemsHtml .= "
            <tr>
                <td style='color: #f5f6f7;'>{$item['name']}</td>
                <td style='color: #f5f6f7;'>{$item['quantity']}</td>
                <td style='color: #f5f6f7;'>" . number_format($item['price'], 2) . "€</td>
                <td style='color: #f5f6f7;'>{$item['discount']}</td>
                <td style='color: #f5f6f7;'>" . number_format($itemTotal, 2) . "€</td>
            </tr>";
    }

    $totalPriceWithShipping = $totalPrice + $shippingCost - $totalDiscount;

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
                <p style='color: #f5f6f7;'>Liebe/r $recipientName,</p>
                <p style='color: #f5f6f7;'>Vielen Dank für deine Bestellung. Hier sind die Bestelldetails:</p>
                <table>
                    <thead>
                        <tr>
                            <th style='color: #f5f6f7;'>Produkt</th>
                            <th style='color: #f5f6f7;'>Menge</th>
                            <th style='color: #f5f6f7;'>Einzelpreis</th>
                            <th style='color: #f5f6f7;'>Rabatt</th>
                            <th style='color: #f5f6f7;'>Produktgesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        $itemsHtml
                        <tr>
                            <td colspan='4' style='color: #f5f6f7;'><strong>Versand: ({$shippingMethod})</strong></td>
                            <td style='color: #f5f6f7;'><strong>" . number_format($shippingCost, 2) . "€</strong></td>
                        </tr>
                        <tr>
                            <td colspan='4' style='color: #f5f6f7;'><strong>Rabatt:</strong></td>
                            <td style='color: #f5f6f7;'><strong>-" . number_format($totalDiscount, 2) . "€</strong></td>
                        </tr>
                        <tr>
                            <td colspan='4' style='color: #f5f6f7;'><strong>Gesamt:</strong></td>
                            <td style='color: #f5f6f7;'><strong>" . number_format($totalPriceWithShipping, 2) . "€</strong></td>
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
                <p style='color: #f5f6f7;'>Liebe/r $recipientName,</p>
                <p style='color: #f5f6f7;'>Vielen Dank für Ihre Registrierung. Hier sind Ihre Login-Daten:</p>
                <p style='color: #f5f6f7;'>Benutzername: $username</p>
                <p style='color: #f5f6f7;'>Temporäres Passwort: <span style='color: #ffffff;'>$temporaryPassword</span></p>
                <p style='color: #f5f6f7;'>Bitte loggen Sie sich mit ihrem temporärem Passwort ein und ändern sie dies.</p>
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
                <p style='color: #f5f6f7;'>Hallo $recipientName,</p>
                <p style='color: #f5f6f7;'>Ihr Passwort wurde zurückgesetzt. Hier ist Ihr neues temporäres Passwort:</p>
                <p style='color: #ffffff;'><strong>$newPassword</strong></p>
                <p style='color: #f5f6f7;'>Bitte loggen Sie sich ein und ändern Sie Ihr Passwort sofort.</p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Ihr Unternehmen. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </body>
    </html>";

    return ['subject' => $subject, 'body' => $body];
}

function getContactEmail($name, $email, $subject, $message) {
    $subject = 'Neue Kontaktanfrage: ' . $subject;

    $body = "
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Kontaktanfrage</title>
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
                <h1 style='color: #fff;'>Kontaktanfrage</h1>
            </div>
            <div class='content'>
                <p style='color: #f5f6f7;'>Eine neue Kontaktanfrage wurde gesendet.</p>
                <p style='color: #f5f6f7;'><strong>Name:</strong> $name</p>
                <p style='color: #f5f6f7;'><strong>E-Mail:</strong> $email</p>
                <p style='color: #f5f6f7;'><strong>Betreff:</strong> $subject</p>
                <p style='color: #f5f6f7;'><strong>Nachricht:</strong> $message</p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Ihr Unternehmen. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </body>
    </html>";

    return ['subject' => $subject, 'body' => $body];
}

function getApplicationEmail($job, $name, $email, $cover_letter) {
    $subject = 'Neue Bewerbung für ' . $job;

    $body = "
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Neue Bewerbung</title>
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
                <h1 style='color: #fff;'>Neue Bewerbung</h1>
            </div>
            <div class='content'>
                <p style='color: #f5f6f7;'>Eine neue Bewerbung für die Position <strong>$job</strong> wurde eingereicht.</p>
                <p style='color: #f5f6f7;'><strong>Name:</strong> $name</p>
                <p style='color: #f5f6f7;'><strong>E-Mail:</strong> $email</p>
                <p style='color: #f5f6f7;'><strong>Anschreiben:</strong></p>
                <p style='color: #f5f6f7;'>$cover_letter</p>
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
<?php
function getPaymentConfirmationEmail1($recipientName, $orderItems, $totalPrice, $shippingMethod, $shippingCost, $totalDiscount) {
    $subject = 'Payment Confirmation';

    $itemsHtml = '';
    foreach ($orderItems as $item) {
        $itemTotal = $item['unit_price'] * $item['quantity'];
        $itemsHtml .= "
            <tr>
                <td>{$item['product_name']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['unit_price'], 2) . "€</td>
                <td>" . (isset($item['rabatt']) ? $item['rabatt'] . '%' : '0%') . "</td>
                <td>" . number_format($itemTotal, 2) . "€</td>
            </tr>";
    }

    $totalPriceWithShipping = $totalPrice + $shippingCost - $totalDiscount;

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
                <p>Liebe/r $recipientName,</p>
                <p>Vielen Dank für deine Bestellung. Hier sind die Bestelldetails:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Menge</th>
                            <th>Einzelpreis</th>
                            <th>Rabatt</th>
                            <th>Produktgesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        $itemsHtml
                        <tr>
                            <td colspan='4'><strong>Versand: ({$shippingMethod})</strong></td>
                            <td><strong>" . number_format($shippingCost, 2) . "€</strong></td>
                        </tr>
                        <tr>
                            <td colspan='4'><strong>Rabatt:</strong></td>
                            <td><strong>-" . number_format($totalDiscount, 2) . "€</strong></td>
                        </tr>
                        <tr>
                            <td colspan='4'><strong>Gesamt:</strong></td>
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
