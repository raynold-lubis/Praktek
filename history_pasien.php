<?php
require_once "config.php";

$id = mysqli_real_escape_string($link, $_GET['id']);

$result = ["found" => "no"];

$q = "SELECT tanggal, keluhan FROM pendaftaran WHERE pasien_id = '$id' AND status = 'Selesai' ";
$res = mysqli_query($link, $q);

$arr = [];
while ($row = mysqli_fetch_assoc($res)) {
    $arr[] = $row;

}
$result['found'] = "ok";
$result["result"] = $arr;
echo json_encode($result);


?>