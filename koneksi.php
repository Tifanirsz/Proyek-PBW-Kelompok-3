<?php

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "akademikproject");

mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$koneksi) {
    die("Koneksi gagal. Pastikan MySQL sudah di-Start di XAMPP dan file akademikproject.sql sudah di-import.");
}
