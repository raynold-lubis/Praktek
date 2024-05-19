<?php

// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";

$count = 1;
$tahun = date('Y');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $start = new DateTime($_POST['before']);
    $end = new DateTime($_POST['after']);
    $end->modify('+1day');
    $interval = new DateInterval("P1D");
    $range = new DatePeriod($start, $interval, $end);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin</title>
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
        <h1 style='text-align: center;'>Laporan Pasien</h1>
        <p style='text-align:center;' class='mb-4'>Laporan jumlah pasien berdasarkan rentang tanggal kedatangan pasien.
        </p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    <input type="date" name="before" class="form-control">
                </div>
                <h1>-</h1>
                <div class="input-group col">
                    <input type="date" name="after" class="form-control">
                </div>
                <div class="input-group col">
                    <input type="submit" value="Submit" class="btn btn-primary">
                </div>
            </div>


        </form>


        <table class="table table-striped table-bordered" id="example">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Tanggal Kedatangan</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Jenis Kelamin</th>
                    <th scope="col">Umur</th>
                    <th scope="col">Nomor Handphone</th>
                    <th scope="col">Keluhan</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if (!empty($range)) {
                    foreach ($range as $date) {

                        $list = $date->format("Y-m-d");

                        // Prepare a select statement untuk mendapatkan user buat print resep
                        $sql = "SELECT * FROM pendaftaran WHERE status='Selesai'";

                        if ($stmt = mysqli_prepare($link, $sql)) {

                            // Attempt to execute the prepared statement
                            if (mysqli_stmt_execute($stmt)) {

                                $result = mysqli_stmt_get_result($stmt);

                                while ($row = mysqli_fetch_array($result)) {

                                    if ($list == $row['tanggal']) {
                                        $id[] = $row['id'];
                                    }
                                }
                            }

                        }



                    }
                }

                if (!empty($id)) {
                    foreach ($id as $value) {

                        // Prepare a select statement
                        $sql = "SELECT * FROM pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND pendaftaran.id = '$value' ";

                        if ($stmt = mysqli_prepare($link, $sql)) {

                            // Attempt to execute the prepared statement
                            if (mysqli_stmt_execute($stmt)) {

                                $result = mysqli_stmt_get_result($stmt);

                                if (mysqli_num_rows($result) == 1) {
                                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                    $tanggal_kedatangan = $row['tanggal'];
                                    $nama = $row['nama'];
                                    $alamat = $row['alamat'];
                                    $jenis_kelamin = $row['jenis_kelamin'];
                                    $umur = $tahun - date('Y', strtotime($row['tanggal_lahir']));
                                    $nomor_handphone = $row['nomor_handphone'];
                                    $keluhan = $row['keluhan'];


                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $count++ ?>
                                        <td>
                                            <?php echo $tanggal_kedatangan ?>
                                        </td>
                                        </td>
                                        <td>
                                            <?php echo $nama ?>
                                        </td>
                                        <td>
                                            <?php echo $alamat ?>
                                        </td>
                                        <td>
                                            <?php echo $jenis_kelamin ?>
                                        </td>
                                        <td>
                                            <?php echo $umur ?>
                                        </td>
                                        <td>
                                            <?php echo $nomor_handphone ?>
                                        </td>
                                        <td>
                                            <?php echo $keluhan ?>
                                        </td>
                                    </tr>
                                    <?php

                                }

                            } else {
                                echo "Error";
                            }

                        }
                    }
                }



                ?>
            </tbody>
        </table>

    </div>

    <script>
        new DataTable('#example');
    </script>

</body>

</html>