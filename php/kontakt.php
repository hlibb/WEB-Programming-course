<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontakt</title>
    <?php include '../php/include/headimport.php'; ?>
</head>
<body>
<?php include "include/navimport.php"; ?>

<div class="contact-container">
    <h2 class="contact-header">Kontaktieren Sie uns</h2>
    <form class="contact-form" action="send_contact_form.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="subject">Betreff:</label>
        <input type="text" id="subject" name="subject" required>

        <label for="message">Nachricht:</label>
        <textarea id="message" name="message" rows="6" required></textarea>

        <button type="submit">Absenden</button>
    </form>
</div>

<?php include "include/footimport.php"; ?>
</body>
</html>
