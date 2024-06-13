<?php
require_once 'send_email.php';
require_once 'email_templates.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // E-Mail-Vorlage erstellen
    $emailTemplate = getContactEmail($name, $email, $subject, $message);

    // E-Mail senden
    sendEmail('webprogrammierung27@gmail.com', 'Kontaktformular', $emailTemplate);

    // Weiterleitung nach dem Absenden
    header("Location: feedmail.php");
    exit();
}
?>
