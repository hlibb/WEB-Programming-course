<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Willkommen</title>
    <?php include '../php/include/headimport.php'; ?>
</head>
<body>
<?php
include_once '../php/include/logged_in.php'; // Sitzungsprüfung hier sicherstellen

// Check if user is logged in
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
            <a href="cart.php"><button type="button" class="btn btn-primary button-spacing">
                    Warenkorb <span class="badge text-bg-secondary">x</span>
                </button></a>
        </nav>
        <div class="collapse" id="navbarToggleExternalContent">
            <div class="bg-dark p-4">
                <ul class="list-unstyled">
                    <li><a href="checkout.php">Checkout</a></li>
                    <li><a href="artikeluebersicht.php">Artikelübersicht</a></li>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="bestellungen.php">Meine Bestellungen</a></li>
                    <li><a href="about.php">About Us</a></li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <?php
    // Check if user is logged in
    if (isset($_SESSION['email']) && isset($_SESSION['users_id'])) {
        // User is logged in, display welcome message and other content
        $username = $_SESSION['email'];
        $usersId = $_SESSION['users_id'];
        $lastLoginTimestamp = $_SESSION['previous_login'];
        setlocale(LC_TIME, 'de_DE.UTF-8');
        $date = new DateTime($lastLoginTimestamp);
        $formattedDate = strftime('%A, %d.%m.%Y', $date->getTimestamp());

        require_once '../php/include/db_connection.php';
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $usersId);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_assoc();
        // Display welcome message
        if ($users) {
            echo "<p>Herzlich Willkommen Herr/Frau {$users['surname']}. Sie waren zuletzt am $formattedDate online.</p>";
        } else {
            echo "<p>Willkommen, unbekannte User!</p>";
        }
    } else {
        echo "<p>Willkommen, nicht eingeloggte User!</p>";
    }
    ?>
    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <a href="product_details.php?id=1"><img
                            src="https://schreibundstil.de/cdn/shop/products/Montblanc-Kugelschreiber-Meisterstuck-Vergoldet-Classique-Schwarz_700x.jpg?v=1594057635"
                            class="d-block w-100" alt="1"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=2"><img
                            src="https://schreibundstil.de/cdn/shop/products/Kaweco-Fueller-DIA2-Gold_3_700x.jpg?v=1594895630"
                            class="d-block w-100" alt="2"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=3"><img
                            src="https://schreibundstil.de/cdn/shop/products/Lamy-Fueller-2000-Schwarz-3.1_700x.jpg?v=1613996674"
                            class="d-block w-100" alt="3"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=4"><img
                            src="https://schreibundstil.de/cdn/shop/products/Waldmann-Fueller-Xetra-Vienna-Lack-Schwarz-11_700x.jpg?v=1593538004"
                            class="d-block w-100" alt="4"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=5"><img
                            src="https://schreibundstil.de/cdn/shop/products/Cleo-Skribent-Kolbenfueller-Classic-Palladium-Gold-Schwarz-1_700x.jpg?v=1594663723"
                            class="d-block w-100" alt="5"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=6"><img
                            src="https://schreibundstil.de/cdn/shop/products/Caran-d_Ache-Fueller-Ecridor-Retro_3_700x.jpg?v=1595308676"
                            class="d-block w-100" alt="6"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=7"><img
                            src="https://schreibundstil.de/cdn/shop/products/Diplomat_Fuller_ExcellenceA2_LackSchwarz_3_700x.jpg?v=1600063565"
                            class="d-block w-100" alt="7"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=8"><img
                            src="https://schreibundstil.de/cdn/shop/products/Graf-von-Faber-Castell-Fueller-Guilloche-Olive-Green-02_700x.jpg?v=1594562957"
                            class="d-block w-100" alt="8"></a>
            </div>
            <div class="carousel-item">
                <a href="product_details.php?id=9"><img
                            src="https://schreibundstil.de/cdn/shop/products/Faber-Castell-Fueller-Ambition-Birnbaum_3_700x.jpg?v=1595129651"
                            class="d-block w-100" alt="9"></a>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<?php include '../php/include/footimport.php'; ?>
</body>
</html>
