<?php
include_once 'include/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = $_POST['username'];
    $stmt = $link->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $response = array('exists' => $stmt->num_rows > 0);
    echo json_encode($response);
    $stmt->close();
}
?>