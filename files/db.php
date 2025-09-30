<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "magyarositasok";

$mysqli = mysqli_init();
if (!$mysqli) {
    die("mysqli_init failed");
}

mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
mysqli_real_connect($mysqli, $servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Adatbázis kapcsolódási hiba: " . $mysqli->connect_error);
}

if (!$mysqli->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $mysqli->error);
}

$conn = $mysqli;
?>