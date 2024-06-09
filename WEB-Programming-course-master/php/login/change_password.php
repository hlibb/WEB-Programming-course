<?php
session_start();

global $link; //um die sache zu standartisieren, wäre gut überall global $link (aus db_connection.php) haben statt immer neu code schreiben.

// Verbindung überprüfen
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET password = '$new_password', first_login = FALSE WHERE id = $user_id";

    if ($link->query($sql) === TRUE) {
        echo "Password changed successfully";
        session_unset();
        session_destroy();
    } else {
        echo "Error updating record: " . $link->error;
    }

    $link->close();

} else {
    echo "Unauthorized access";
}
?>
