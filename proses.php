<?php
// proses.php - Memproses form dari index.php (tambah / ubah / hapus)
// lalu menampilkan hasilnya.
require_once "functions.php";

// Halaman ini hanya boleh dibuka lewat form (POST). Kalau tidak, kembali ke beranda.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit;
}

$aksi    = $_POST["aksi"];   // isinya: tambah / ubah / hapus
$errors  = [];               // daftar pesan error
$log     = [];               // catatan perubahan data otomatis
$pesanOk = "";
$mhs     = false;            // object Mahasiswa (diisi kalau berhasil)


// =====================================================
// HAPUS
// =====================================================
if ($aksi == "hapus") {
    $npmHapus = $_POST["npm"];

    if (hapusMahasiswa($koneksi, $npmHapus)) {
        $pesanOk = "Data mahasiswa dengan NPM $npmHapus berhasil dihapus.";
    } else {
        $errors[] = "Data dengan NPM $npmHapus tidak ditemukan.";
    }

// =====================================================
// TAMBAH atau UBAH
// =====================================================
} elseif ($aksi == "tambah" || $aksi == "ubah") {

    // ----- Ambil data dari form -----
    $nama  = rapikanNama($_POST["nama"]);          // [P4] STRING: trim + strtolower + ucwords
    $prodi = trim($_POST["program_studi"]);
    $aktif = isset($_POST["status_aktif"]);        // [P3] bool: true kalau checkbox dicentang

    // ----- Cek NPM -----
    $npmAsli = $_POST["npm"];
    $npm     = bersihkanNpm($npmAsli);             // [P6] DO-WHILE ada di dalam function ini
    if ($npm !== $npmAsli) {
        $log[] = "NPM dirapikan dari \"$npmAsli\" menjadi \"$npm\".";   // [P4] STRING: variabel di dalam kutip ganda
    }
    if (!cekNpm($npm)) {                           // [P4] operator logika ! (not)
        $errors[] = "NPM harus berupa " . PANJANG_NPM . " digit angka.";   // [P4] STRING: gabung teks pakai titik (.)
    }

    // ----- Cek nama -----
    if ($nama == "") {
        $errors[] = "Nama tidak boleh kosong.";
    }

    // ----- Cek nilai Tugas, UTS, UAS -----
    $nilai = ["tugas" => 0, "uts" => 0, "uas" => 0];   // [P3] array asosiatif
    foreach ($nilai as $kunci => $isi) {
        $mentah = $_POST["nilai_" . $kunci];

        if (!is_numeric($mentah)) {
            $errors[] = "Nilai " . strtoupper($kunci) . " harus berupa angka.";
        } else {
            $angka = (float) $mentah;              // [P3] KONVERSI TIPE: string -> float
            $baru  = koreksiNilai($angka);         // [P5] WHILE ada di dalam function ini
            if ($baru != $angka) {
                $log[] = "Nilai " . strtoupper($kunci) . " ($angka) di luar 0-100, diubah menjadi $baru.";
            }
            $nilai[$kunci] = $baru;
        }
    }

    // ----- Cek NPM sudah dipakai atau belum -----
    $npmLama = "";
    if ($aksi == "ubah") {
        $npmLama = $_POST["npm_lama"];
    }

    if (count($errors) == 0) {
        if ($aksi == "tambah" && ambilMahasiswa($koneksi, $npm)) {
            $errors[] = "NPM $npm sudah terdaftar. Gunakan tombol Ubah di halaman utama.";
        }
        if ($aksi == "ubah" && !ambilMahasiswa($koneksi, $npmLama)) {
            $errors[] = "Data dengan NPM $npmLama tidak ditemukan.";
        }
        if ($aksi == "ubah" && $npm !== $npmLama && ambilMahasiswa($koneksi, $npm)) {
            $errors[] = "NPM $npm sudah dipakai mahasiswa lain.";
        }
    }

    // ----- Simpan ke database kalau tidak ada error -----
    if (count($errors) == 0) {
        // [P2] OBJECT: dibuat dari class Mahasiswa
        $mhs = new Mahasiswa($npm, $nama, $prodi, $nilai["tugas"], $nilai["uts"], $nilai["uas"], $aktif);

        if ($aksi == "tambah") {
            $berhasil = tambahMahasiswa($koneksi, $mhs);
            $pesanOk  = "Mahasiswa baru berhasil ditambahkan.";
        } else {
            $berhasil = ubahMahasiswa($koneksi, $mhs, $npmLama);
            $pesanOk  = "Data mahasiswa berhasil diperbarui.";
        }

        if (!$berhasil) {
            $errors[] = "Data gagal disimpan ke database.";
            $mhs = false;
        }
    }

} else {
    $errors[] = "Aksi tidak dikenal.";
}

$judulHalaman = "Hasil Proses";
$subjudul     = "Ringkasan data yang dikirim";
$halamanAktif = "index.php";
require "partials/header.php";
?>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-error">
        <strong>Proses gagal:</strong>
        <ul>
            <?php foreach ($errors as $pesan): ?>
                <li><?= escapeHtml($pesan) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="alert alert-ok"><?= escapeHtml($pesanOk) ?></div>
<?php endif; ?>

<?php if ($mhs): ?>
    <section class="card">
        <h2>Rincian Perhitungan Nilai</h2>
        <?php
        // Tiap baris: [nama komponen, nilai, bobot]
        $baris = [
            ["Tugas", $mhs->tugas, BOBOT_TUGAS],
            ["UTS",   $mhs->uts,   BOBOT_UTS],
            ["UAS",   $mhs->uas,   BOBOT_UAS],
        ];
        ?>
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr><th>Komponen</th><th class="angka">Nilai</th><th class="angka">Bobot</th><th class="angka">Kontribusi</th></tr>
                </thead>
                <tbody>
                    <?php // [P7] FOR: menampilkan baris Tugas, UTS, UAS dalam TABEL ?>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <tr>
                            <td><?= $baris[$i][0] ?></td>
                            <td class="angka"><?= formatNilai($baris[$i][1]) ?></td>
                            <td class="angka"><?= $baris[$i][2] * 100 ?>%</td>
                            <td class="angka"><?= formatNilai($baris[$i][1] * $baris[$i][2]) ?></td>
                        </tr>
                    <?php endfor; ?>
                    <tr>
                        <td colspan="3"><strong>Nilai Akhir</strong></td>
                        <td class="angka"><strong><?= formatNilai($mhs->hitungNilai()) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>
            Grade: <span class="badge <?= kelasGrade($mhs->getGrade()) ?>"><?= $mhs->getGrade() ?></span>
            &nbsp; Kelulusan:
            <span class="badge <?= $mhs->getKelulusan() == 'Lulus' ? 'badge-hijau' : 'badge-merah' ?>">
                <?= $mhs->getKelulusan() ?>
            </span>
            &nbsp; Status: <span class="badge badge-abu"><?= $mhs->getStatusAktif() ?></span>
        </p>
    </section>
<?php endif; ?>

<?php if (count($log) > 0): ?>
    <section class="card">
        <h2>Catatan Perubahan Data</h2>
        <ul class="catatan">
            <?php foreach ($log as $catatan): ?>
                <li><?= escapeHtml($catatan) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<p><a class="btn" href="index.php">&larr; Kembali ke Beranda</a></p>

<?php require "partials/footer.php"; ?>
