<?php
// Koneksi database MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pelitarental';

$koneksi = new mysqli('localhost', 'root', '', 'projek_pemweb');

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
