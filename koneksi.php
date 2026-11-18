<?php
// Mengatur agar session login bertahan lama (misal 24 jam = 86400 detik)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ekskul_smkn5";

$conn = mysqli_connect($host,$user, $pass,$db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>