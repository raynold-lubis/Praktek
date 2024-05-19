<?php
// Initialize the session
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";

$antrian = 0;
$tahun = date('Y');

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
    include "nav.php";
    ?>

    <div class="container my-5">
        <h1 class='mb-4' style='text-align: center;'>Daftar Pasien</h1>

        <table class="table table-striped table-bordered" id='example'>
            <thead>
                <tr>
                    <th scope="col">Nomor Rekam Medis</th>
                    <th scope='col'>NIK</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Umur</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Jenis Kelamin</th>
                    <th scope="col">Nomor Handphone</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Prepare a select statement
                $sql = "SELECT * FROM pasien";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        $result = mysqli_stmt_get_result($stmt);

                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["nik"] . "</td>";
                            echo "<td>" . $row['nama'] . "</td>";
                            echo "<td>" . $tahun - date('Y', strtotime($row['tanggal_lahir'])) . "</td>";
                            echo "<td>" . $row['alamat'] . "</td>";
                            echo "<td>" . $row['jenis_kelamin'] . "</td>";
                            echo "<td>" . $row['nomor_handphone'] . "</td>";
                            echo "<td>";

                            // Edit User
                            echo '<a href="edit_pasien.php?id=' . $row['id'] . '" class="btn btn-primary">Edit</a>';

                            // Delete User
                            echo '<a href="delete_pasien.php?id=' . $row['id'] . '" class="btn btn-danger">Delete</a>';

                            echo "</td>";
                            echo "</tr>";


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