<?php

// initialize session
session_start();

// cek apakah user sudah login, jika belum arahkan ke halaman login 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

// include config file
require_once "config.php";

$obat = $terapi = $rekam_medis_id = $jumlah = $nama = $umur = $alamat = "";
$beli = $harga = $total = $total2 = 0;

$tgl = date('d-m-Y H:i:s');
$tahun = date('Y');

$status = '';

$pendaftaran = $_GET['pendaftaran_id'];

function integerToRoman($integer)
{
    // Convert the integer into an integer (just to make sure)
    $integer = intval($integer);
    $result = '';

    // Create a lookup array that contains all of the Roman numerals.
    $lookup = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1
    );

    foreach ($lookup as $roman => $value) {
        // Determine the number of matches
        $matches = intval($integer / $value);

        // Add the same number of characters to the string
        $result .= str_repeat($roman, $matches);

        // Set the integer to be the remainder of the integer and the value
        $integer = $integer % $value;
    }

    // The Roman numeral should be built, return it
    return $result;
}

// Prepare a select statement untuk menampilkan resep
$sql = "SELECT resep.id FROM resep INNER JOIN pendaftaran ON resep.pendaftaran_id=pendaftaran.id AND pendaftaran_id = '$pendaftaran'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {
            $resep_id[] = $row['id'];
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}

// Select statement untuk mengambil nama pasien
$sql = "SELECT * FROM resep INNER JOIN pendaftaran INNER JOIN pasien ON resep.pendaftaran_id = pendaftaran.id AND pendaftaran.pasien_id=pasien.id AND pendaftaran_id='$pendaftaran'";

if ($stmt = mysqli_prepare($link, $sql)) {

    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_array($result);
            $status = $row['status'];

        }
    }
}

if (!empty($resep_id)) {
    foreach ($resep_id as $value) {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            if (isset($_POST['submit'])) {
                $beli = $_POST['beli=' . $value];
                $harga = $_POST['harga=' . $value];
                $total = $beli * $harga;

                $sql = "UPDATE resep SET dibeli = '$beli', harga='$harga', total='$total' WHERE id = '$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);
                    } else {
                        echo "ERROR";
                    }

                    mysqli_stmt_close($stmt);

                }
            } else if (isset($_POST['print'])) {
                echo '<script>window.print()</script>';
            }


        }

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Resep</title>
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

        #print {
            display: none;
        }

        @media print {
            #noprint {
                display: none;
            }

            #print {
                display: block;
            }

            body {
                font-size: x-large;
            }

        }
    </style>
</head>

<body>
    <?php

    include "nav.php"

        ?>

    <div class="container my-5" id='noprint'>
        <h1 class="mb-4" style="text-align:center;">Transaksi,<b>
                <?php echo $row['nama'] ?>
            </b></h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?pendaftaran_id=' . $pendaftaran); ?>"
            method="post">

            <table class="table table-striped table-bordered" id="example">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Terapi</th>
                        <th scope="col">Beli</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sql = "SELECT resep.id, obat, jumlah, terapi, dibeli, harga,total FROM resep INNER JOIN pendaftaran ON resep.pendaftaran_id = pendaftaran.id AND pendaftaran_id = '$pendaftaran'";

                    if ($stmt = mysqli_prepare($link, $sql)) {

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Store result
                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_array($result)) {
                                $beli = $row['dibeli'];
                                $harga = $row['harga'];
                                $total = $row['total'];
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['obat'] ?>
                                    </td>
                                    <td>

                                        <?php if ($row['obat'] != 'Konsultasi') {
                                            echo $row['jumlah'];
                                        } ?>
                                    </td>
                                    <td>
                                        <?php echo $row['terapi'] ?>
                                    </td>
                                    <td>
                                        <?php

                                        if (($row['obat'] != 'Konsultasi')) {

                                            ?>
                                            <input class='form-control' type="number" name='beli=<?php echo $row['id'] ?>'
                                                max="<?php echo $row['jumlah'] ?>" min="0" value="<?php echo $beli ?>">
                                            <?php

                                        } else {
                                            ?>
                                            <input hidden class='form-control' type="number" name='beli=<?php echo $row['id'] ?>'
                                                value="1">
                                            <?php
                                        }

                                        ?>

                                    </td>
                                    <td>
                                        <input class='form-control' type="number" name='harga=<?php echo $row['id'] ?>' min="0"
                                            value="<?php echo $harga ?>">
                                    </td>
                                    <td>
                                        <?php echo $total ?>
                                    </td>

                                </tr>
                                <?php
                            }
                        }
                    }

                    ?>
                </tbody>
            </table>
            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
            <input type="submit" name="print" class="btn btn-primary" value="Print">
            <?php if ($status == 'Selesai') {
                ?>
                <a href="view_pasien.php" class="btn btn-danger">Back</a>
                <?php
            } else {
                ?>
                <a href="apoteker.php" class="btn btn-danger">Back</a>
                <?php
            } ?>


        </form>

    </div>

    <div id="print">

        <h2 class="my-5" style="text-align:center;"><u>Dr. Melda Simanjuntak</u>
            <br>
            SIP.503.3/0233/SIP.DOK/XII/2021
        </h2>

        <div class="row my-3">
            <div class="col float-left">
                Praktek : <br> Pukul 17.00 - 21.00 WIB <br> Jl. Seth Adji Kav. 02 Telp. (0536) 3236 078 <br>
                Palangkaraya
                73111
            </div>
            <div class="col float-right" style="text-align:right">
                Rumah : <br> Jl. Morist Ismail IV No. 38 <br> Palangkaraya 73111
            </div>
        </div>

        <p>Palangkaraya,
            <?php echo date("Y-m-d") ?>
        </p>

        <?php

        // Prepare a select statement untuk menampilkan resep
        $sql = "SELECT * FROM resep WHERE pendaftaran_id = '$pendaftaran'";

        if ($stmt = mysqli_prepare($link, $sql)) {

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {

                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_array($result)) {
                    $resep[] = $row['id'];
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        $sql = "SELECT pendaftaran.id, nama, alamat,tanggal_lahir FROM pendaftaran INNER JOIN pasien ON pendaftaran.pasien_id=pasien.id AND pendaftaran.id='$pendaftaran'";

        if ($stmt = mysqli_prepare($link, $sql)) {

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {

                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {

                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $nama = $row['nama'];
                    $alamat = $row['alamat'];
                    $tahun_lahir = date('Y', strtotime($row["tanggal_lahir"]));
                    $umur = $tahun - $tahun_lahir;

                }
            }

        }

        if (!empty($resep)) {
            foreach ($resep as $value) {
                $sql = "SELECT * FROM resep WHERE id = '$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {

                        $result = mysqli_stmt_get_result($stmt);

                        if (mysqli_num_rows($result) == 1) {

                            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                        }
                    }
                }

                ?>

                <p>
                    <?php
                    if ($row['dibeli'] != $row['jumlah']) {
                        echo "R/ " . $row['obat'] . " No.";
                        echo integerToRoman($row['jumlah'] - $row['dibeli']);
                        echo "<br> S";
                        echo $row['terapi'];
                    }
                    ?>
                </p>
                <?php

            }
        }

        ?>

        <p style='text-align:center; break-after:page;'>Pro :
            <?php echo $nama; ?><br> Umur :
            <?php echo $umur; ?><br> Alamat :
            <?php echo $alamat; ?> <br><br> <i>obat tidak boleh diganti tanpa seijin dokter</i>
        </p>

    </div>

    <?php

    include "nota.php";

    ?>


</body>

<script>
    new DataTable('#example');
</script>

</html>