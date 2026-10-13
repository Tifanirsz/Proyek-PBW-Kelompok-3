<?php
// Class Mahasiswa: 1 object = 1 mahasiswa.

// [P2] CLASS
class Mahasiswa
{
    // Properti (data milik object)
    public $npm;
    public $nama;
    public $prodi;
    public $tugas;
    public $uts;
    public $uas;
    public $aktif;

    // Constructor: dijalankan otomatis saat "new Mahasiswa(...)"
    public function __construct($npm, $nama, $prodi, $tugas, $uts, $uas, $aktif)
    {
        $this->npm   = $npm;
        $this->nama  = $nama;
        $this->prodi = $prodi;
        $this->tugas = $tugas;
        $this->uts   = $uts;
        $this->uas   = $uas;
        $this->aktif = $aktif;
    }

    // Method: menghitung nilai akhir (memanggil function di functions.php)
    public function hitungNilai()
    {
        return hitungNilaiAkhir($this->tugas, $this->uts, $this->uas);
    }

    // Method: grade huruf (A, B, C, D, E)
    public function getGrade()
    {
        return tentukanGrade($this->hitungNilai());
    }

    // Method: "Lulus" atau "Tidak Lulus"
    public function getKelulusan()
    {
        // [P4] operator TERNARY
        return $this->hitungNilai() >= BATAS_LULUS ? "Lulus" : "Tidak Lulus";
    }

    // Method: "Aktif" atau "Nonaktif"
    public function getStatusAktif()
    {
        return $this->aktif ? "Aktif" : "Nonaktif";
    }
}
