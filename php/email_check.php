<?php
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $link->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $response = array('exists' => $stmt->num_rows > 0);
    echo json_encode($response);
    $stmt->close();
}
?>