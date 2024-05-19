<?php
require_once "config.php";
$user = mysqli_real_escape_string($link, $_GET["user"]);
$keluhan = mysqli_real_escape_string($link, $_GET["keluhan"]);
$tanggal = mysqli_real_escape_string($link, $_GET["tanggal"]);
$btn = mysqli_real_escape_string($link, $_GET['btn']);

if ($btn == 'Simpan') {
    $q = "INSERT INTO pendaftaran (pasien_id,tanggal,keluhan,status) VALUES ('$user','$tanggal','$keluhan','Menunggu')";
    mysqli_query($link, $q);
    $result = ["found" => "simpan"];
    echo json_encode($result);
} else if ($btn == "Update") {
    $q = "UPDATE pendaftaran SET tanggal = '$tanggal', keluhan= '$keluhan' WHERE pasien_id='$user' AND status = 'Menunggu'";
    mysqli_query($link, $q);
    $result = ["found" => "update"];
    echo json_encode($result);
}



?>