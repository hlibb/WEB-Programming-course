<?php
// Start the session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Prüfen, ob der Benutzer eingeloggt ist
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<a href="home.php"><img src="../assets/images/logo.png" class="logo"></a>
<div class="text-right mt-2">
    <?php if ($isLoggedIn): ?>
        <a href="include/logout.php" class="btn btn-danger button-spacing">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn btn-primary button-spacing">Login</a>
    <?php endif; ?>
</div>
<?php if ($isLoggedIn): ?>
    <div class="pos-f-t">
        <nav class="navbar navbar-dark bg-dark">
            <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="shopping_cart.php"><button type="button" class="btn btn-primary button-spacing">
                    Warenkorb <span class="badge text-bg-secondary">x</span>
                </button></a>
        </nav>
        <div class="collapse" id="navbarToggleExternalContent">
            <div class="bg-dark p-4">
                <ul class="list-unstyled">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="artikeluebersicht.php">Artikelübersicht</a></li>
                    <li><a href="bestellungen.php">Meine Bestellungen</a></li>
                    <li><a href="checkout.php">Checkout</a></li>
                    <li><a href="about.php">About Us</a></li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .button-spacing {
        margin: 0 5px; /* Abstand um die Buttons herum */
    }
</style>
