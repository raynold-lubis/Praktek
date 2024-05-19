<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$alergi = 'Tidak Ada';
$kesadaran = 'Compos Mentis';
$status_pulang = "Berobat Jalan";
$anamnesa = $diagnosa = $suhu = $tinggi_badan = $berat_badan = $lingkar_perut = $respiratory_rate = $sistole = $diastole = $heart_rate = "";
$anamnesa_err = $diagnosa = $tinggi_badan_err = $berat_badan_err = $lingkar_perut_err = $suhu_err = $respiratory_rate_err = $sistole_err = $diastole_err = $heart_rate_err = "";
$alergi_arr = array("Tidak Ada", "Seafood", "Gandum", "Susu Sapi", "Kacang-Kacangan", "Makanan Lain", "Panas", "Dingin", "Kotor", "Antibiotik", "Antiinflamasi", "Non Steroid", "Kortikosteroid", "Insulin", "Obat-Obatan Lain");
$kesadaran_arr = array("Compos Mentis", "Somnolence", "Sopor", "Coma");
$status_pulang_arr = array("Rujuk", "Meninggal", "Berobat Jalan");

//$rekam_medis_id = $_GET['rekam_medis_id'];
$pendaftaran = $_GET['pendaftaran_id'];

$obat = $jumlah = $konsultasi[] = '';

$status = '';

$sql = "SELECT * FROM rekam_medis INNER JOIN pendaftaran ON rekam_medis.pendaftaran_id=pendaftaran.id";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $arr[] = $row['pendaftaran_id'];
            $pasien[] = $row['pasien_id'];
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

$sql = "SELECT * FROM resep WHERE pendaftaran_id = '$pendaftaran'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $resep[] = $row['id'];
            $konsultasi[] = $row['obat'];
        }
    } else {
        echo "Error";
    }

}

