<?php
require_once "config.php";
$user = mysqli_real_escape_string($link, $_GET["user"]);
$tanggal = mysqli_real_escape_string($link, $_GET["tanggal"]);

$q = "DELETE FROM pendaftaran WHERE tanggal = '$tanggal' AND pasien_id = '$user' AND status <> 'Selesai'";
mysqli_query($link, $q);
$result = ["found" => "no"];
echo json_encode($result);

?>