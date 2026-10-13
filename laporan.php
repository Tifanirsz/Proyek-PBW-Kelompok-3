<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/classes/Mahasiswa.php';
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
$sql = "SELECT npm, nama, program_studi,
               nilai_tugas, nilai_uts, nilai_uas, status_aktif
        FROM mahasiswa
        ORDER BY nama ASC";
$result = mysqli_query($koneksi, $sql);
if ($result === false) {
    error_log('Query mahasiswa gagal: ' . mysqli_error($koneksi));
    exit('Data mahasiswa gagal dimuat.');
}
$mahasiswa = [];
while ($row = mysqli_fetch_assoc($result)) {
    $mahasiswa[] = new Mahasiswa(
        $row['npm'],
        $row['nama'],
        $row['program_studi'],
        (float) $row['nilai_tugas'],
        (float) $row['nilai_uts'],
        (float) $row['nilai_uas'],
        (bool) $row['status_aktif']
    );
}
mysqli_free_result($result);
// WHILE: rekap data mahasiswa.
$totalMahasiswa = count($mahasiswa);
$totalAktif = 0;
$totalLulus = 0;
$totalNilai = 0.0;
$i = 0;
while ($i < $totalMahasiswa) {
    $item = $mahasiswa[$i];
    $totalNilai += $item->hitungNilaiAkhir();
    if ($item->isAktif()) {
        $totalAktif++;
    }
    if ($item->tentukanStatusKelulusan() === 'Lulus') {
        $totalLulus++;
    }
    $i++;
}
$rataRata = $totalMahasiswa > 0
    ? $totalNilai / $totalMahasiswa
    : 0.0;
// DO-WHILE: pemeriksaan kesiapan laporan.
$laporanSiap = false;
$percobaan = 0;
do {
    $percobaan++;
    if ($totalMahasiswa > 0) {
        $laporanSiap = true;
    }
} while (!$laporanSiap && $percobaan < 3);
?>