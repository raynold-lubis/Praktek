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

$nomor = 0;

$sql = "SELECT * FROM users WHERE user='dokter' or user='active'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $id[] = $row['id'];

        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $dokter = $_POST['dokter'];



    $sql = "UPDATE users SET user = 'active' WHERE id='$dokter'";

    if ($stmt = mysqli_prepare($link, $sql)) {

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            header("location: data_dokter.php");
        } else {
            echo "error";
        }

    }


    $sql = "UPDATE users SET user = 'dokter' WHERE NOT id ='$dokter' AND user='active'";

    if ($stmt = mysqli_prepare($link, $sql)) {

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            header("location: data_dokter.php");
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
        <h1>Data Dokter</h1>
        <form action="data_dokter.php" method="get">
            <div class="form-row align-items-center mb-3">
                <div class="input-group col">
                    <input type="text" name="search" class="form-control" placeholder="Search">
                </div>
                <div class="input-group col">
                    <input type="submit" class="btn btn-primary" value="Search">
                </div>

            </div>
        </form>

        <form action="edit_user.php" method="get">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['search'])) {
                        $search = $_GET["search"];
                        $sql = "SELECT * FROM users WHERE user = 'dokter' or user='active' AND nama like '%$search%' ";
                    } else {
                        // Prepare a select statement
                        $sql = "SELECT * FROM users WHERE user = 'dokter' or user='active'";
                    }

                    if ($stmt = mysqli_prepare($link, $sql)) {

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {

                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {
                                $nomor++;
                                echo "<tr>";
                                echo "<td>" . $nomor . "</td>";
                                echo "<td>" . $row['nama'] . "</td>";
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
        </form>
        <a class="btn btn-primary" href="register.php">Tambah User</a>

        <h5 class='my-4'>Pilih dokter yang melayani hari ini</h5>
        <form action="data_dokter.php" method="post">

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text " for="inputGroupSelect01">Dokter</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01" name="dokter">
                    <?php

                    foreach ($id as $data) {

                        $sql = "SELECT * FROM users WHERE id = '$data'";

                        if ($stmt = mysqli_prepare($link, $sql)) {

                            if (mysqli_stmt_execute($stmt)) {
                                $result = mysqli_stmt_get_result($stmt);

                                if (mysqli_num_rows($result) == 1) {
                                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                } else {
                                    header("location: error.php");
                                    exit();
                                }
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }

                        ?>

                        <?php
                        if ($row['user'] == "active") {
                            ?>
                            <option selected value="<?php echo $data ?>">
                                <?php echo $row['nama'] ?>
                            </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $data ?>">
                                <?php echo $row['nama'] ?>
                            </option>

                            <?php
                        }
                    }

                    ?>
                </select>

            </div>

            <input type="submit" class="btn btn-primary" value="Submit">

        </form>

    </div>
</body>

</html>