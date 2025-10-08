<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "mohammed4";  // Your database name

$conn = new mysqli($host, $user, $pass, $db);
//$conn =mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
