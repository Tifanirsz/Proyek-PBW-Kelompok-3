<?php

require_once "koneksi.php";
require_once "classes/mahasiswa.php";

// =====================================================
// KONSTANTA (nilai tetap, tidak berubah)
// =====================================================
// [P3] KONSTANTA
define("BOBOT_TUGAS", 0.30);   // 30%
define("BOBOT_UTS", 0.30);     // 30%
define("BOBOT_UAS", 0.40);     // 40%
define("NILAI_MIN", 0);
define("NILAI_MAX", 100);
define("BATAS_LULUS", 60);
define("PANJANG_NPM", 10);


// =====================================================
// FUNCTION PERHITUNGAN
// =====================================================

// [P2] FUNCTION #1 | [P4] operator aritmetika (* dan +)
// Nilai akhir = tugas 30% + UTS 30% + UAS 40%
function hitungNilaiAkhir($tugas, $uts, $uas)
{
    $hasil = ($tugas * BOBOT_TUGAS) + ($uts * BOBOT_UTS) + ($uas * BOBOT_UAS);
    return round($hasil, 2);
}

// [P2] FUNCTION #2 | [P4] operator perbandingan (>=)
// Mengubah nilai akhir jadi huruf A/B/C/D/E
function tentukanGrade($nilai)
{
    if ($nilai >= 85) {
        return "A";
    } elseif ($nilai >= 75) {
        return "B";
    } elseif ($nilai >= 60) {
        return "C";
    } elseif ($nilai >= 50) {
        return "D";
    } else {
        return "E";
    }
}

// Menentukan warna badge (class CSS) sesuai grade
function kelasGrade($grade)
{
    if ($grade == "A" || $grade == "B") {   // [P4] operator logika ||
        return "badge-hijau";
    } elseif ($grade == "C") {
        return "badge-biru";
    } elseif ($grade == "D") {
        return "badge-oranye";
    } else {
        return "badge-merah";
    }
}


// =====================================================
// FUNCTION STRING
// =====================================================

// [P4] STRING #1: htmlspecialchars (supaya output aman, tidak bisa disisipi kode HTML)
function escapeHtml($teks)
{
    return htmlspecialchars($teks);
}

// [P4] STRING #2: trim (buang spasi di pinggir)
// [P4] STRING #3: strtolower + ucwords (huruf pertama tiap kata jadi kapital)
function rapikanNama($nama)
{
    $nama = trim($nama);
    $nama = strtolower($nama);
    return ucwords($nama, " '-");
}

// [P4] STRING #4: strlen (cek panjang NPM) | operator logika &&
// NPM valid = semuanya angka dan panjangnya 10
function cekNpm($npm)
{
    return ctype_digit($npm) && strlen($npm) == PANJANG_NPM;
}

// [P4] STRING #5: number_format (format angka: 85,50)
function formatNilai($angka)
{
    return number_format($angka, 2, ",", ".");
}


// =====================================================
// PERULANGAN WHILE dan DO-WHILE
// =====================================================

// [P5] WHILE: selama nilai di luar 0-100, nilai dikoreksi
// [P4] operator logika || (atau)
function koreksiNilai($nilai)
{
    while ($nilai < NILAI_MIN || $nilai > NILAI_MAX) {
        if ($nilai < NILAI_MIN) {
            $nilai = NILAI_MIN;
        } else {
            $nilai = NILAI_MAX;
        }
    }
    return $nilai;
}

// [P6] DO-WHILE: membersihkan NPM, dijalankan minimal 1 kali, maksimal 3 kali
function bersihkanNpm($npm)
{
    $percobaan = 0;
    do {
        $percobaan++;                     // [P4] operator increment (++)
        if ($percobaan == 1) {
            $npm = trim($npm);                 // percobaan 1: buang spasi pinggir
        } else {
            $npm = str_replace(" ", "", $npm); // percobaan 2-3: buang semua spasi
        }
    } while (strlen($npm) != PANJANG_NPM && $percobaan < 3);

    return $npm;
}


// =====================================================
// FUNCTION DATABASE
// =====================================================

// Mengubah 1 baris hasil query menjadi object Mahasiswa.
// [P3] KONVERSI TIPE DATA: (string), (float), (bool)
function barisKeObjek($row)
{
    $npm   = (string) $row["npm"];
    $nama  = (string) $row["nama"];
    $prodi = (string) $row["program_studi"];
    $tugas = (float) $row["nilai_tugas"];
    $uts   = (float) $row["nilai_uts"];
    $uas   = (float) $row["nilai_uas"];
    $aktif = (bool) $row["status_aktif"];

    // [P2] OBJECT: dibuat dari class Mahasiswa pakai "new"
    return new Mahasiswa($npm, $nama, $prodi, $tugas, $uts, $uas, $aktif);
}

// Mengambil semua mahasiswa -> hasilnya ARRAY berisi object Mahasiswa
function ambilSemuaMahasiswa($koneksi)
{
    $hasil = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY nama ASC");

    $daftar = [];   // [P3] tipe data ARRAY
    // [P5] WHILE: baca hasil query satu per satu
    while ($row = mysqli_fetch_assoc($hasil)) {
        $daftar[] = barisKeObjek($row);
    }
    return $daftar;
}

// Mengambil 1 mahasiswa berdasarkan NPM. Kalau tidak ada, hasilnya false.
function ambilMahasiswa($koneksi, $npm)
{
    $npm   = mysqli_real_escape_string($koneksi, $npm);   // amankan teks sebelum masuk SQL
    $hasil = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE npm = '$npm'");
    $row   = mysqli_fetch_assoc($hasil);

    if ($row) {
        return barisKeObjek($row);
    }
    return false;
}

// Menambah mahasiswa baru (INSERT)
function tambahMahasiswa($koneksi, $m)
{
    $npm   = mysqli_real_escape_string($koneksi, $m->npm);
    $nama  = mysqli_real_escape_string($koneksi, $m->nama);
    $prodi = mysqli_real_escape_string($koneksi, $m->prodi);
    $aktif = $m->aktif ? 1 : 0;

    $sql = "INSERT INTO mahasiswa (npm, nama, program_studi, nilai_tugas, nilai_uts, nilai_uas, status_aktif)
            VALUES ('$npm', '$nama', '$prodi', $m->tugas, $m->uts, $m->uas, $aktif)";
    return mysqli_query($koneksi, $sql);
}

// Mengubah data mahasiswa (UPDATE). $npmLama = NPM sebelum diubah.
function ubahMahasiswa($koneksi, $m, $npmLama)
{
    $npm     = mysqli_real_escape_string($koneksi, $m->npm);
    $nama    = mysqli_real_escape_string($koneksi, $m->nama);
    $prodi   = mysqli_real_escape_string($koneksi, $m->prodi);
    $npmLama = mysqli_real_escape_string($koneksi, $npmLama);
    $aktif   = $m->aktif ? 1 : 0;

    $sql = "UPDATE mahasiswa
            SET npm = '$npm', nama = '$nama', program_studi = '$prodi',
                nilai_tugas = $m->tugas, nilai_uts = $m->uts, nilai_uas = $m->uas,
                status_aktif = $aktif
            WHERE npm = '$npmLama'";
    return mysqli_query($koneksi, $sql);
}

// Menghapus mahasiswa (DELETE)
function hapusMahasiswa($koneksi, $npm)
{
    $npm = mysqli_real_escape_string($koneksi, $npm);
    mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE npm = '$npm'");
    return mysqli_affected_rows($koneksi) > 0;   // true kalau ada baris yang terhapus
}
