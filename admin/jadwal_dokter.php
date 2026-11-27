<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah ada appointment terkait
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM appointment WHERE jadwal_dokter_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo "<script>alert('Gagal menghapus! Jadwal ini sudah memiliki appointment.'); window.location.href='" . APP_URL . "/admin/jadwal_dokter.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>alert('Jadwal berhasil dihapus!'); window.location.href='" . APP_URL . "/admin/jadwal_dokter.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus jadwal!'); window.location.href='" . APP_URL . "/admin/jadwal_dokter.php';</script>";
        }
    }
    exit;
}

// Ambil data jadwal
$query = "SELECT jd.*, d.nama as nama_dokter 
          FROM jadwal_dokter jd 
          JOIN dokter d ON jd.dokter_id = d.id 
          ORDER BY jd.tanggal DESC, jd.jam_mulai ASC";
$result = $conn->query($query);

$page_title = 'Jadwal Dokter';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-alt me-2"></i>Jadwal Dokter</h2>
            <a href="<?php echo APP_URL; ?>/admin/tambah_jadwal.php" class="btn btn-primary-custom-solid btn-custom">
                <i class="fas fa-plus me-2"></i>Tambah Jadwal
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dokter</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Slot (Terisi/Max)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                                <td><?php echo formatTanggal($row['tanggal']); ?></td>
                                <td><?php echo formatWaktu($row['jam_mulai']) . ' - ' . formatWaktu($row['jam_selesai']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['slot_terisi'] >= $row['max_slot'] ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $row['slot_terisi'] . ' / ' . $row['max_slot']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/admin/edit_jadwal.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/admin/jadwal_dokter.php?delete=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada jadwal dokter.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
