<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

require_once "config.php";

$date = date("Y-m-d");
$antrian = 1;

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

if (!empty($pendaftaran)) {
    foreach ($pendaftaran as $value) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['done=' . $value])) {

                $sql = "UPDATE pendaftaran SET status = 'Selesai' WHERE id ='$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);
                    } else {
                        echo "Error";
                    }
                    mysqli_stmt_close($stmt);
                }

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Apoteker</title>
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

    include "nav.php";

    ?>

    <div class="container my-5">
        <h1 style='text-align:center;' class="mb-4">Antrian Pengambilan Obat
            <?php echo $date; ?>
        </h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <table class="table table-striped table-bordered" id="example">
                <thead>
                    <tr>
                        <th scope="col">Antrian</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">Tanggal Lahir</th>
                        <th scope="col">Nomor Handphone</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php

                    $sql = "SELECT pendaftaran.id, nama, alamat, jenis_kelamin, tanggal_lahir, nomor_handphone,status FROM pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND tanggal = '$date' AND (status = 'Menunggu Obat' OR status = 'Selesai') ";

                    if ($stmt = mysqli_prepare($link, $sql)) {

                        if (mysqli_stmt_execute($stmt)) {

                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {

                                $pendaftaran = $row['id'];

                                echo "<tr>";
                                echo "<td>" . $antrian++ . "</td>";
                                echo "<td>" . $row['nama'] . "</td>";
                                echo "<td>" . $row['alamat'] . "</td>";
                                echo "<td>" . $row['jenis_kelamin'] . "</td>";
                                echo "<td>" . $row['tanggal_lahir'] . "</td>";
                                echo "<td>" . $row['nomor_handphone'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>"; ?>

                                <a href="resep.php?pendaftaran_id=<?php echo $pendaftaran ?>" class='btn btn-primary'>Transaksi</a>
                                <input type="submit" name='done=<?php echo $row['id'] ?>' value="Done" class="btn btn-success">

                                <?php


                                echo "</tr>";

                            }

                        }

                    }

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                        if (isset($_POST[$value])) {

                            $sql = "UPDATE resep SET created_at = now() WHERE rekam_medis_id = '$value'";

                            if ($stmt = mysqli_prepare($link, $sql)) {

                                if (mysqli_stmt_execute($stmt)) {

                                    mysqli_stmt_store_result($stmt);

                                    header("location: apoteker.php");
                                    exit();

                                } else {
                                    echo "ERROR";
                                }

                                mysqli_stmt_close($stmt);

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