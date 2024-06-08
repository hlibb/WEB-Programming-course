<?php
global $link;

if (isset($_POST['username'])) {
    // Sanitize the input
    $username = trim($_POST['username']);

    // Prepare the SQL statement to check the username
    $sql = "SELECT * FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind the username parameter
        mysqli_stmt_bind_param($stmt, "s", $username);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            // Check if the username exists
            if (mysqli_stmt_num_rows($stmt) >= 1) {
                echo json_encode(array("exists" => true));
            } else {
                echo json_encode(array("exists" => false));
            }
        } else {
            echo json_encode(array("error" => "Query execution failed"));
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(array("error" => "Statement preparation failed"));
    }

    // Close the connection
    mysqli_close($link);
} else {
    echo json_encode(array("error" => "Invalid request"));
}
?>
