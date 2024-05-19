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
$nama = $nik = $alamat = $jenis_kelamin = $tanggal_lahir = $nomor_handphone = "";
$nama_err = $nik_err = $alamat_err = $jenis_kelamin_err = $tanggal_lahir_err = $nomor_handphone_err = "";

$id = $_GET['id'];

$sql = "SELECT * FROM pasien WHERE id='$id'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        /* store result */
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            echo "ERROR";
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    //Validate nik
    if (empty(trim($_POST["nik"]))) {
        $nik_err = "Please enter NIK.";
    } else {
        $nik = trim($_POST["nik"]);
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

    // Check input errors before updating the database
    if (empty($nik_err) && empty($nama_err) && empty($alamat_err) && empty($jenis_kelamin_err) && empty($tanggal_lahir_err) && empty($nomor_handphone_err)) {

        // Prepare an update statement
        $sql = "UPDATE pasien SET nik=?,nama = ?, alamat = ?, jenis_kelamin = ?, tanggal_lahir = ?, nomor_handphone = ? WHERE id = '$id'";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isssss", $param_nik, $param_nama, $param_alamat, $param_jenis_kelamin, $param_tanggal_lahir, $param_nomor_handphone);

            // Set parameters
            $param_nik = $nik;
            $param_nama = $nama;
            $param_alamat = $alamat;
            $param_jenis_kelamin = $jenis_kelamin;
            $param_tanggal_lahir = $tanggal_lahir;
            $param_nomor_handphone = $nomor_handphone;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                header("location: edit_pasien.php?id=$id&success");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }



    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin</title>
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
        <h1 style='text-align: center;' class="mb-4">Profile,<b>
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

            <!-- NIK -->
            <div class="form-group">
                <label>NIK</label>
                <input type="number" name="nik"
                    class="form-control <?php echo (!empty($nik_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $row["nik"]; ?>">
                <span class="invalid-feedback">
                    <?php echo $nik_err; ?>
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
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <input type="submit" class="btn btn-primary" value="Update">
            <a href="admin.php" class="btn btn-danger">Back</a>

        </form>
    </div>
</body>

</html>