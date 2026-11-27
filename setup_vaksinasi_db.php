<?php
require_once 'config.php';
require_once 'koneksi.php';

// 1. Tabel jadwal_vaksinasi
$sql_jadwal = "CREATE TABLE IF NOT EXISTS jadwal_vaksinasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    jenis_vaksin VARCHAR(100) NOT NULL,
    kuota INT NOT NULL,
    kuota_terpakai INT DEFAULT 0,
    dokter_id INT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE SET NULL
)";

if ($conn->query($sql_jadwal) === TRUE) {
    echo "Tabel jadwal_vaksinasi berhasil dibuat/sudah ada.<br>";
} else {
    echo "Error creating table jadwal_vaksinasi: " . $conn->error . "<br>";
}

// 2. Tabel vaksinasi (pendaftaran & rekam medis vaksin)
$sql_vaksinasi = "CREATE TABLE IF NOT EXISTS vaksinasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hewan_id INT NOT NULL,
    jadwal_id INT NOT NULL,
    dokter_id INT NULL,
    tanggal_vaksin DATE NULL,
    jenis_vaksin VARCHAR(100) NULL,
    dosis VARCHAR(50) NULL,
    catatan_dokter TEXT NULL,
    status ENUM('Menunggu Konfirmasi', 'Dikonfirmasi', 'Ditolak', 'Ditunda', 'Sudah Divaksin') DEFAULT 'Menunggu Konfirmasi',
    alasan_penundaan TEXT NULL,
    next_vaksin_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hewan_id) REFERENCES hewan(id) ON DELETE CASCADE,
    FOREIGN KEY (jadwal_id) REFERENCES jadwal_vaksinasi(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE SET NULL
)";

if ($conn->query($sql_vaksinasi) === TRUE) {
    echo "Tabel vaksinasi berhasil dibuat/sudah ada.<br>";
} else {
    echo "Error creating table vaksinasi: " . $conn->error . "<br>";
}

$conn->close();
?>
