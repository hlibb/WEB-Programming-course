<?php
function getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost, $totalDiscount) {
    $subject = 'Payment Confirmation';

    $itemsHtml = '';
    foreach ($cartItems as $item) {
        $discountRate = 0; // Set to 0% as the discount is applied to the total
        $itemTotal = $item['product_total'];
        $itemsHtml .= "
            <tr>
                <td>{$item['name']}</td>
                <td>{$item['quantity']}</td>
                <td>" . number_format($item['price'], 2) . "€</td>
                <td>{$item['discount']}</td>
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

require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('getPaymentConfirmationEmail')) {
    function getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost, $totalDiscount)
    {
        $subject = 'Payment Confirmation';
        // Der restliche Code bleibt unverändert
    }
}

if (!function_exists('getRegistrationEmail')) {
    function getRegistrationEmail($recipientName, $username, $temporaryPassword)
    {
        $subject = 'Account Registration';
        // Der restliche Code bleibt unverändert
    }
}

if (!function_exists('getResetPasswordEmail')) {
    function getResetPasswordEmail($recipientName, $newPassword)
    {
        $subject = 'Passwort zurückgesetzt';
        // Der restliche Code bleibt unverändert
    }
}

if (!function_exists('getContactEmail')) {
    function getContactEmail($name, $email, $subject, $message)
    {
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
                    <p>Eine neue Kontaktanfrage wurde gesendet.</p>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>E-Mail:</strong> $email</p>
                    <p><strong>Betreff:</strong> $subject</p>
                    <p><strong>Nachricht:</strong> $message</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 Ihr Unternehmen. Alle Rechte vorbehalten.</p>
                </div>
            </div>
        </body>
        </html>";

        return ['subject' => $subject, 'body' => $body];
    }
}

require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

if (!function_exists('getPaymentConfirmationEmail')) {
    function getPaymentConfirmationEmail($recipientName, $cartItems, $totalPrice, $shippingMethod, $shippingCost, $totalDiscount)
    {
        $subject = 'Payment Confirmation';
        // Der restliche Code bleibt unverändert
    }
}

if (!function_exists('getRegistrationEmail')) {
    function getRegistrationEmail($recipientName, $username, $temporaryPassword)
    {
        $subject = 'Account Registration';
        // Der restliche Code bleibt unverändert
    }
}

if (!function_exists('getResetPasswordEmail')) {
    function getResetPasswordEmail($recipientName, $newPassword)
    {
        $subject = 'Passwort zurückgesetzt';
        // Der restliche Code bleibt unverändert
    }
}

if (!function_exists('getApplicationEmail')) {
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
                    <p>Eine neue Bewerbung für die Position <strong>$job</strong> wurde eingereicht.</p>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>E-Mail:</strong> $email</p>
                    <p><strong>Anschreiben:</strong></p>
                    <p>$cover_letter</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 Ihr Unternehmen. Alle Rechte vorbehalten.</p>
                </div>
            </div>
        </body>
        </html>";

        return ['subject' => $subject, 'body' => $body];
    }
}



