<?php
require_once "config.php";
$username = mysqli_real_escape_string($link, $_GET["username"]);
$password = mysqli_real_escape_string($link, $_GET["password"]);

$result = ["found" => "no"];
$q = "SELECT * FROM pasien WHERE username='$username'";
$res = mysqli_query($link, $q);
if ($row = mysqli_fetch_assoc($res)) {
    if (password_verify($password, $row["password"])) {
        $result["found"] = "ok";
        $result["user"] = $row;
    }
}
echo json_encode($result);
?>