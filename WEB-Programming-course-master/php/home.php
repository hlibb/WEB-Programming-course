<!doctype html>
<html lang="en">
<head>
    <?php
    include "../php/include/headimport.php"
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Willkommen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php
include '../php/include/navimport.php';
?>
<div class="container">
    <?php
    include_once "../php/include/logged_in.php";
    // Check if user is logged in
    if (isset($_SESSION['email']) && isset($_SESSION['user_id'])) {
        // User is logged in, display welcome message and other content
        $username = $_SESSION['email'];
        $userId = $_SESSION['user_id'];
        // Fetch user data from database if necessary
        require_once '../php/include/db_connection.php';
        $userId = $_SESSION['user_id'];
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        // Display welcome message
        if ($user) {
            $loginTimestamp = $user['login_timestamp'];
            echo "<p>Herzlich Willkommen Herr/Frau {$user['surname']}. Sie waren zuletzt am $loginTimestamp online.</p>";
        } else {
            // User not found
            echo "<p>Willkommen! 1</p>";
        }
    } else {
        // User not logged in, handle accordingly
        echo "<p>Willkommen! 2</p>";
    }
    ?>
    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <a href="../php/products.php?id=1"><img
                            src="https://th.bing.com/th/id/OIP.6rsxXGY0IcuwUeMiVHA8vgAAAA?rs=1&pid=ImgDetMain"
                            class="d-block w-100" alt="1"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=2"><img
                            src="https://schreibundstil.de/cdn/shop/products/Kaweco-Fueller-DIA2-Gold_3_700x.jpg?v=1594895630"
                            class="d-block w-100" alt="2"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=3"><img
                            src="https://schreibundstil.de/cdn/shop/products/Lamy-Fueller-2000-Schwarz-3.1_700x.jpg?v=1613996674"
                            class="d-block w-100" alt="3"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=4"><img
                            src="https://schreibundstil.de/cdn/shop/products/Waldmann-Fueller-Xetra-Vienna-Lack-Schwarz-11_700x.jpg?v=1593538004"
                            class="d-block w-100" alt="4"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=5"><img
                            src="https://schreibundstil.de/collections/fullfederhalter-von-cleo-skribent/products/cleo-skribent-kolbenfuller-classic-palladium-gold-schwarz-1"
                            class="d-block w-100" alt="5"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=6"><img
                            src="https://schreibundstil.de/cdn/shop/products/Caran-d_Ache-Fueller-Ecridor-Retro_3_700x.jpg?v=1595308676"
                            class="d-block w-100" alt="6"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=7"><img
                            src="https://schreibundstil.de/cdn/shop/products/Diplomat_Fuller_ExcellenceA2_LackSchwarz_3_700x.jpg?v=1600063565"
                            class="d-block w-100" alt="7"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=8"><img
                            src="https://schreibundstil.de/cdn/shop/products/Graf-von-Faber-Castell-Fueller-Guilloche-Olive-Green-02_700x.jpg?v=1594562957"
                            class="d-block w-100" alt="8"></a>
            </div>
            <div class="carousel-item">
                <a href="../php/products.php?id=9"><img
                            src="https://schreibundstil.de/collections/faber-castell-fullfederhalter/products/faber-castell-fuller-ambition-birnbaum"
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
<?php
include '../php/include/footimport.php'
?>
</body>
</html>
