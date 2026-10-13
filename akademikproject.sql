CREATE DATABASE IF NOT EXISTS `akademikproject`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `akademikproject`;

DROP TABLE IF EXISTS `mahasiswa`;

CREATE TABLE `mahasiswa` (
  `npm`           VARCHAR(15)  NOT NULL,
  `nama`          VARCHAR(100) NOT NULL,
  `program_studi` VARCHAR(100) DEFAULT NULL,
  `nilai_tugas`   DECIMAL(5,2) NOT NULL DEFAULT 0,
  `nilai_uts`     DECIMAL(5,2) NOT NULL DEFAULT 0,
  `nilai_uas`     DECIMAL(5,2) NOT NULL DEFAULT 0,
  `status_aktif`  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`npm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mahasiswa`
  (`npm`, `nama`, `program_studi`, `nilai_tugas`, `nilai_uts`, `nilai_uas`, `status_aktif`) VALUES
('4524210073', 'Nadine Agietha Bethesda',      'Teknik Informatika', 90.00, 85.00, 92.00, 1),
('4524210108', 'Firyal Nibras Hariyadi',       'Teknik Informatika', 85.00, 80.00, 88.00, 1),
('4524210109', 'Joanne Trixie Isaura',         'Teknik Informatika', 88.00, 84.00, 90.00, 1),
('4524210122', 'Mujizat Hasyir Azhar',         'Teknik Informatika', 78.00, 72.00, 80.00, 1),
('4524210126', 'Rihhadatul''Aisy Septifani Z', 'Teknik Informatika', 92.00, 89.00, 94.00, 1);
