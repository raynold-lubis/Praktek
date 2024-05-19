<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = $id = "";

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
}

// Prepare a select statement

$sql = "SELECT * FROM users WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    if (empty($id)) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id);
    }


    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            /* Fetch result row as an associative array. Since the result set
            contains only one row, we don't need to use while loop */
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            // URL doesn't contain valid id parameter. Redirect to error page
            header("location: error.php");
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have atleast 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters

            if (empty($_GET['id'])) {
                mysqli_stmt_bind_param($stmt, "si", $param_password, $_SESSION['id']);
            } else {
                mysqli_stmt_bind_param($stmt, "si", $param_password, $_GET['id']);
            }
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Password updated successfully. Destroy the session, and redirect to login page

                if (empty($_GET['id'])) {
                    session_destroy();
                    header("location: /praktik");
                } else {
                    header('location: admin.php');
                }

                exit();
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }
    </style>
</head>

<body>
    <?php

    include "nav.php"

        ?>

    <div class="container my-5">
        <h1 style='text-align :center;'>Reset Password</h1>
        <p class="mb-4" style='text-align: center;'>Silakan isi formulir ini untuk mengatur ulang kata sandi<b>
                <?php echo $row['nama'] ?>
            </b>
        </p>
        <form action="reset_password.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password"
                    class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $new_password; ?>">
                <span class="invalid-feedback">
                    <?php echo $new_password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"
                    class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback">
                    <?php echo $confirm_password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <?php
                if (empty($id)) {
                    ?>
                    <a class="btn btn-danger" href="profile.php">Back</a>
                    <?php
                } else {
                    ?>
                    <a class="btn btn-danger" href="edit_user.php?id=<?php echo $row['id'] ?>">Back</a>
                    <?php
                }
                ?>

            </div>
        </form>
    </div>
</body>

</html>