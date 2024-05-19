<?php
require_once "config.php";

$username = mysqli_real_escape_string($link, $_GET['username']);
$phone = mysqli_real_escape_string($link, $_GET['phone']);
$password = mysqli_real_escape_string($link, $_GET['password']);

$q = "UPDATE pasien SET password='$password' WHERE username='$username' AND nomor_handphone='$phone'";
mysqli_query($link, $q);



?>