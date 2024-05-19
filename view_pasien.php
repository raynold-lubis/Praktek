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
$date = date("Y-m-d");
$tahun = date('Y');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dokter</title>
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

        <h1 style='text-align: center;'>History Pasien</h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">

            <table class="table table-striped table-bordered" id='example'>
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jam</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Umur</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">Nomor Handphone</th>
                        <th scope="col">Keluhan</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Prepare a select statement
                    $sql = "SELECT pendaftaran.id, tanggal, nama, alamat, jenis_kelamin, tanggal_lahir, nomor_handphone, keluhan, rekam_medis.created_at FROM rekam_medis INNER JOIN pendaftaran INNER JOIN pasien ON rekam_medis.pendaftaran_id = pendaftaran.id AND pendaftaran.pasien_id = pasien.id AND status='Selesai'";


                    if ($stmt = mysqli_prepare($link, $sql)) {

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {

                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . date('d F Y', strtotime($row["tanggal"])) . "</td>";
                                echo "<td>" . date('H:i:s', strtotime($row["created_at"])) . "</td>";
                                echo "<td>" . $row['nama'] . "</td>";
                                echo "<td>" . $tahun - date('Y', strtotime($row['tanggal_lahir'])) . "</td>";
                                echo "<td>" . $row['alamat'] . "</td>";
                                echo "<td>" . $row['jenis_kelamin'] . "</td>";

                                echo "<td>" . $row['nomor_handphone'] . "</td>";
                                echo "<td>" . $row['keluhan'] . "</td>";
                                echo "<td>"; ?>

                                <?php

                                if ($_SESSION['user'] == 'Dokter' || $_SESSION['user'] == 'Active') {

                                    ?>
                                    <a href="rekam_medis.php?pendaftaran_id=<?php echo $row['id'] ?>" class="btn btn-primary">Edit</a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="resep.php?pendaftaran_id=<?php echo $row['id'] ?>" class="btn btn-primary">Edit</a>
                                    <?php
                                }

                                ?>


                                <?php

                                "</td>";


                                echo "</tr>";

                            }

                        }
                    }



                    ?>

                </tbody>
            </table>
        </form>

    </div>

</body>

<script>
    new DataTable('#example');
</script>

</html>