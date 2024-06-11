<!DOCTYPE html>
<html lang="de">
<head>
    <title>Passwort zurücksetzen</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../php/include/headimport.php' ?>
</head>
<body>
<div class="container">
    <h1>Passwort zurücksetzen</h1>
    <p>Bitte geben Sie Ihre E-Mail-Adresse ein, um ein neues Passwort zu erhalten.</p>
    <form action="reset_password_process.php" method="post">
        <div class="form-group">
            <label for="email">E-Mail</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Passwort zurücksetzen</button>
    </form>
</div>
</body>
</html>
