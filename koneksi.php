<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'akademik';
$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);
if (!$koneksi) {
    error_log('Koneksi database gagal: ' . mysqli_connect_error());
    exit('Koneksi database gagal. Periksa konfigurasi MySQL.');
}
mysqli_set_charset($koneksi, 'utf8mb4');
?>