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

$obat = $terapi = $jumlah = "";
$obat_err = $terapi_err = $jumlah_err = "";

$pendaftaran = $_GET['pendaftaran_id'];

if (isset($_GET['resep_id'])) {

    $btn = 'update';

    $resep_id = $_GET['resep_id'];
    $sql = "SELECT * FROM resep WHERE id = '$resep_id'";

    if ($stmt = mysqli_prepare($link, $sql)) {

        if (mysqli_stmt_execute($stmt)) {

            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {

                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $obat = $row['obat'];
                $jumlah = $row['jumlah'];
                $terapi = $row['terapi'];

            }

        } else {
            echo "ERROR";
        }

    }

} else {
    $btn = 'insert';
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Validate obat
    if (empty(trim($_POST['obat']))) {
        $obat_err = "Please enter obat.";
    } else {
        $obat = trim($_POST['obat']);
    }

    // Validate jumlah
    if (empty(trim($_POST['jumlah']))) {
        $jumlah_err = "Please enter jumlah.";
    } else {
        $jumlah = trim($_POST['jumlah']);
    }

    // Validate terapi
    if (empty(trim($_POST['terapi']))) {
        $terapi_err = "Please enter terapi.";
    } else {
        $terapi = trim($_POST['terapi']);
    }

    if (empty($obat_err) && empty($jumlah_err) && empty($terapi_err)) {

        if ($btn == 'insert') {
            $sql = "INSERT INTO resep (pendaftaran_id, obat, jumlah, terapi) VALUES ('$pendaftaran',?,?,?)";
        } else if ($btn == 'update') {
            $sql = "UPDATE resep SET obat=?, jumlah=?,terapi=? WHERE id='$resep_id'";
        }

        // Prepare an insert element

        if ($stmt = mysqli_prepare($link, $sql)) {

            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sis", $param_obat, $param_jumlah, $param_terapi);

            // Set parameters
            $param_obat = $obat;
            $param_jumlah = $jumlah;
            $param_terapi = $terapi;


            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to resep page
                if ($btn == 'insert') {
                    header('location: rekam_medis.php?pendaftaran_id=' . $pendaftaran);
                } else {
                    header('location: tambah_obat.php?pendaftaran_id=' . $pendaftaran . '&resep_id=' . $resep_id . '&success');
                }

            } else {
                echo "ERROR";
            }

        }

        // Close statement
        mysqli_stmt_close($stmt);

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dokter</title>
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
        <h1 style='text-align:center;'>Data Obat</h1>

        <?php

        if (isset($_GET['success'])) {
            ?>
            <div class="alert alert-success" role="alert">
                Data berhasil disimpan
            </div>
            <?php
        }

        ?>

        <?php if ($btn == 'insert') {
            ?>
            <form action="tambah_obat.php?pendaftaran_id=<?php echo $pendaftaran ?>" method="post">
                <?php
        } else if ($btn == 'update') {

            ?>
                    <form action="tambah_obat.php?pendaftaran_id=<?php echo $pendaftaran ?>&resep_id=<?php echo $resep_id ?>"
                        method="post">
                    <?php

        } ?>


                <!-- Obat -->
                <div class="form-group">
                    <label>Obat</label>
                    <input type="text" name="obat"
                        class="form-control <?php echo (!empty($obat_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $obat; ?>">
                    <span class="invalid-feedback">
                        <?php echo $obat_err; ?>
                    </span>
                </div>

                <!-- Jumlah -->
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" min=1 name="jumlah"
                        class="form-control <?php echo (!empty($jumlah_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $jumlah; ?>">
                    <span class="invalid-feedback">
                        <?php echo $jumlah_err; ?>
                    </span>
                </div>

                <!-- Terapi -->
                <div class="form-group">
                    <label>Terapi</label>
                    <input type="text" name="terapi"
                        class="form-control <?php echo (!empty($terapi_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $terapi; ?>" placeholder="contoh : 3 dd 1 tab pc">
                    <span class="invalid-feedback">
                        <?php echo $terapi_err; ?>
                    </span>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <a href="rekam_medis.php?pendaftaran_id=<?php echo $pendaftaran ?>" class='btn btn-danger'>Back</a>
                </div>

            </form>

    </div>

</body>

</html>