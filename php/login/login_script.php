<?php
// Initialize the session
global $link;
session_start();

// Include config file
require_once '../include/db_connection.php';

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_err = "Please enter your password.";
    } else {
        $password = htmlspecialchars(trim($_POST["password"]));

        // Validate password format
        if (strlen($password) < 6 || !preg_match("/^[a-zA-Z0-9!@#$%^&*_]+$/", $password)) {
            $password_err = "Password must be at least 6 characters long and contain letters, numbers, and special characters (!@#$%^&*_).";
        }
    }


    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, email, password FROM users WHERE email = ?";

        // Set parameters
        $param_email = $email;

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            echo $sql; //delete
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                echo mysqli_stmt_num_rows($stmt);
                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Start a new session if not already started
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }

                            // Store data in session variables
                            $_SESSION["logged_in"] = true;
                            $_SESSION["user_id"] = $id; // Assuming $id is the user's ID
                            $_SESSION["email"] = $email; // Assuming $email is the user's email

                            // Update login timestamp
                            $update_sql = "UPDATE users SET login_timestamp = NOW() WHERE id = ?";
                            if ($update_stmt = mysqli_prepare($link, $update_sql)) {
                                // Bind variables to the prepared statement as parameters
                                mysqli_stmt_bind_param($update_stmt, "i", $id);
                                // Attempt to execute the prepared statement
                                if (!mysqli_stmt_execute($update_stmt)) {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                                // Close statement
                                mysqli_stmt_close($update_stmt);
                            }

                            // Update user points
                            $points_sql = "UPDATE users SET points_awarded = points_awarded + 5 WHERE id = ?";
                            if ($points_stmt = mysqli_prepare($link, $points_sql)) {
                                // Bind variables to the prepared statement as parameters
                                mysqli_stmt_bind_param($points_stmt, "i", $id);
                                // Attempt to execute the prepared statement
                                if (!mysqli_stmt_execute($points_stmt)) {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                                // Close statement
                                mysqli_stmt_close($points_stmt);
                            }

                            // Redirect user to welcome page
                            header("location: ../../html/index.php");
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered is not valid.";
                        }
                    }
                } else {
                    // Display an error message if email doesn't exist
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/styles.css"/>
</head>
<body>
<br><br>
<div style="width: 400px;margin: auto;">

    <div class="panel panel-info">
        <div class="panel-heading"><h1>Login</h1></div>
        <div class="panel-body">
            <p class="text-warning">Login to make a purchase</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">Email
                    <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="error-message" style="color: red;"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">Password
                    <input type="password" name="password" class="form-control">
                    <span class="error-message" style="color: red;"><?php echo $password_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>
        <div class="panel-footer">
            <p class='text-info'>Don't have an account? <a href="../registration/registration.php">Register</a></p>
        </div>
    </div>

</div>
</body>
</html>
