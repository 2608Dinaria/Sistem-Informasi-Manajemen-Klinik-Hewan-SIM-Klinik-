<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah ada pendaftaran terkait
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaksinasi WHERE jadwal_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Gagal menghapus! Jadwal ini sudah memiliki pendaftar.'); window.location.href='" . APP_URL . "/admin/jadwal_vaksinasi.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM jadwal_vaksinasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>alert('Jadwal berhasil dihapus!'); window.location.href='" . APP_URL . "/admin/jadwal_vaksinasi.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus jadwal!'); window.location.href='" . APP_URL . "/admin/jadwal_vaksinasi.php';</script>";
        }
    }
    exit;
}

// Ambil data jadwal
$query = "SELECT jv.*, d.nama as nama_dokter 
          FROM jadwal_vaksinasi jv 
          LEFT JOIN dokter d ON jv.dokter_id = d.id 
          ORDER BY jv.tanggal DESC, jv.waktu ASC";
$result = $conn->query($query);

$page_title = 'Jadwal Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-syringe me-2"></i>Jadwal Vaksinasi</h2>
            <a href="<?php echo APP_URL; ?>/admin/tambah_jadwal_vaksinasi.php" class="btn btn-primary-custom-solid btn-custom">
                <i class="fas fa-plus me-2"></i>Tambah Jadwal
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Jenis Vaksin</th>
                        <th>Dokter</th>
                        <th>Kuota (Terisi/Max)</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo formatTanggal($row['tanggal']); ?></td>
                                <td><?php echo formatWaktu($row['waktu']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_vaksin']); ?></td>
                                <td><?php echo $row['nama_dokter'] ? htmlspecialchars($row['nama_dokter']) : '-'; ?></td>
                                <td>
                                    <span class="badge <?php echo $row['kuota_terpakai'] >= $row['kuota'] ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $row['kuota_terpakai'] . ' / ' . $row['kuota']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="catatan-box">
                                        <?php echo !empty($row['catatan']) ? $row['catatan'] : '-'; ?>
                                    </div>
                                </td>

                                <td>
                                    <a href="<?php echo APP_URL; ?>/admin/edit_jadwal_vaksinasi.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/admin/jadwal_vaksinasi.php?delete=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada jadwal vaksinasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
