<?php
require_once "config.php";

$date = date("Y-m-d");
$noAntrian = "";
$tanggal = mysqli_real_escape_string($link, $_GET['tanggal']);
$ctr = 1;

$q = "SELECT * FROM pendaftaran WHERE tanggal = '$tanggal' ORDER BY created_at ASC";
$res = mysqli_query($link, $q);

$arr = [];

while ($row = mysqli_fetch_assoc($res)) {
    $arr[] = $row['pasien_id'];
    if ($row['status'] == 'Sedang dilayani' && $noAntrian == '') {
        $noAntrian = $ctr;
    }
    $ctr++;
}

$result['antrian_masuk'] = $noAntrian;
$result['antrian'] = $arr;

echo json_encode($result);

?>