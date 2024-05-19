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
$date = date("Y-m-d");

$users_id = $_GET['users_id'];

$sql = "SELECT * FROM pasien WHERE id = '$users_id'";

if ($stmt = mysqli_prepare($link, $sql)) {

    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_array($result);

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>History Pasien</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 50%;
            height: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    <?php

    include "nav.php"

        ?>
    <div class="wrapper">

        <h1 class='mb-4'>History, <b>
                <?php echo $row['nama'] ?>
            </b></h1>

        <!-- History -->
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Keluhan</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Prepare a select statement
                $sql = "SELECT * FROM rekam_medis WHERE users_id = ? AND status = 'Selesai' ";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Bind variable to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "i", $users_id);

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        $result = mysqli_stmt_get_result($stmt);

                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['tanggal_kedatangan'] . "</td>";
                            echo "<td>" . $row['keluhan'] . "</td>";
                            echo "<td>";

                            // Edit Rekam Medis
                            if ($_SESSION["user"] == 'dokter' || $_SESSION['user'] == 'active') {
                                echo '<a href="rekam_medis.php?rekam_medis_id=' . $row['id'] . '" class="btn btn-primary mr-3">Rekam Medis</a>';
                                echo '<a href="resep.php?rekam_medis_id=' . $row['id'] . '" class="btn btn-primary mr-3" >Resep</a>';
                            } elseif ($_SESSION['user'] == 'apoteker') {
                                echo '<a href="view_resep.php?rekam_medis_id=' . $row['id'] . '" class="btn btn-primary mr-3" >Resep</a>';
                            }



                            echo "</td>";
                            echo "</tr>";

                        }

                    }

                }

                ?>
            </tbody>
        </table>

        <a class='btn btn-danger' href="view_pasien.php">Back</a>

    </div>
</body>

</html>