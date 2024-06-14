<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../php/include/headimport.php' ?>
    <script>
        async function hashPassword(password) {
            const msgUint8 = new TextEncoder().encode(password);
            const hashBuffer = await crypto.subtle.digest('SHA-512', msgUint8);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            return hashHex;
        }

        async function validateForm(event) {
            event.preventDefault();
            const passwordInput = document.getElementById('password');
            const hashedPasswordInput = document.createElement('input');
            hashedPasswordInput.type = 'hidden';
            hashedPasswordInput.name = 'hashed_password';
            hashedPasswordInput.value = await hashPassword(passwordInput.value);
            passwordInput.parentNode.appendChild(hashedPasswordInput);
            passwordInput.value = ''; // Clear the plain text password
            event.target.submit();
        }
    </script>
    <style>
        .center-button {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="panel panel-info">
        <div class="panel-heading"><h1>Login</h1></div>
        <div class="panel-body">
            <div class="center-button">
                <a href="artikeluebersicht.php"><button class="btn btn-secondary">Produkte ansehen</button></a>
            </div>
            <p class="text-warning">Für weitere Funktionen bitte einloggen</p>
            <form action="login_script.php" method="post" onsubmit="validateForm(event)">
                <div class="form-group">E-mail oder Benutzername
                    <input type="text" name="email_or_username" class="form-control" required>
                </div>
                <div class="form-group">Passwort
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">TOTP Code
                    <input type="text" name="totp_code" class="form-control" required>
                </div>
                <div class="text-danger"><?php if (isset($_GET['error'])) {
                        echo htmlspecialchars($_GET['error']);
                    } ?></div>
                <input type="submit" value="Login" class="btn btn-primary"/>
            </form>
        </div>
        <div class="panel-footer">
            <p class='text-info'>Haben Sie keinen Account? <a href="registration.php">Register</a></p>
        </div>
        <div class="panel-footer">
            <p class='text-info'><a href="reset_password.php">Passwort vergessen?</a></p>
        </div>
    </div>
</div>
</body>
</html>
