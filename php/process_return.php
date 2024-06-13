<?php
session_start();
include_once 'include/db_connection.php';
include_once 'send_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['reason']) && isset($_SESSION['users_id'])) {
    $orderId = $_POST['order_id'];
    $reason = $_POST['reason'];
    $userId = $_SESSION['users_id'];

    // Check if the order belongs to the user and has not been returned
    $stmt = $link->prepare("SELECT id, total_amount FROM orders WHERE id = ? AND users_id = ? AND is_returned = FALSE");
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        // Fetch order items
        $stmt = $link->prepare("SELECT p.name, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $itemsResult = $stmt->get_result();
        $orderItems = [];
        while ($row = $itemsResult->fetch_assoc()) {
            $orderItems[] = $row;
        }

        // Process the return
        $stmt = $link->prepare("UPDATE orders SET is_returned = TRUE WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        // Get user email
        $stmt = $link->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $user = $userResult->fetch_assoc();
        $userEmail = $user['email'];

        // Prepare order details
        $orderDetails = "";
        foreach ($orderItems as $item) {
            $orderDetails .= "<tr><td>" . htmlspecialchars($item['name']) . "</td><td>" . htmlspecialchars($item['quantity']) . "</td></tr>";
        }

        $emailContentUser = [
            'subject' => 'Return information',
            'body' => "
            <html>
            <head>
                <style>
                    .email-container {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        background-color: #1B1B32;
                        color: #ffffff;
                        padding: 20px;
                        text-align: center;
                    }
                    .email-header {
                        background-color: #1B1B32;
                        color: #ffffff;
                        padding: 10px;
                    }
                    .email-body {
                        background-color: #f5f5f5;
                        color: #000000;
                        padding: 20px;
                        text-align: left;
                    }
                    .email-footer {
                        background-color: #1B1B32;
                        color: #ffffff;
                        padding: 10px;
                        text-align: center;
                    }
                    .order-details-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                    }
                    .order-details-table th, .order-details-table td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    .order-details-table th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h1>Rückgabe Bestätigung</h1>
                    </div>
                    <div class='email-body'>
                        <p>Liebe/r Kunde/in,</p>
                        <p>Ihre Rückgabe war erfolgreich! Hier sind die Details Ihrer Bestellung:</p>
                        <table class='order-details-table'>
                            <tr>
                                <th>Bestellnummer</th>
                                <td>$orderId</td>
                            </tr>
                            <tr>
                                <th>Gesamtbetrag</th>
                                <td>" . number_format($order['total_amount'], 2) . "€</td>
                            </tr>
                        </table>
                        <h3>Bestelldetails</h3>
                        <table class='order-details-table'>
                            <tr>
                                <th>Produkt</th>
                                <th>Menge</th>
                            </tr>
                            $orderDetails
                        </table>
                        <p>Vielen Dank für Ihren Einkauf bei uns!</p>
                    </div>
                    <div class='email-footer'>
                        <p>&copy; " . date("Y") . " Ink & Inspiration. Alle Rechte vorbehalten.</p>
                    </div>
                </div>
            </body>
            </html>"
        ];

        // Prepare email content for webshop
        $emailContentShop = [
            'subject' => 'Return information',
            'body' => "
            <html>
            <head>
                <style>
                    .email-container {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        background-color: #1B1B32;
                        color: #ffffff;
                        padding: 20px;
                        text-align: center;
                    }
                    .email-header {
                        background-color: #1B1B32;
                        color: #ffffff;
                        padding: 10px;
                    }
                    .email-body {
                        background-color: #f5f5f5;
                        color: #000000;
                        padding: 20px;
                        text-align: left;
                    }
                    .email-footer {
                        background-color: #1B1B32;
                        color: #ffffff;
                        padding: 10px;
                        text-align: center;
                    }
                    .order-details-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                    }
                    .order-details-table th, .order-details-table td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    .order-details-table th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h1>Rückgabe Information</h1>
                    </div>
                    <div class='email-body'>
                        <p>Ein Kunde hat eine Rückgabe initiiert. Hier sind die Details der Bestellung:</p>
                        <table class='order-details-table'>
                            <tr>
                                <th>Bestellnummer</th>
                                <td>$orderId</td>
                            </tr>
                            <tr>
                                <th>Gesamtbetrag</th>
                                <td>" . number_format($order['total_amount'], 2) . "€</td>
                            </tr>
                            <tr>
                                <th>Rückgabegrund</th>
                                <td>" . htmlspecialchars($reason) . "</td>
                            </tr>
                        </table>
                        <h3>Bestelldetails</h3>
                        <table class='order-details-table'>
                            <tr>
                                <th>Produkt</th>
                                <th>Menge</th>
                            </tr>
                            $orderDetails
                        </table>
                    </div>
                    <div class='email-footer'>
                        <p>&copy; Ink & Inspiration. Alle Rechte vorbehalten.</p>
                    </div>
                </div>
            </body>
            </html>"
        ];

        // Send email to user
        sendEmail($userEmail, "Kunde", $emailContentUser);

        // Send email to webshop
        $shopEmail = 'webprogrammierung27@gmail.com';
        sendEmail($shopEmail, "Webshop", $emailContentShop);

        // Redirect to the return page with a success message
        $_SESSION['return_success'] = true;
        header("Location: rueckgabe.php");
        exit();
    } else {
        // Redirect to the return page with an error message
        $_SESSION['return_error'] = true;
        header("Location: rueckgabe.php");
        exit();
    }

    $stmt->close();
}
$link->close();
?>
