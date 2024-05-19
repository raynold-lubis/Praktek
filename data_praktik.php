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

$antrian = 0;


$sql = "SELECT * FROM users WHERE user = 'Active'";

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    } else {

        header("location: error.php");
        exit();

    }

    mysqli_stmt_close($stmt);

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $dokter = $_POST['dokter'];

    $sql = "UPDATE users SET user = 'Active' WHERE id='$dokter'";

    if ($stmt = mysqli_prepare($link, $sql)) {

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            header("location: data_praktik.php");
        } else {
            echo "error";
        }

    }

    $sql = "UPDATE users SET user = 'Dokter' WHERE NOT id ='$dokter' AND user='Active'";

    if ($stmt = mysqli_prepare($link, $sql)) {

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            header("location: data_praktik.php");
            exit();
        } else {
            echo "error";
        }

    }
    mysqli_stmt_close($stmt);

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

        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                            data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <?php echo "Dokter ";
                            echo
                                $row['nama'];
                            echo " Bertugas" ?>
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">

                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Nama :
                                <?php echo $row['nama'] ?>
                            </li>
                            <li class="list-group-item">Alamat :
                                <?php echo $row['alamat'] ?>
                            </li>
                            <li class="list-group-item">Jenis Kelamin :
                                <?php echo $row['jenis_kelamin'] ?>
                            </li>
                            <li class="list-group-item">Tanggal Lahir :
                                <?php echo $row['tanggal_lahir'] ?>
                            </li>
                            <li class="list-group-item">Nomor Handphone :
                                <?php echo $row['nomor_handphone'] ?>
                            </li>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method='post'>
                                <li class='list-group-item'>
                                    <p>Ganti dokter yang bertugas</p>
                                    <div class='input-group mb-3'>
                                        <select name="dokter" id="inputGroupSelect01" class='custom-select'>
                                            <?php

                                            $sql = "SELECT * FROM users WHERE user='Dokter'";

                                            if ($stmt = mysqli_prepare($link, $sql)) {

                                                // Attempt to execute the prepared statement
                                                if (mysqli_stmt_execute($stmt)) {

                                                    $result = mysqli_stmt_get_result($stmt);

                                                    while ($row = mysqli_fetch_array($result)) {
                                                        ?>

                                                        <option value="<?php echo $row['id'] ?>">
                                                            <?php echo $row['nama'] ?>
                                                        </option>

                                                        <?php

                                                    }

                                                }
                                            }

                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <input type="submit" class='btn btn-primary' value='Submit'>
                                        </div>
                                    </div>

                                </li>




                            </form>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <h1 class='my-4' style='text-align: center;'>Daftar User</h1>

        <table class="table table-striped table-bordered" id='example'>
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope='col'>Nama</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Jenis Kelamin</th>
                    <th scope="col">Tanggal Lahir</th>
                    <th scope="col">Nomor Handphone</th>
                    <th scope="col">User</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Prepare a select statement
                $sql = "SELECT * FROM users";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        $result = mysqli_stmt_get_result($stmt);

                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["nama"] . "</td>";
                            echo "<td>" . $row['alamat'] . "</td>";
                            echo "<td>" . $row['jenis_kelamin'] . "</td>";
                            echo "<td>" . $row['tanggal_lahir'] . "</td>";
                            echo "<td>" . $row['nomor_handphone'] . "</td>";
                            if ($row['user'] == 'Active') {
                                echo "<td>Dokter</td>";
                            } else {
                                echo "<td>" . $row['user'] . "</td>";
                            }
                            echo "<td>";

                            // Edit User
                            echo '<a href="edit_user.php?id=' . $row['id'] . '" class="btn btn-primary mr-3" title="View Record" data-toggle="tooltip">Edit</a>';

                            // Delete User
                            echo '<a href="delete_user.php?id=' . $row['id'] . '" class="btn btn-danger mr-3" title="Delete Record" data-toggle="tooltip">Delete</a>';

                            echo "</td>";
                            echo "</tr>";


                        }

                    }
                }



                ?>

            </tbody>
        </table>


        <a class="btn btn-primary" href="register.php">Tambah User</a>

    </div>

    <script>
        new DataTable('#example');
    </script>
</body>

</html>