<?php
require_once 'config.php';
require_once 'koneksi.php';

// Drop table vaksinasi
$conn->query("DROP TABLE IF EXISTS vaksinasi");

// Recreate table vaksinasi with correct FK to pasien
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
    FOREIGN KEY (hewan_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (jadwal_id) REFERENCES jadwal_vaksinasi(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE SET NULL
)";

if ($conn->query($sql_vaksinasi) === TRUE) {
    echo "Tabel vaksinasi berhasil diperbaiki (referensi ke pasien).<br>";
} else {
    echo "Error creating table vaksinasi: " . $conn->error . "<br>";
}

$conn->close();
?>
