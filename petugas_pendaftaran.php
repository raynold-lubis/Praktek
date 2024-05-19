<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if ($_SESSION["loggedin"] != true) {
    header("location: /praktik");
    exit;
}

// Include config file
require_once "config.php";

$date = date("Y-m-d");
$tahun = date('Y');


$antrian = 1;

$sql = "SELECT * FROM pendaftaran";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_array($result)) {

        if ($row['status'] == 'Sedang dilayani') {
            $masuk[] = $row['id'];
        } else if ($row['status'] == 'Menunggu') {
            $antri[] = $row['id'];
        }


    }

    mysqli_stmt_close($stmt);

}

if (!empty($masuk)) {
    foreach ($masuk as $value) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['cancel=' . $value])) {

                $sql = "UPDATE pendaftaran SET status='Menunggu' WHERE id='$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        // Store result
                        mysqli_stmt_store_result($stmt);
                        header('location: petugas_pendaftaran.php');

                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);

                }

            }
        }
    }
}
if (!empty($antri)) {
    foreach ($antri as $value) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['masuk=' . $value])) {

                // Prepare an update statement
                $sql = "UPDATE pendaftaran SET status = 'Sedang dilayani' WHERE id = '$value' AND tanggal = '$date'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        // Store result
                        mysqli_stmt_store_result($stmt);

                        // Redirect to petugas page
                        header("location: petugas_pendaftaran.php");
                        exit();

                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    //Close statement
                    mysqli_stmt_close($stmt);
                }
            } else if (isset($_POST['delete=' . $value])) {

                // Prepare an delete statement
                $sql = "DELETE FROM pendaftaran WHERE id = '$value' AND tanggal = '$date' AND status = 'Menunggu'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        // Records deleted successfully
                        header("location: petugas_pendaftaran.php");
                        exit();
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                }

                //Close connection
                mysqli_close($link);

            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Petugas Pendaftaran</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>

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

        <?php

        if (!empty($masuk)) {
            foreach ($masuk as $value) {

                // Prepare a select statement for show the profile
                $sql = "SELECT pendaftaran.id, nama, alamat, jenis_kelamin, tanggal_lahir, nomor_handphone FROM pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND pendaftaran.id = '$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) == 1) {

                        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    } else {

                        // URL doesn't contain valid id parameter. Redirect to error page
                        header("location: error.php");
                        exit();

                    }

                    mysqli_stmt_close($stmt);

                }

                ?>
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                    data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <?php echo "Pasien ";
                                    echo
                                        $row['nama'];
                                    echo " masuk" ?>
                                </button>
                            </h2>
                        </div>

                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">

                            <div class="card">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Nama :
                                        <?php echo $row['nama'] ?>
                                    </li>
                                    <li class="list-group-item">Umur :
                                        <?php echo $tahun - date('Y', strtotime($row['tanggal_lahir'])) ?>
                                    </li>
                                    <li class="list-group-item">Alamat :
                                        <?php echo $row['alamat'] ?>
                                    </li>
                                    <li class="list-group-item">Jenis Kelamin :
                                        <?php echo $row['jenis_kelamin'] ?>
                                    </li>
                                    <li class="list-group-item">Nomor Handphone :
                                        <?php echo $row['nomor_handphone'] ?>
                                    </li>
                                    <form action="petugas_pendaftaran.php" method="post">
                                        <li class="list-group-item">
                                            <input type="submit" class='btn btn-danger' name='cancel=<?php echo $row['id'] ?>'
                                                value='Cancel'>
                                        </li>
                                    </form>

                                </ul>
                            </div>

                        </div>
                    </div>
                </div>

                <?php

            }
        }

        ?>


        <h1 style='text-align: center;' class='my-4'>Antrian Pasien
            <?php echo date('d F Y'); ?>
        </h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <table class="table table-striped table-bordered" id="example">
                <thead>
                    <tr>
                        <th scope="col">Antrian</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Umur</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">Nomor Handphone</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Prepare a select statement
                    $sql = "SELECT pendaftaran.id, nama, alamat, jenis_kelamin, tanggal_lahir, nomor_handphone, status FROM pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND tanggal = ? AND status <> 'Sedang dilayani' ORDER BY pendaftaran.created_at";

                    if ($stmt = mysqli_prepare($link, $sql)) {

                        // Bind variable to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "s", $date);

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {

                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $antrian++ . "</td>";
                                echo "<td>" . $row['nama'] . "</td>";
                                echo "<td>" . $tahun - date('Y', strtotime($row['tanggal_lahir'])) . "</td>";
                                echo "<td>" . $row['alamat'] . "</td>";
                                echo "<td>" . $row['jenis_kelamin'] . "</td>";
                                echo "<td>" . $row['nomor_handphone'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>"; ?>
                                <?php

                                if ($row['status'] != 'Selesai' && $row['status'] != 'Menunggu Obat') {
                                    ?>
                                    <input type='submit' name='masuk=<?php echo $row["id"] ?>' class='btn btn-primary' value='Panggil'>
                                    <input type='submit' name='delete=<?php echo $row['id'] ?>' class='btn btn-danger' value='Delete'>
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