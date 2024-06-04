<?php
// Datenbankverbindung herstellen
include_once '../include/db_connection.php';

// SQL-Abfrage, um einen Artikel abzurufen (Beispiel: Artikel mit ID 1)
$sql = "SELECT * FROM products WHERE id = 1"; // Sie können die Bedingung entsprechend anpassen, z.B. WHERE id = $_GET['id'] für dynamisches Abrufen basierend auf einer ID aus der URL

// Ergebnis der Abfrage erhalten
$result = $link->query($sql);

// Überprüfen, ob die Abfrage erfolgreich war
if ($result === false) {
    die("Query failed: " . $link->error);
}

// Überprüfen, ob Ergebnisse vorhanden sind
if ($result->num_rows > 0) {
    // Ergebnisse ausgeben
    $row = $result->fetch_assoc();
    $productName = $row["name"];
    $productDescription = $row["description"];
    $productPrice = $row["price"];
    $productImage = $row["image_url"];
} else {
    echo "0 results";
}

// Verbindung schließen
$link->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Artikelseite</title>
    <?php include "../include/headimport.php"; ?>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
<?php include "../include/navimport.php"; ?>
<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <img
                    src="<?php echo htmlspecialchars($productImage); ?>"
                    class="img-fluid"
                    alt="Artikelbild"
            />
        </div>
        <div class="col-md-6">
            <h1 class="mb-4"><?php echo htmlspecialchars($productName); ?></h1>
            <p class="lead mb-4"><?php echo htmlspecialchars($productDescription); ?></p>
            <p><strong>Preis:</strong> $<?php echo htmlspecialchars($productPrice); ?></p>
            <button class="btn btn-primary">In den Warenkorb legen</button>
        </div>
    </div>
</div>
<?php include "../include/footimport.php"; ?>
</body>
</html>
