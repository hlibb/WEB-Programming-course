<?php
// Datenbankverbindung herstellen
include_once '../include/db_connection.php';

// SQL-Abfrage, um alle Artikel-IDs abzurufen
$sql = "SELECT name FROM products";

// Ergebnis der Abfrage erhalten
$result = $link->query($sql);

// Überprüfen, ob die Abfrage erfolgreich war
if ($result === false) {
    die("Query failed: " . $link->error);
}

// Dropdown-Menü erstellen
echo '<select name="product_id">';
while ($row = $result->fetch_assoc()) {
    $productId = $row['name'];
    echo "<option value='$productId'>$productId</option>";
}
echo '</select>';

// Verbindung schließen
$link->close();
?>
