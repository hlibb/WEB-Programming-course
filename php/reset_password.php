<!DOCTYPE html>
<html lang="de">
<head>
    <title>Passwort zurücksetzen</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../php/include/headimport.php' ?>
    <script>
        function validateEmailForm() {
            var email = document.getElementById('email').value;
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (!emailPattern.test(email)) {
                alert('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Passwort zurücksetzen</h1>
    <p>Bitte geben Sie Ihre E-Mail-Adresse ein, um ein neues Passwort zu erhalten.</p>
    <form action="reset_password_process.php" method="post" onsubmit="return validateEmailForm()">
        <div class="form-group">
            <label for="email">E-Mail</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Passwort zurücksetzen</button>
    </form>
</div>
</body>
</html>
