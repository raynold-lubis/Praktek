<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /praktik");
    exit;
}

require_once "config.php";

$obat = $terapi = $rekam_medis_id = $jumlah = $nama = $umur = $harga = $alamat = $dibeli = "";
$total = $total2 = 0;

$tgl = date('d-m-Y H:i:s');

$tahun = date('Y');

$rekam_medis_id = $_GET['rekam_medis_id'];

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

// Prepare a select statement untuk mendapatkan user buat print resep
$sql = "SELECT * FROM rekam_medis INNER JOIN pasien ON rekam_medis.users_id = pasien.id AND rekam_medis.id = '$rekam_medis_id'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $nama = $row["nama"];
            $alamat = $row["alamat"];
            $tahun_lahir = date('Y', strtotime($row["tanggal_lahir"]));
            $umur = $tahun - $tahun_lahir;
        }
    }

}

$sql = "SELECT * FROM resep WHERE rekam_medis_id = '$rekam_medis_id'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        }
    }

}

// Prepare a select statement untuk mendapatkan user buat print resep
$sql = "SELECT * FROM resep WHERE rekam_medis_id = '$rekam_medis_id'";

if ($stmt = mysqli_prepare($link, $sql)) {

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result)) {

            $resep_id[] = $row['id'];
        }
    }

}


if (!empty($resep_id)) {
    foreach ($resep_id as $value) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST['selesai'])) {

                $sql = "UPDATE resep set status='selesai' WHERE rekam_medis_id = '$rekam_medis_id'";

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

                // } elseif (isset($_POST['delete=' . $value])) {


                //     $sql = "DELETE FROM resep where id = '$value'";

                //     if ($stmt = mysqli_prepare($link, $sql)) {

                //         if (mysqli_stmt_execute($stmt)) {

                //             header('location: view_resep.php?rekam_medis_id=' . $rekam_medis_id);
                //             exit();

                //         } else {
                //             echo "Error";
                //         }

                //     }


                // } 
            } elseif (isset($_POST['update'])) {

                $dibeli = $_POST['dibeli=' . $value];
                $harga = $_POST['harga=' . $value];
                $total = $dibeli * $harga;

                $sql = "UPDATE resep set dibeli = '$dibeli', harga='$harga', total ='$total' WHERE id = '$value'";

                if ($stmt = mysqli_prepare($link, $sql)) {

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);
                    } else {
                        echo "ERROR";
                    }

                    mysqli_stmt_close($stmt);

                }


            } elseif (isset($_POST['print'])) {
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
    <title>Apoteker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {

            width: 70%;
            height: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
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

    include "nav.php";

    ?>
    <div class="wrapper">

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
        </div>


        <h2 class="my-3" id="noprint">Resep Obat</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?rekam_medis_id=' . $rekam_medis_id) ?>"
            method="post">

            <table class="table" id="noprint">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Jumlah (tab/cap/syr/pcs) </th>
                        <th scope="col">Terapi</th>

                        <th scope="col">Jumlah Dibeli</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Total</th>
                        <!-- <th scope="col">Action</th> -->
                        <!-- <th scope="col">Print</th> -->
                    </tr>

                </thead>
                <tbody>

                    <?php

                    if (!empty($resep_id)) {
                        foreach ($resep_id as $value) {

                            // Prepare a select statement
                            $sql = "SELECT * FROM resep WHERE id = '$value'";

                            if ($stmt = mysqli_prepare($link, $sql)) {

                                // Attempt to execute the prepared statement
                                if (mysqli_stmt_execute($stmt)) {

                                    $result = mysqli_stmt_get_result($stmt);

                                    if (mysqli_num_rows($result) == 1) {

                                        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                        $obat = $row["obat"];
                                        $jumlah = $row["jumlah"];
                                        $terapi = $row["terapi"];

                                        $dibeli = $row['dibeli'];
                                        $harga = $row['harga'];
                                        $total = $row['total'];
                                        $status = $row['status'];
                                    }
                                }
                            }

                            ?>


                            <tr>
                                <td>
                                    <?php echo $obat ?>
                                </td>
                                <td>
                                    <!-- <input type="text" name="jumlah=<?php echo $value ?>" value="<?php echo $jumlah ?>"> -->
                                    <?php echo $jumlah ?>
                                </td>
                                <td>
                                    <?php echo $terapi ?>
                                </td>



                                <td>

                                    <?php


                                    ?>
                                    <input type="number" name="dibeli=<?php echo $value ?>" value="0"
                                        max="<?php echo $jumlah ?>" min="0" hidden>
                                    <?php



                                    ?>

                                </td>

                                <td>


                                    <?php


                                    ?>
                                    <input type="number" name="harga=<?php echo $value ?>" value='0' min='0' hidden>
                                    <?php



                                    ?>

                                </td>

                                <td>

                                    <?php


                                    //if (!empty($_POST['dibeli=' . $value]) && !empty($_POST['harga=' . $value])) {
                            
                                    ?>
                                    <!-- <?php echo $total ?> -->

                                    <?php
                                    $total2 = $total2 + $total;
                                    //}
                            

                                    ?>



                                </td>

                                <!-- <td>
                                    <input type="submit" name="delete=<?php echo $value ?>" value="Delete"
                                        class="btn btn-danger">


                                </td> -->
                                <!-- <td style="text-align: center;">
                                    <input type="checkbox" name="print=<?php echo $value ?>" class="form-check-input"
                                        id="defaultCheck1">
                                    <?php

                                    ?>
                                </td> -->

                            </tr>


                            <?php
                            // if (!empty($_POST['print=' . $value])) {
                            //     if ($_POST['print=' . $value]) {
                    
                            ?>

                            <p id="print">
                                R/
                                <?php echo $obat ?>
                                No.
                                <?php

                                echo integerToRoman($jumlah - $dibeli);



                                ?>
                                <br>
                                S
                                <?php echo $terapi ?>
                            </p>

                            <?php




                            //     }
                            // }
                    




                        }
                    }

                    ?>
                    <tr>
                        <th scope='col'>
                            Total
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <?php echo $total2 ?>
                        </th>

                    </tr>


                </tbody>
            </table>

            <div class="form-group" id="noprint">
                <?php

                if ($row['status'] == 'selesai') {
                    ?>

                    <input type="submit" name="print" class="btn btn-primary" value="Print">
                    <a href="apoteker.php" class="btn btn-danger">Back</a>

                    <?php
                } else {

                    ?>
                    <input type="submit" name="update" class="btn btn-primary" value="Update">
                    <input type="submit" name="print" class="btn btn-primary" value="Print">
                    <input type="submit" name="selesai" class="btn btn-primary" value="Selesai">
                    <a href="apoteker.php" class="btn btn-danger">Back</a>

                <?php

                }

                ?>



            </div>
        </form>

        <div style="text-align:center; break-after: page;" id="print">
            <p>Pro :
                <?php echo $nama; ?> <br> Umur :
                <?php echo $umur; ?><br> Alamat :
                <?php echo $alamat; ?><br><br> <i>obat tidak boleh diganti tanpa seijin
                    dokter</i>
            </p>
        </div>

        <?php

        include "nota.php";

        ?>

    </div>
</body>

</html>