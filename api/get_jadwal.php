<?php
require_once '../config.php';
require_once '../koneksi.php';

header('Content-Type: application/json');

$dokter_id = isset($_GET['dokter_id']) ? (int)$_GET['dokter_id'] : 0;

if ($dokter_id > 0) {
    $stmt = $conn->prepare("
        SELECT jd.*, jd.slot_terisi as terisi
        FROM jadwal_dokter jd
        WHERE jd.dokter_id = ? AND jd.tanggal >= CURDATE()
        ORDER BY jd.tanggal ASC, jd.jam_mulai ASC
    ");
    $stmt->bind_param("i", $dokter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $jadwal = [];
    while ($row = $result->fetch_assoc()) {
        $jadwal[] = $row;
    }
    
    echo json_encode($jadwal);
    $stmt->close();
} else {
    echo json_encode([]);
}
?>


