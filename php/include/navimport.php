<?php
// Start the session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prüfen, ob der Benutzer eingeloggt ist
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

$host = "localhost";
$port = 3306;
$username = "root";
$password = "";
$database = "web-programming";


$link = new mysqli($host, $username, $password, $database);

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

// Fetch total items in cart if user is logged in
$cartItemCount = 0;
if ($isLoggedIn) {
    $userId = $_SESSION['users_id'];
    $stmt = $link->prepare("SELECT COUNT(*) AS total_items FROM `cart-body` cb
                            JOIN `cart-header` ch ON cb.warenkorb_id = ch.id
                            WHERE ch.users_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($cartItemCount);
    $stmt->fetch();
    $stmt->close();
}

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
        <nav class="navbar navbar-dark bg-dark d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <?php include "fetch_online_users.php"; ?>
                <span class="text-white ml-3">User Online: <?php echo $onlineUsers; ?></span>
            </div>
            <div class="ml-auto">
                <a href="cart.php" class="ml-3">
                    <button type="button" class="btn btn-primary button-spacing">
                        Warenkorb <span class="badge text-bg-secondary"><?php echo $cartItemCount; ?></span>
                    </button>
                </a>
            </div>
        </nav>
        <div class="collapse" id="navbarToggleExternalContent">
            <div class="bg-dark p-4">
                <ul class="list-unstyled">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="artikeluebersicht.php">Artikelübersicht</a></li>
                    <li><a href="bestellungen.php">Meine Bestellungen</a></li>
                    <li><a href="checkout.php">Checkout</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="rueckgabe.php">Kundenportal</a></li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .button-spacing {
        margin: 0 5px; /* Abstand um die Buttons herum */
    }
    .navbar-toggler {
        margin-right: 10px; /* Abstand zwischen Toggler und "User Online:" */
    }
    .ml-3 {
        margin-left: 10px; /* Abstand zwischen "User Online:" und Warenkorb */
    }
    .ml-auto {
        margin-left: auto; /* Automatischer linker Rand, um Warenkorb nach rechts zu schieben */
    }
</style>
