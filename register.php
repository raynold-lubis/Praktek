<?php

// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to petugas pendaftaran page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$nama = $alamat = $jenis_kelamin = $tanggal_lahir = $nomor_handphone = $user = "";
$username_err = $password_err = $confirm_password_err = "";
$nama_err = $alamat_err = $jenis_kelamin_err = $tanggal_lahir_err = $nomor_handphone_err = $user_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    //Validate nama
    if (empty(trim($_POST["nama"]))) {
        $nama_err = "Please enter a nama.";
    } else {
        $nama = trim($_POST["nama"]);
    }

    //Validate alamat
    if (empty(trim($_POST["alamat"]))) {
        $alamat_err = "Please enter a alamat.";
    } else {
        $alamat = trim($_POST["alamat"]);
    }

    //Validate jenis kelamin
    $jenis_kelamin = trim($_POST["jenis_kelamin"]);

    //Validate tanggal lahir
    if (empty(trim($_POST["tanggal_lahir"]))) {
        $tanggal_lahir_err = "Please enter a tanggal lahir.";
    } else {
        $tanggal_lahir = trim($_POST["tanggal_lahir"]);
    }

    //Validate nomor handphone
    if (empty(trim($_POST["nomor_handphone"]))) {
        $nomor_handphone_err = "Please enter a nomor handphone.";
    } else {
        $nomor_handphone = trim($_POST["nomor_handphone"]);
    }

    //Validate jenis kelamin
    $user = trim($_POST["user"]);

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($nama_err) && empty($alamat_err) && empty($jenis_kelamin_err) && empty($tanggal_lahir_err) && empty($nomor_handphone_err) && empty($user_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, nama, alamat, jenis_kelamin, tanggal_lahir, nomor_handphone, user) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssss", $param_username, $param_password, $param_nama, $param_alamat, $param_jenis_kelamin, $param_tanggal_lahir, $param_nomor_handphone, $param_user);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_nama = $nama;
            $param_alamat = $alamat;
            $param_jenis_kelamin = $jenis_kelamin;
            $param_tanggal_lahir = $tanggal_lahir;
            $param_nomor_handphone = $nomor_handphone;
            $param_user = $user;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: data_praktik.php");
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font: 14px sans-serif;
        }
    </style>
</head>

<body>
    <?php

    include "nav.php";

    ?>
    <div class="container my-5">
        <h1 style='text-align: center;'>Create Account</h1>
        <p class='mb-4' style='text-align:center;'>Silakan isi formulir untuk membuat akun</p>
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
                    class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $password; ?>">
                <span class="invalid-feedback">
                    <?php echo $password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"
                    class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback">
                    <?php echo $confirm_password_err; ?>
                </span>
            </div>
            <!-- Nama -->
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama"
                    class="form-control <?php echo (!empty($nama_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $nama; ?>">
                <span class="invalid-feedback">
                    <?php echo $nama_err; ?>
                </span>
            </div>
            <!-- Alamat -->
            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat"
                    class="form-control <?php echo (!empty($alamat_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $alamat; ?>">
                <span class="invalid-feedback">
                    <?php echo $alamat_err; ?>
                </span>
            </div>
            <!-- Jenis Kelamin -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Jenis Kelamin</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01" name="jenis_kelamin">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir"
                    class="form-control <?php echo (!empty($tanggal_lahir_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $tanggal_lahir; ?>">
                <span class="invalid-feedback">
                    <?php echo $tanggal_lahir_err; ?>
                </span>
            </div>
            <!-- Nomor Handphone -->
            <!-- Nomor telepon di profile sesuaikan input type nya dan digitnya -->
            <div class="form-group">
                <label>Nomor Handphone</label>
                <input type="tel" name="nomor_handphone" placeholder="08xx-xxxx-xxxx"
                    pattern="[0-9]{4}-[0-9]{4}-[0-9]{3,4}"
                    class="form-control <?php echo (!empty($nomor_handphone_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $nomor_handphone; ?>">
                <span class="invalid-feedback">
                    <?php echo $nomor_handphone_err; ?>
                </span>
            </div>
            <!-- User -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">User</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01" name="user">
                    <option value="Petugas Pendaftaran">Petugas Pendaftaran</option>
                    <option value="Dokter">Dokter</option>
                    <option value="Apoteker">Apoteker</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-danger" href="data_praktik.php">Back</a>
            </div>
        </form>
    </div>
</body>

</html>