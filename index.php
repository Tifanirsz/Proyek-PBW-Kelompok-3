<?php
// index.php - Halaman utama: form tambah/ubah + daftar mahasiswa
require_once "functions.php";

// [P1] PHP mengambil data dari database lalu menampilkannya sebagai HTML
$daftar = ambilSemuaMahasiswa($koneksi);   // [P3] array
$total  = count($daftar);                  // [P3] int

// Nilai awal form (kosong = mode tambah)
$modeUbah = false;
$npm   = "";
$nama  = "";
$prodi = "";
$tugas = "";
$uts   = "";
$uas   = "";
$aktif = true;

// Kalau ada ?ubah=NPM di alamat, isi form dengan data mahasiswa itu
if (isset($_GET["ubah"])) {
    $mhs = ambilMahasiswa($koneksi, $_GET["ubah"]);
    if ($mhs) {
        $modeUbah = true;
        $npm   = $mhs->npm;
        $nama  = $mhs->nama;
        $prodi = $mhs->prodi;
        $tugas = $mhs->tugas;
        $uts   = $mhs->uts;
        $uas   = $mhs->uas;
        $aktif = $mhs->aktif;
    }
}

$judulHalaman = "Sistem Akademik";
$subjudul     = "Data mahasiswa, nilai, dan kelulusan";
$halamanAktif = "index.php";
require "partials/header.php";
?>

<section class="card">
    <?php // [P4] operator TERNARY: judul berubah sesuai mode ?>
    <h2><?= $modeUbah ? "Ubah Data Mahasiswa" : "Tambah Mahasiswa" ?></h2>

    <form method="post" action="proses.php">
        <input type="hidden" name="aksi" value="<?= $modeUbah ? 'ubah' : 'tambah' ?>">
        <?php if ($modeUbah): ?>
            <?php // NPM lama dikirim supaya UPDATE tahu baris mana yang diubah ?>
            <input type="hidden" name="npm_lama" value="<?= escapeHtml($npm) ?>">
        <?php endif; ?>

        <div class="baris">
            <div>
                <label for="npm">NPM</label>
                <input type="text" id="npm" name="npm" required
                       value="<?= escapeHtml($npm) ?>" placeholder="10 digit, contoh 4524210009">
            </div>
            <div>
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" required value="<?= escapeHtml($nama) ?>">
            </div>
            <div>
                <label for="program_studi">Program Studi</label>
                <input type="text" id="program_studi" name="program_studi" value="<?= escapeHtml($prodi) ?>">
            </div>
        </div>

        <div class="baris" style="margin-top:14px">
            <div>
                <label for="nilai_tugas">Nilai Tugas (30%)</label>
                <input type="number" step="0.01" id="nilai_tugas" name="nilai_tugas" required value="<?= $tugas ?>">
            </div>
            <div>
                <label for="nilai_uts">Nilai UTS (30%)</label>
                <input type="number" step="0.01" id="nilai_uts" name="nilai_uts" required value="<?= $uts ?>">
            </div>
            <div>
                <label for="nilai_uas">Nilai UAS (40%)</label>
                <input type="number" step="0.01" id="nilai_uas" name="nilai_uas" required value="<?= $uas ?>">
            </div>
            <div class="cek">
                <input type="checkbox" id="status_aktif" name="status_aktif" value="1" <?= $aktif ? "checked" : "" ?>>
                <label for="status_aktif">Mahasiswa aktif</label>
            </div>
        </div>

        <div class="aksi">
            <button class="btn" type="submit"><?= $modeUbah ? "Simpan Perubahan" : "Tambah Mahasiswa" ?></button>
            <?php if ($modeUbah): ?>
                <a href="index.php">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="card">
    <h2>Daftar Mahasiswa (<?= $total ?>)</h2>

    <?php if ($total == 0): ?>
        <p class="kosong">Belum ada data mahasiswa.</p>
    <?php else: ?>
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th><th>NPM</th><th>Nama</th><th>Program Studi</th>
                        <th class="angka">Nilai Akhir</th><th>Grade</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php // [P7] FOR: menampilkan daftar mahasiswa dalam TABEL ?>
                    <?php for ($i = 0; $i < $total; $i++): ?>
                        <?php $m = $daftar[$i]; ?>
                        <?php // [P4] modulus (%): baris genap diberi warna beda ?>
                        <tr class="<?= $i % 2 == 0 ? 'baris-genap' : '' ?>">
                            <td><?= $i + 1 ?></td>
                            <td><code><?= escapeHtml($m->npm) ?></code></td>
                            <td><?= escapeHtml($m->nama) ?></td>
                            <td><?= escapeHtml($m->prodi) ?></td>
                            <td class="angka"><?= formatNilai($m->hitungNilai()) ?></td>
                            <td><span class="badge <?= kelasGrade($m->getGrade()) ?>"><?= $m->getGrade() ?></span></td>
                            <td>
                                <span class="badge <?= $m->aktif ? 'badge-hijau' : 'badge-abu' ?>">
                                    <?= $m->getStatusAktif() ?>
                                </span>
                            </td>
                            <td>
                                <div class="aksi">
                                    <a class="btn btn-kecil" href="index.php?ubah=<?= urlencode($m->npm) ?>">Ubah</a>
                                    <form method="post" action="proses.php" onsubmit="return confirm('Yakin hapus data ini?')">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="npm" value="<?= escapeHtml($m->npm) ?>">
                                        <button class="btn btn-merah btn-kecil" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require "partials/footer.php"; ?>
