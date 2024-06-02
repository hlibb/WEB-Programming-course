<?php
// Datenbankverbindung herstellen
include_once '../include/db_connection.php';

// SQL-Abfrage, um einen Artikel abzurufen (Beispiel: Artikel mit ID 1)
$sql = "SELECT * FROM products WHERE id = 1"; // Sie können die Bedingung entsprechend anpassen, z.B. WHERE id = $_GET['id'] für dynamisches Abrufen basierend auf einer ID aus der URL

$result = $conn->query($sql);

// Überprüfe, ob die SQL-Abfrage erfolgreich war
if (!$result) {
    echo "Fehler bei der SQL-Abfrage: " . $conn->error;
} else {
    // Überprüfe, ob Daten zurückgegeben wurden
    if ($result->num_rows > 0) {
        // Ergebnisse ausgeben
        $row = $result->fetch_assoc();
        $productName = $row["name"];
        $productDescription = $row["description"];
        $productPrice = $row["price"];
        $productImage = $row["image_url"];

        // Debugging-Ausgaben
        echo "Produktname: " . $productName . "<br>";
        echo "Produktbeschreibung: " . $productDescription . "<br>";
        echo "Produktpreis: " . $productPrice . "<br>";
        echo "Produktbild: " . $productImage . "<br>";
    } else {
        echo "Keine Ergebnisse gefunden";
    }
}

$conn->close();
?>
