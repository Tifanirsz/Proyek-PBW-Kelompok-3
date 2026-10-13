<?php
require_once "functions.php";

$mahasiswa      = ambilSemuaMahasiswa($koneksi);
$totalMahasiswa = count($mahasiswa);

// Variabel untuk menampung hasil hitungan
$totalAktif     = 0;
$totalLulus     = 0;
$totalNilai     = 0;
$nilaiTertinggi = 0;
$nilaiTerendah  = 100;

// [P5] WHILE: menghitung rekap dari semua mahasiswa
// [P4] operator += dan ++ , perbandingan > dan <
$i = 0;
while ($i < $totalMahasiswa) {
    $m          = $mahasiswa[$i];
    $nilaiAkhir = $m->hitungNilai();

    $totalNilai += $nilaiAkhir;

    if ($m->aktif) {
        $totalAktif++;
    }
    if ($m->getKelulusan() == "Lulus") {
        $totalLulus++;
    }
    if ($nilaiAkhir > $nilaiTertinggi) {
        $nilaiTertinggi = $nilaiAkhir;
    }
    if ($nilaiAkhir < $nilaiTerendah) {
        $nilaiTerendah = $nilaiAkhir;
    }

    $i++;
}

// [P4] operator pembagian (/) dan ternary: jangan bagi dengan 0 kalau data kosong
$rataRata = $totalMahasiswa > 0 ? $totalNilai / $totalMahasiswa : 0;

// [P6] DO-WHILE: cek data siap, dijalankan minimal 1 kali
$laporanSiap = false;
$percobaan   = 0;
do {
    $percobaan++;
    if ($totalMahasiswa > 0) {
        $laporanSiap = true;
    }
} while (!$laporanSiap && $percobaan < 3);

$judulHalaman = "Laporan Akademik";
$subjudul     = "Rekap nilai dan kelulusan seluruh mahasiswa";
$halamanAktif = "laporan.php";
require "partials/header.php";
?>

<?php if (!$laporanSiap): ?>
    <div class="alert alert-error">
        Belum ada data mahasiswa. Tambahkan data di <a href="index.php">Beranda</a>.
    </div>
<?php else: ?>

    <div class="grid grid-4" style="margin-top:20px">
        <div class="stat biru"><small>Total Mahasiswa</small><strong><?= $totalMahasiswa ?></strong></div>
        <div class="stat cyan"><small>Mahasiswa Aktif</small><strong><?= $totalAktif ?></strong></div>
        <div class="stat hijau"><small>Lulus</small><strong><?= $totalLulus ?></strong></div>
        <div class="stat oranye"><small>Rata-rata Nilai Akhir</small><strong><?= formatNilai($rataRata) ?></strong></div>
    </div>

    <section class="card">
        <h2>Ringkasan Nilai</h2>
        <table>
            <tbody>
                <tr><td>Nilai tertinggi</td><td class="angka"><?= formatNilai($nilaiTertinggi) ?></td></tr>
                <tr><td>Nilai terendah</td><td class="angka"><?= formatNilai($nilaiTerendah) ?></td></tr>
                <tr><td>Batas kelulusan</td><td class="angka"><?= formatNilai(BATAS_LULUS) ?></td></tr>
                <tr><td>Persentase lulus</td><td class="angka"><?= round($totalLulus / $totalMahasiswa * 100) ?>%</td></tr>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Tabel Nilai Mahasiswa</h2>
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th><th>NPM</th><th>Nama</th>
                        <th class="angka">Tugas</th><th class="angka">UTS</th><th class="angka">UAS</th>
                        <th class="angka">Nilai Akhir</th><th>Grade</th><th>Kelulusan</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php // [P7] FOR: satu baris tabel untuk setiap mahasiswa ?>
                    <?php for ($n = 0; $n < $totalMahasiswa; $n++): ?>
                        <?php $m = $mahasiswa[$n]; ?>
                        <?php // [P4] modulus (%): baris genap diberi warna beda ?>
                        <tr class="<?= $n % 2 == 0 ? 'baris-genap' : '' ?>">
                            <td><?= $n + 1 ?></td>
                            <td><code><?= escapeHtml($m->npm) ?></code></td>
                            <td><?= escapeHtml($m->nama) ?></td>
                            <td class="angka"><?= formatNilai($m->tugas) ?></td>
                            <td class="angka"><?= formatNilai($m->uts) ?></td>
                            <td class="angka"><?= formatNilai($m->uas) ?></td>
                            <td class="angka"><strong><?= formatNilai($m->hitungNilai()) ?></strong></td>
                            <td><span class="badge <?= kelasGrade($m->getGrade()) ?>"><?= $m->getGrade() ?></span></td>
                            <td>
                                <span class="badge <?= $m->getKelulusan() == 'Lulus' ? 'badge-hijau' : 'badge-merah' ?>">
                                    <?= $m->getKelulusan() ?>
                                </span>
                            </td>
                            <td><?= $m->getStatusAktif() ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php endif; ?>

<?php require "partials/footer.php"; ?>
