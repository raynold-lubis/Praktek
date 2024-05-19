<?php
require_once "config.php";

$id = mysqli_real_escape_string($link, $_GET['id']);
$password = mysqli_real_escape_string($link, $_GET['password']);

$q = "UPDATE pasien SET password='$password' WHERE id='$id'";
mysqli_query($link, $q);



?>