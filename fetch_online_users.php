<?php
// Start
session_start();

// Unique Nutzer Prüfen
if (!isset($_SESSION['unique_user'])) {
    // Nutzerinitialisierung
    $_SESSION['unique_user'] = true;

    //Überprüfen ob es Sessions gibt
    if (!isset($_SESSION['online_users'])) {
        //Einem Nutzer den Wert zuordnen
        $_SESSION['online_users'] = 1;
    } else {
        //Nutzer hinzufügen
        $_SESSION['online_users']++;
    }
}

//Online nutzer abfragen
$onlineUsersCount = $_SESSION['online_users'];

//Nutzerzahl senden
echo $onlineUsersCount;
?>
