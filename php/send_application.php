<?php
require 'send_email.php';
require 'email_templates.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job = $_POST['job'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $cover_letter = $_POST['cover_letter'];

    // E-Mail-Vorlage erstellen
    $emailTemplate = getApplicationEmail($job, $name, $email, $cover_letter);

    // E-Mail senden
    sendEmail('webprogrammierung27@example.com', 'Neue Bewerbung', $emailTemplate);

    // Weiterleitung nach dem Absenden
    header("Location: bewmail.php");
    exit();
}
?>
