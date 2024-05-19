<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";


// Define variables and initialize with empty values
$username = $nama = $alamat = $jenis_kelamin = $tanggal_lahir = $nomor_handphone = "";
$username_err = $nama_err = $alamat_err = $jenis_kelamin_err = $tanggal_lahir_err = $nomor_handphone_err = "";

$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id ='$id'";

if ($stmt = mysqli_prepare($link, $sql)) {

    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        }

    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

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


                // Jika ingin mengubah nama saja di profile tidak harus ubah username juga
                if ($_POST["username"] != $row["username"]) {
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $username_err = "This username is already taken.";
                    } else {
                        $username = trim($_POST["username"]);
                    }
                }

            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }


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

    $user = trim($_POST["user"]);

    // Check input errors before updating the database
    if (empty($username_err) && empty($nama_err) && empty($alamat_err) && empty($jenis_kelamin_err) && empty($tanggal_lahir_err) && empty($nomor_handphone_err)) {


        if ($_POST["username"] != $row["username"]) {

            // Prepare an update statement
            $sql = "UPDATE users SET username = ?, nama = ?, alamat = ?, jenis_kelamin = ?, tanggal_lahir = ?, nomor_handphone = ?, user = ? WHERE id = '$id'";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssssss", $param_username, $param_nama, $param_alamat, $param_jenis_kelamin, $param_tanggal_lahir, $param_nomor_handphone, $param_user);

                // Set parameters
                $param_username = $username;
                $param_nama = $nama;
                $param_alamat = $alamat;
                $param_jenis_kelamin = $jenis_kelamin;
                $param_tanggal_lahir = $tanggal_lahir;
                $param_nomor_handphone = $nomor_handphone;
                $param_user = $user;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    header("location: edit_user.php?id=$id&success");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

        } else {

            // Prepare an update statement
            $sql = "UPDATE users SET nama = ?, alamat = ?, jenis_kelamin = ?, tanggal_lahir = ?, nomor_handphone = ?, user = ? WHERE id = '$id'";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssss", $param_nama, $param_alamat, $param_jenis_kelamin, $param_tanggal_lahir, $param_nomor_handphone, $param_user);

                // Set parameters
                $param_nama = $nama;
                $param_alamat = $alamat;
                $param_jenis_kelamin = $jenis_kelamin;
                $param_tanggal_lahir = $tanggal_lahir;
                $param_nomor_handphone = $nomor_handphone;
                $param_user = $user;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    header("location: edit_user.php?id=$id&success");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
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

    include "nav.php"

        ?>

    <div class="container my-5">
        <h1 class="mb-4" style='text-align:center;'>Profile,<b>
                <?php echo htmlspecialchars($row["nama"]); ?>
            </b></h1>

        <?php

        if (isset($_GET['success'])) {
            ?>
            <div class="alert alert-success" role="alert">
                Data berhasil disimpan
            </div>
            <?php
        }

        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $row['id']); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $row["username"]; ?>"
                    class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback">
                    <?php echo $username_err; ?>
                </span>
            </div>
            <!-- Nama -->
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama"
                    class="form-control <?php echo (!empty($nama_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $row["nama"]; ?>">
                <span class="invalid-feedback">
                    <?php echo $nama_err; ?>
                </span>
            </div>
            <!-- Alamat -->
            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat"
                    class="form-control <?php echo (!empty($alamat_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $row['alamat']; ?>">
                <span class="invalid-feedback">
                    <?php echo $alamat_err; ?>
                </span>
            </div>
            <!-- Jenis Kelamin -->
            <!-- Drop-down jenis kelamin di profile hanya 2 options -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Jenis Kelamin</label>
                </div>

                <select class="custom-select" id="inputGroupSelect01" name="jenis_kelamin">
                    <option selected>
                        <?php echo $row["jenis_kelamin"] ?>
                    </option>
                    <?php

                    if ($row["jenis_kelamin"] == "Laki-laki") {
                        ?>

                        <option value="Perempuan">Perempuan</option>

                        <?php
                    } else {
                        ?>

                        <option value="Laki-laki">Laki-laki</option>

                        <?php
                    }

                    ?>
                </select>
            </div>
            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir"
                    class="form-control <?php echo (!empty($tanggal_lahir_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $row['tanggal_lahir']; ?>">
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
                    value="<?php echo $row['nomor_handphone']; ?>">
                <span class="invalid-feedback">
                    <?php echo $nomor_handphone_err; ?>
                </span>
            </div>

            <?php
            if ($row['user'] != "Dokter" && $row['user'] != "Active") {
                ?>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">User</label>
                    </div>

                    <select class="custom-select" id="inputGroupSelect01" name="user">
                        <option selected value="<?php echo $row['user'] ?>">
                            <?php echo $row['user']; ?>
                        </option>
                        <?php if ($row['user'] == "Petugas Pendaftaran") {
                            ?>

                            <option value="Apoteker">Apoteker</option>
                            <option value="Admin">Admin</option>
                            <?php
                        } else if ($row['user'] == "Apoteker") {
                            ?>
                                <option value="Petugas Pendaftaran">Petugas Pendaftaran</option>

                                <option value="Admin">Admin</option>
                            <?php
                        } else if ($row['user'] == "Admin") {
                            ?>
                                    <option value="Petugas Pendaftaran">Petugas Pendaftaran</option>

                                    <option value="Apoteker">Apoteker</option>
                            <?php
                        } ?>
                    </select>



                </div>
                <?php
            } else {

                ?>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">User</label>
                    </div>

                    <select class="custom-select" id="inputGroupSelect01" name="user">
                        <option selected value="
                        <?php if ($row['user'] == 'Active') {
                            echo 'Active';
                        } else {
                            echo 'Dokter';
                        } ?>">
                            <?php echo "Dokter"; ?>
                        </option>
                    </select>

                </div>
                <?php
            }

            ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <input type="submit" class="btn btn-primary" value="Update">
            <a href="reset_password.php?id=<?php echo $id ?>" class='btn btn-danger'>Reset Password</a>
            <a href="data_praktik.php" class="btn btn-danger">Back</a>

        </form>
    </div>
</body>

</html>