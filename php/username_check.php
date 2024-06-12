<?php
require_once 'include/db_connection.php';
global $link;

function jsonResponse($status, $message) {
    echo json_encode(array($status => $message));
    exit;
}

header('Content-Type: application/json');

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);

    $sql = "SELECT * FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) >= 1) {
                jsonResponse("exists", true);
            } else {
                jsonResponse("exists", false);
            }
        } else {
            jsonResponse("error", "Query execution failed");
        }
        mysqli_stmt_close($stmt);
    } else {
        jsonResponse("error", "Statement preparation failed");
    }
    mysqli_close($link);
} else {
    jsonResponse("error", "Invalid request");
}
