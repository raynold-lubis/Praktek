<?php
require_once "config.php";
$id = mysqli_real_escape_string($link, $_GET['id']);
$username = mysqli_real_escape_string($link, $_GET['username']);
$nik = mysqli_real_escape_string($link, $_GET['nik']);
$nama = mysqli_real_escape_string($link, $_GET['nama']);
$alamat = mysqli_real_escape_string($link, $_GET['alamat']);
$jenis_kelamin = mysqli_real_escape_string($link, $_GET['jenis_kelamin']);
$tanggal_lahir = mysqli_real_escape_string($link, $_GET['tanggal_lahir']);
$nomor = mysqli_real_escape_string($link, $_GET['nomor']);

$q = "SELECT * FROM pasien WHERE username ='$username' AND id<>'$id'";
$res = mysqli_query($link, $q);
if ($row = mysqli_fetch_assoc($res)) {
    $result["found"] = "ok";
    echo json_encode($result);
} else {
    $result["found"] = "no";
    echo json_encode($result);
}

if ($result["found"] == "no" && !empty($username) && !empty($nik) && !empty($nama) && !empty($alamat) && !empty($jenis_kelamin) && !empty($tanggal_lahir) && !empty($nomor)) {
    $q = "UPDATE pasien SET username ='$username', nik='$nik', nama='$nama', alamat='$alamat', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', nomor_handphone='$nomor' WHERE id='$id'";

    mysqli_query($link, $q);
}




?>