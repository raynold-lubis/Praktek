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
$date = date("Y-m-d");
$tahun = date('Y');

$nama = $id = $keluhan = "";

$sql = "SELECT * FROM pendaftaran";
if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $pendaftaran[] = $row['id'];
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

$sql = "SELECT * FROM rekam_medis";
if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $rekam_medis[] = $row['pendaftaran_id'];
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}


if (!empty($pendaftaran)) {
    foreach ($pendaftaran as $value) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['delete=' . $value])) {

                $sql = "UPDATE pendaftaran SET status ='Menunggu' WHERE id='$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);
                    } else {
                        echo "Error";
                    }
                    mysqli_stmt_close($stmt);
                }

            } //else if (isset($_POST['done=' . $value])) {

            //     $sql = "UPDATE pendaftaran SET status = 'Selesai' WHERE id ='$value'";

            //     if ($stmt = mysqli_prepare($link, $sql)) {
            //         if (mysqli_stmt_execute($stmt)) {
            //             mysqli_stmt_store_result($stmt);
            //         } else {
            //             echo "Error";
            //         }
            //         mysqli_stmt_close($stmt);
            //     }

            // }
        }
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

        <h1 style="text-align:center;">Daftar Pasien
            <?php echo date('d F Y') ?>
        </h1>
        <form action="dokter.php" method="post">

            <table class="table table-striped table-bordered" id='example'>
                <thead>
                    <tr>
                        <th scope="col">Nama Pasien</th>
                        <th scope="col">Umur</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">No Handphone</th>
                        <th scope="col">Keluhan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $sql = "SELECT pendaftaran.id, nama, alamat, jenis_kelamin, tanggal_lahir, nomor_handphone, keluhan, status FROM pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND status<>'Menunggu' AND tanggal='$date'";

                    if ($stmt = mysqli_prepare($link, $sql)) {

                        if (mysqli_stmt_execute($stmt)) {

                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {

                                echo "<tr>";
                                echo "<td>" . $row['nama'] . "</td>";
                                echo "<td>" . $tahun - date('Y', strtotime($row['tanggal_lahir'])) . "</td>";
                                echo "<td>" . $row['alamat'] . "</td>";
                                echo "<td>" . $row['jenis_kelamin'] . "</td>";
                                echo "<td>" . $row['nomor_handphone'] . "</td>";
                                echo "<td>" . $row['keluhan'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>"; ?>

                                <?php

                                if (in_array($row['id'], $rekam_medis)) {
                                    ?>
                                    <a href="rekam_medis.php?pendaftaran_id=<?php echo $row['id'] ?>" class="btn btn-primary">Edit</a>

                                    <!-- <input type="submit" name='done=<?php echo $row['id'] ?>' value="Done" class="btn btn-success"> -->
                                    <?php



                                } else {
                                    ?>
                                    <a href="rekam_medis.php?pendaftaran_id=<?php echo $row['id'] ?>" class="btn btn-primary">Edit</a>

                                    <input type="submit" name='delete=<?php echo $row['id'] ?>' value="Delete" class="btn btn-danger">
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