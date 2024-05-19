<?php
require_once "config.php";
$user = mysqli_real_escape_string($link, $_GET['user']);

$result = ["found" => "no"];
$q = "SELECT * FROM pendaftaran WHERE pasien_id = '$user' AND status='Menunggu' ";
$res = mysqli_query($link, $q);
if ($row = mysqli_fetch_assoc($res)) {
    $result["found"] = "ok";
    $result["users_id"] = $row;
}

// Untuk menampilkan dokter yang hadir
$q = "SELECT * FROM users WHERE user = 'Active'";
$res = mysqli_query($link, $q);
if ($row = mysqli_fetch_assoc($res)) {
    $result['nama'] = $row;
}

echo json_encode($result);

?>