<?php
// Datenbankverbindung herstellen
include_once '../include/db_connection.php';

// SQL-Abfrage, um alle Artikel-IDs abzurufen
$sql = "SELECT id, name FROM products";

// Ergebnis der Abfrage erhalten
$result = $link->query($sql);

// Überprüfen, ob die Abfrage erfolgreich war
if ($result === false) {
    die("Query failed: " . $link->error);
}

// Dropdown-Menü erstellen
$selectOptions = '';
while ($row = $result->fetch_assoc()) {
    $productId = $row['id'];
    $productName = $row['name'];
    $selectOptions .= "<option value='$productId'>$productName</option>";
}

// Verbindung schließen
$link->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produktseite</title>
    <?php include "../include/headimport.php"; ?>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
<?php include "../include/navimport.php"; ?>
<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <label for="product-select">Wählen Sie ein Produkt aus:</label>
            <select id="product-select" class="form-control">
                <option value="">Bitte wählen</option>
                <?php echo $selectOptions; ?>
            </select>
        </div>
    </div>
    <div class="row mt-3" id="product-details">
        <!-- Hier werden die Produktinformationen geladen -->
    </div>
</div>
<?php include "../include/footimport.php"; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        // Event-Handler für Änderungen im Dropdown-Feld
        $('#product-select').change(function () {
            var productId = $(this).val();

            // AJAX-Anfrage, um Produktinformationen basierend auf der ausgewählten ID abzurufen
            $.ajax({
                type: 'POST',
                url: 'get_product_details.php', // Hier den Pfad zu deinem PHP-Skript angeben, das die Produktinformationen zurückgibt
                data: { productId: productId },
                success: function (response) {
                    $('#product-details').html(response);
                },
                error: function () {
                    alert('Fehler beim Laden der Produktinformationen.');
                }
            });
        });
    });
</script>
</body>
</html>
