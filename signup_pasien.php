<?php
require_once "config.php";
$user = mysqli_real_escape_string($link, $_GET['user']);
$password = mysqli_real_escape_string($link, $_GET['password']);
$nik = mysqli_real_escape_string($link, $_GET['nik']);
$nama = mysqli_real_escape_string($link, $_GET['nama']);
$alamat = mysqli_real_escape_string($link, $_GET['alamat']);
$jenis_kelamin = mysqli_real_escape_string($link, $_GET['jenis_kelamin']);
$tanggal_lahir = mysqli_real_escape_string($link, $_GET['tanggal_lahir']);
$nomor = mysqli_real_escape_string($link, $_GET['nomor']);

$q = "SELECT * FROM pasien WHERE username = '$user'";
$res = mysqli_query($link, $q);
if ($row = mysqli_fetch_assoc($res)) {
    $result["found"] = "ok";
    echo json_encode($result);
} else {
    $result['found'] = "no";
    echo json_encode($result);
}

if ($result['found'] == "no" && !empty($user) && !empty($password) && !empty($nik) && !empty($nama) && !empty($alamat) && !empty($jenis_kelamin) && !empty($tanggal_lahir) && !empty($nomor)) {
    $q = "INSERT INTO pasien (username, password, nik, nama, alamat, jenis_kelamin, nomor_handphone, tanggal_lahir) VALUES ('$user', '$password', '$nik' ,'$nama','$alamat', '$jenis_kelamin','$nomor','$tanggal_lahir')";

    mysqli_query($link, $q);
}

?>