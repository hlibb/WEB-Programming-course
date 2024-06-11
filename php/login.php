<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../node_modules/@fortawesome/fontawesome-free/css/all.css" rel="stylesheet">
    <?php
    include "fetch_online_users.php"
    ?>
    <link rel="icon" type="image/png" href="../assets/images/tabuicon.png">
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
            <form action="login_script.php" method="post">
                <div class="form-group">E-mail
                    <input type="text" name="email" class="form-control">
                </div>
                <div class="form-group">Passwort
                    <input type="password" name="password" class="form-control">
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
