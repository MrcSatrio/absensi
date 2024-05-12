<?php
// Informasi koneksi database
$servername = "103.193.15.90";
$username = 'absen';
$password = "maLCW6FTrcP8Zrwr";
$dbname = "absen";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
