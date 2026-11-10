<?php
// Koneksi database MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pelitarental';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
