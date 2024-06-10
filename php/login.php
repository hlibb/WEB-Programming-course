<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../php/include/headimport.php' ?>

</head>
<body>
<br><br>
<div style="width: 400px; margin: auto;">

    <div class="panel panel-info">
        <div class="panel-heading"><h1>Login</h1></div>
        <div class="panel-body">
            <p class="text-warning">Login to make a purchase</p>
            <form action="login_script.php" method="post">
                <div class="form-group">Email
                    <input type="text" name="email" class="form-control">
                </div>
                <div class="form-group">Password
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="text-danger"><?php if (isset($_GET['error'])) {
                        echo htmlspecialchars($_GET['error']);
                    } ?></div>
                <input type="submit" value="Login"/>
            </form>

        </div>
        <div class="panel-footer">
            <p class='text-info'>Don't have an account? <a href="registration.php">Register</a></p>
        </div>
        <div class="panel-footer">
            <p class='text-info'><a href="reset_password.php">Passwort vergessen?</a></p>
        </div>
    </div>

</div>
</body>
</html>