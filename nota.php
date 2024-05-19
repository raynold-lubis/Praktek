<center id="print">
    <table style="font-size:xx-large; font-family:calibri; border-collapse: collapse;' border='0'">
        <td align='CENTER' vertical-align:top'><span style='color:black;'>
                <b> APOTEK RAY FARMA</b></br>Jl. Seth Adji Kav II Palangkaraya </span></br>
            <span style="font-size:xx-large">Tgl :
                <?php echo $tgl ?> </br> Pasien :
                <?php echo $nama ?>
            </span></br></br>
        </td>
    </table>
    <style>
        hr {
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-style: inset;
            border-width: 1px;
        }
    </style>
    <table cellspacing='0' cellpadding='0' style='font-size:xx-large; font-family:calibri;  border-collapse: collapse;'
        border='0'>
        <tr align='center'>
            <td width='10%'>Item</td>
            <td width='4%'>Qty</td>
            <td width='13%'>Price</td>
            <td width='13%'>Total</td>
        <tr>
            <td colspan='4'>
                <hr>
            </td>
        </tr>
        </tr>

        <?php
        $total = 0;


        if (!empty($resep)) {
            foreach ($resep as $value) {

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
                            $dibeli = $row['dibeli'];
                            $harga = $row['harga'];
                        }
                    }
                }
                ?>

                <tr>
                    <td style='vertical-align:top'>
                        <?php echo $obat ?>
                    </td>
                    <td style='vertical-align:top; text-align:right; padding-right:10px'>
                        <?php echo $dibeli ?>
                    </td>
                    <td style='vertical-align:top; text-align:right; padding-right:10px'>
                        <?php echo $harga ?>
                    </td>
                    <td style='text-align:right; vertical-align:top'>
                        <?php
                        $total = $harga * $dibeli;
                        echo $total;
                        $total2 = $total2 + $total;
                        ?>
                    </td>
                </tr>

                <?php
            }
        }


        ?>
        </tr>

        <tr>
            <td colspan="4">
                <hr>
            </td>
        </tr>

        <tr>
            <td colspan='3'>
                <div style='text-align:right; color:black'>Total : </div>
            </td>
            <td style='text-align:right; font-size:xx-large; color:black'>
                <?php echo $total2; ?>
            </td>
        </tr>
    </table>
    <table style='font-size:xx-large;' cellspacing='2'>
        <tr></br>
            <td align='center'>****** TERIMAKASIH ******</br></td>
        </tr>
    </table>

</center>