if (in_array($pendaftaran, $arr)) {
    // Prepare a select statement
    $btn = "update";

    $sql = "SELECT * FROM rekam_medis INNER JOIN pendaftaran INNER JOIN pasien ON rekam_medis.pendaftaran_id=pendaftaran.id AND pendaftaran.pasien_id=pasien.id AND pendaftaran.id = '$pendaftaran' ";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $anamnesa = $row['anamnesa'];
                $diagnosa = $row['diagnosa'];
                $suhu = $row['suhu'];
                $tinggi_badan = $row['tinggi_badan'];
                $berat_badan = $row['berat_badan'];
                $lingkar_perut = $row['lingkar_perut'];
                $respiratory_rate = $row['respiratory_rate'];
                $sistole = $row['sistole'];
                $diastole = $row['diastole'];
                $heart_rate = $row['heart_rate'];
                $alergi = $row['alergi'];
                $kesadaran = $row['kesadaran'];
                $status_pulang = $row['status_pulang'];

                $status = $row['status'];

            } else {
                // URL doesn't contain valid id parameter. Redirect to error page
                echo "error";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
} else {

    $btn = 'insert';

    $sql = "SELECT pendaftaran.id,nama, tanggal, pasien_id FROM  pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND pendaftaran.id='$pendaftaran'";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $pasien_id = $row['pasien_id'];


            } else {
                // URL doesn't contain valid id parameter. Redirect to error page
                echo "error";
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    if (in_array($pasien_id, $pasien)) {

        $sql = "SELECT pendaftaran.id, nama, tanggal, pasien_id, tinggi_badan, lingkar_perut FROM rekam_medis INNER JOIN pendaftaran INNER JOIN pasien ON rekam_medis.pendaftaran_id=pendaftaran.id AND pendaftaran.pasien_id = pasien.id AND pasien_id = '$pasien_id' LIMIT 1";

        if ($stmt = mysqli_prepare($link, $sql)) {

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $tinggi_badan = $row['tinggi_badan'];
                    $lingkar_perut = $row['lingkar_perut'];

                } else {
                    // URL doesn't contain valid id parameter. Redirect to error page
                    echo "error";
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

    }

}


// Processing form data when form is submitted
if (!empty($resep)) {
    foreach ($resep as $value) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST['delete=' . $value])) {
                $sql = "DELETE FROM resep WHERE id = '$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        // Records deleted successfully
                        header("location: rekam_medis.php?pendaftaran_id=" . $pendaftaran);
                        exit();
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                }
            }

        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    //Validate anamnesa
    if (empty(trim($_POST["anamnesa"]))) {
        $anamnesa_err = "Please enter anamnesa.";
    } else {
        $anamnesa = trim($_POST["anamnesa"]);
    }

    //Validate diagnosa
    if (empty(trim($_POST["diagnosa"]))) {
        $diagnosa_err = "Please enter diagnosa.";
    } else {
        $diagnosa = trim($_POST["diagnosa"]);
    }

    //Validate alergi
    if (empty(trim($_POST["alergi"]))) {
        $alergi_err = "Please enter alergi.";
    } else {
        $alergi = trim($_POST["alergi"]);
    }

    //Validate kesadaran
    if (empty(trim($_POST["kesadaran"]))) {
        $kesadaran_err = "Please enter kesadaran.";
    } else {
        $kesadaran = trim($_POST["kesadaran"]);
    }

    //Validate tinggi badan
    if (empty(trim($_POST["tinggi_badan"]))) {
        $tinggi_badan_err = "Please enter tinggi badan.";
    } else {
        $tinggi_badan = trim($_POST["tinggi_badan"]);
    }

    //Validate berat badan
    if (empty(trim($_POST["berat_badan"]))) {
        $berat_badan_err = "Please enter berat badan.";
    } else {
        $berat_badan = trim($_POST["berat_badan"]);
    }

    //Validate lingkar perut
    if (empty(trim($_POST["lingkar_perut"]))) {
        $lingkar_perut_err = "Please enter lingkar perut.";
    } else {
        $lingkar_perut = trim($_POST["lingkar_perut"]);
    }

    //Validate suhu
    if (empty(trim($_POST["suhu"]))) {
        $suhu_err = "Please enter suhu.";
    } else {
        $suhu = trim($_POST["suhu"]);
    }

    //Validate respiratory rate
    if (empty(trim($_POST["respiratory_rate"]))) {
        $respiratory_rate_err = "Please enter respiratory rate.";
    } else {
        $respiratory_rate = trim($_POST["respiratory_rate"]);
    }

    //Validate sistole
    if (empty(trim($_POST["sistole"]))) {
        $sistole_err = "Please enter sistole.";
    } else {
        $sistole = trim($_POST["sistole"]);
    }

    //Validate diastole
    if (empty(trim($_POST["diastole"]))) {
        $diastole_err = "Please enter diastole.";
    } else {
        $diastole = trim($_POST["diastole"]);
    }

    //Validate heart rate
    if (empty(trim($_POST["heart_rate"]))) {
        $heart_rate_err = "Please enter heart rate.";
    } else {
        $heart_rate = trim($_POST["heart_rate"]);
    }

    //Validate status pulang
    if (empty(trim($_POST["status_pulang"]))) {
        $status_pulang_err = "Please enter status pulang.";
    } else {
        $status_pulang = trim($_POST["status_pulang"]);
    }

    $obat = $_POST['obat'];
    $jumlah = $_POST['jumlah'];


    if (empty($anamnesa_err) && empty($diagnosa_err) && empty($suhu_err) && empty($tinggi_badan_err) && empty($berat_badan_err) && empty($lingkar_perut_err) && empty($sistole_err) && empty($diastole_err) && empty($respiratory_rate_err) && empty($heart_rate_err)) {


        if (!in_array($obat, $konsultasi)) {

            $sql = "INSERT INTO resep (pendaftaran_id, obat,jumlah) VALUES ('$pendaftaran','$obat','$jumlah')";
            if ($stmt = mysqli_prepare($link, $sql)) {

                // Attempt to execute the prepared statement
                mysqli_stmt_execute($stmt);

                // Close statement
                mysqli_stmt_close($stmt);

            }
        }

        if ($status != 'Selesai') {
            $sql = "UPDATE pendaftaran SET status ='Menunggu Obat' WHERE id='$pendaftaran'";
            if ($stmt = mysqli_prepare($link, $sql)) {
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);

                } else {
                    echo "Error";
                }
                mysqli_stmt_close($stmt);
            }
        }

        if ($btn == 'insert') {
            //Prepare an update element
            $sql = "INSERT INTO rekam_medis (pendaftaran_id,anamnesa, diagnosa, tinggi_badan, berat_badan, lingkar_perut, suhu, respiratory_rate, sistole, diastole, heart_rate, alergi, kesadaran, status_pulang) VALUES ('$pendaftaran',?,?,?,?,?,?,?,?,?,?,?,?,?)";
        } else if ($btn == "update") {
            $sql = "UPDATE rekam_medis SET anamnesa = ?, diagnosa = ?,tinggi_badan = ?, berat_badan = ?, lingkar_perut = ?, suhu = ?, respiratory_rate =?, sistole=?, diastole=?, heart_rate=?, alergi=?, kesadaran=?, status_pulang=? WHERE pendaftaran_id = '$pendaftaran'";
        }

        if ($stmt = mysqli_prepare($link, $sql)) {

            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssiiiiiiiisss", $param_anammesa, $param_diagnosa, $param_tinggi_badan, $param_berat_badan, $param_lingkar_perut, $param_suhu, $param_respiratory_rate, $param_sistole, $param_diastole, $param_heart_rate, $param_alergi, $param_kesadaran, $param_status_pulang);

            // Set parameters   
            $param_anammesa = $anamnesa;
            $param_diagnosa = $diagnosa;
            $param_alergi = $alergi;
            $param_kesadaran = $kesadaran;
            $param_tinggi_badan = $tinggi_badan;
            $param_berat_badan = $berat_badan;
            $param_lingkar_perut = $lingkar_perut;
            $param_suhu = $suhu;
            $param_respiratory_rate = $respiratory_rate;
            $param_sistole = $sistole;
            $param_diastole = $diastole;
            $param_heart_rate = $heart_rate;
            $param_status_pulang = $status_pulang;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to dokter page

                header("location: rekam_medis.php?pendaftaran_id=" . $pendaftaran . '&success');
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
    <title>Rekam Medis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js'></script>

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
        <h1 class='mb-4' style="text-align:center;">Rekam Medis,
            <b>
                <?php echo $row['nama'] ?>
            </b>
        </h1>

        <?php

        if (isset($_GET['success'])) {
            ?>
            <div class="alert alert-success" role="alert">
                Data berhasil disimpan
            </div>
            <?php
        }

        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?pendaftaran_id=' . $pendaftaran); ?>"
            method="post">

            <!-- Tanggal Kedatangan -->
            <div class="form-group">
                <label>Tanggal Kedatangan</label>
                <input type="text" disabled="true" name="tanggal_kedatangan" class="form-control"
                    value="<?php echo $row['tanggal'] ?>">
            </div>

            <!-- Anamnesa -->
            <div class="form-group">
                <label>Anamnesa</label>
                <textarea type="text" name="anamnesa"
                    class="form-control <?php echo (!empty($anamnesa_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $anamnesa ?>"><?php echo $anamnesa; ?></textarea>
                <span class="invalid-feedback">
                    <?php echo $anamnesa_err; ?>
                </span>
            </div>

            <!-- Diagnosa -->
            <div class="form-group">
                <label>Diagnosa</label>
                <textarea type="text" name="diagnosa"
                    class="form-control <?php echo (!empty($diagnosa_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $diagnosa ?>"><?php echo $diagnosa; ?></textarea>
                <span class="invalid-feedback">
                    <?php echo $diagnosa_err; ?>
                </span>
            </div>

            <!-- Alergi -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Alergi</label>

                </div>

                <select class="custom-select" id="inputGroupSelect01" name="alergi">
                    <option value="<?php $alergi; ?>" selected>
                        <?php echo $alergi ?>
                    </option>
                    <?php
                    foreach ($alergi_arr as $value) {
                        if ($alergi != $value) {
                            ?>

                            <option value="<?php echo $value ?>">
                                <?php echo $value ?>
                            </option>

                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Kesadaran -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect01">Kesadaran</label>
                </div>
                <select class="custom-select " id="inputGroupSelect01" name="kesadaran">
                    <option value="<?php $kesadaran; ?>" selected>
                        <?php echo $kesadaran ?>
                    </option>
                    <?php
                    foreach ($kesadaran_arr as $value) {
                        if ($kesadaran != $value) {
                            ?>

                            <option value="<?php echo $value ?>">
                                <?php echo $value ?>
                            </option>

                            <?php
                        }
                    }
                    ?>
                </select>

            </div>

            <!-- Suhu -->
            <label>Suhu</label>
            <div class="input-group mb-3">
                <input type="number" name="suhu"
                    class="form-control <?php echo (!empty($suhu_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $suhu; ?>">
                <div class="input-group-append">
                    <span class="input-group-text">
                        ℃
                    </span>
                </div>
                <span class="invalid-feedback">
                    <?php echo $suhu_err; ?>
                </span>
            </div>

            <!-- Pemeriksaan Fisik -->
            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    Tinggi Badan
                </div>
                <div class="input-group col">
                    Berat Badan
                </div>
                <div class="input-group col">
                    Lingkar Perut
                </div>
            </div>

            <div class="form-row align-items-center mb-3">
                <div class="input-group col ">
                    <input type="number" name="tinggi_badan"
                        class="form-control <?php echo (!empty($tinggi_badan_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $tinggi_badan; ?>" placeholder="Tinggi Badan">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            cm
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $tinggi_badan_err; ?>
                    </span>
                </div>

                <div class="input-group col">
                    <input type="number" name="berat_badan"
                        class="form-control <?php echo (!empty($berat_badan_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $berat_badan; ?>" placeholder="Berat Badan">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            kg
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $berat_badan_err; ?>
                    </span>
                </div>

                <div class="input-group col">
                    <input type="number" name="lingkar_perut"
                        class="form-control <?php echo (!empty($lingkar_perut_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $lingkar_perut; ?>" placeholder="Lingkar Perut">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            cm
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $lingkar_perut_err; ?>
                    </span>
                </div>
            </div>

            <!-- Tekanan Darah -->

            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    Sistole
                </div>
                <div class="input-group col">
                    Diastole
                </div>
            </div>

            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    <input type="number" name="sistole"
                        class="form-control <?php echo (!empty($sistole_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $sistole; ?>" placeholder="Sistole">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            mmHg
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $sistole_err; ?>
                    </span>
                </div>

                <div class="input-group col">
                    <input type="number" name="diastole"
                        class="form-control <?php echo (!empty($diastole_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $diastole; ?>" placeholder="Diastole">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            mmHg
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $diastole_err; ?>
                    </span>
                </div>
            </div>

            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    Respiratory Rate
                </div>
                <div class="input-group col">
                    Heart Rate
                </div>
            </div>

            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    <input type="number" name="respiratory_rate"
                        class="form-control <?php echo (!empty($respiratory_rate_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $respiratory_rate; ?>" placeholder="Respiratory Rate">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            /minute
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $respiratory_rate_err; ?>
                    </span>
                </div>

                <div class="input-group col">
                    <input type="number" name="heart_rate"
                        class="form-control <?php echo (!empty($heart_rate_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $heart_rate; ?>" placeholder="Heart Rate">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            bpm
                        </span>
                    </div>
                    <span class="invalid-feedback">
                        <?php echo $heart_rate_err; ?>
                    </span>
                </div>

            </div>

            <!-- Status Pulang -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text " for="inputGroupSelect01">Status Pulang</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01" name="status_pulang">
                    <option value="<?php $status_pulang; ?>" selected>
                        <?php echo $status_pulang ?>
                    </option>
                    <?php
                    foreach ($status_pulang_arr as $value) {
                        if ($status_pulang != $value) {
                            ?>

                            <option value="<?php echo $value ?>">
                                <?php echo $value ?>
                            </option>

                            <?php
                        }
                    }
                    ?>
                </select>

            </div>


            <h1 class="my-5" style="text-align: center;">Resep Obat,<b>
                    <?php echo $row['nama'] ?>
                </b></h1>

            <table class="table table-striped table-bordered" id='example'>
                <thead>
                    <tr>
                        <th scope="col">Nama Obat</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Terapi</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sql = "SELECT resep.id, obat, jumlah, terapi from resep INNER JOIN pendaftaran ON resep.pendaftaran_id = pendaftaran.id AND pendaftaran.id = $pendaftaran";

                    if ($stmt = mysqli_prepare($link, $sql)) {

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {

                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {
                                if ($row['obat'] != 'Konsultasi') {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $row['obat'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['jumlah'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['terapi'] ?>
                                        </td>
                                        <td>
                                            <a href="tambah_obat.php?pendaftaran_id=<?php echo $pendaftaran ?>&resep_id=<?php echo $row['id'] ?>"
                                                class="btn btn-primary">Edit
                                                Obat</a>

                                            <input type="submit" name='delete=<?php echo $row['id'] ?>' value="Delete"
                                                class="btn btn-danger">
                                        </td>
                                    </tr>
                                    <?php

                                }
                            }
                        }
                    }
                    ?>

                    <tr style="display:none;">
                        <td>
                            <input type="text" name='obat' value='Konsultasi'>
                        </td>
                        <td><input type="text" name='jumlah' value='1'></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <a href="tambah_obat.php?pendaftaran_id=<?php echo $pendaftaran ?>" class="btn btn-primary">Tambah
                Obat</a>
            <input type="submit" class="btn btn-primary" value="Submit">
            <?php

            if ($status == 'Selesai') {
                ?>

                <a href="view_pasien.php" class='btn btn-danger'>Back</a>

                <?php

            } else {
                ?>
                <a class="btn btn-danger" href='dokter.php'>Back</a>
                <?php

            }


            ?>


        </form>

    </div>
</body>

<script>
    new DataTable('#example');
</script>

</html>