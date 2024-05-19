<?php
require_once "config.php";
$id = mysqli_real_escape_string($link, $_GET['id']);

$result = ["found" => "no"];
$q = "SELECT * FROM pasien WHERE id = '$id'";
$res = mysqli_query($link, $q);
if ($row = mysqli_fetch_assoc($res)) {
    $result["found"] = "ok";
    $result["id"] = $row;
}

echo json_encode($result);

?>