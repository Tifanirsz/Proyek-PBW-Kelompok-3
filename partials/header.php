<?php
// partials/header.php
// Bagian atas halaman (judul + menu). Dipanggil dengan require.
// Variabel yang harus diisi dulu di halaman: $judulHalaman, $subjudul, $halamanAktif
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escapeHtml($judulHalaman) ?> | Sistem Akademik</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="container">
        <a class="brand" href="index.php"><span>Sistem Akademik</span></a>
        <div class="menu">
            <a href="index.php" class="<?= $halamanAktif == 'index.php' ? 'aktif' : '' ?>">Beranda</a>
            <a href="laporan.php" class="<?= $halamanAktif == 'laporan.php' ? 'aktif' : '' ?>">Laporan</a>
        </div>
    </div>
</nav>
<main class="container">
    <div class="judul">
        <h1><?= escapeHtml($judulHalaman) ?></h1>
        <p><?= escapeHtml($subjudul) ?></p>
    </div>
