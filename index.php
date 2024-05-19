<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to user page
if (isset($_SESSION["loggedin"]) == true) {
    $url = $_SESSION['user'] . ".php";
    header("location: $url");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $user = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {

        // Prepare a select statement
        $sql = "SELECT id, password, user FROM users WHERE username = '$username'";

        // Create a prepared statement
        if ($stmt = mysqli_prepare($link, $sql)) {

            // Execute the prepared statement
            mysqli_stmt_execute($stmt);

            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if username exists, if yes then verify password
            if (mysqli_stmt_num_rows($stmt) == 1) {

                // Bind result variables
                mysqli_stmt_bind_result($stmt, $id, $hashed_password, $user);

                // Fetch values
                mysqli_stmt_fetch($stmt);

                if (password_verify($password, $hashed_password)) {

                    // Password is correct, so start a new session
                    session_start();

                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["user"] = $user;

                    // Redirect user to page
                    if ($user == "Petugas Pendaftaran") {
                        header("location: petugas_pendaftaran.php");
                    } elseif ($user == "Dokter" || $user == "Active") {
                        header("location: dokter.php");
                    } elseif ($user == "Apoteker") {
                        header("location: apoteker.php");
                    } else if ($user == "Admin") {
                        header("location: admin.php");
                    }

                } else {
                    // Password is not valid, display a generic error message
                    $login_err = "Invalid password.";
                }

            } else {

                // Username doesn't exist, display a generic error message
                $login_err = "Invalid username.";

            }

            // Close statement
            mysqli_stmt_close($stmt);

        }

        // Close connection
        mysqli_close($link);

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                    class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $username; ?>">
                <span class="invalid-feedback">
                    <?php echo $username_err; ?>
                </span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                    class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback">
                    <?php echo $password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>

</body>

</html>