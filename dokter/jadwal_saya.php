<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

$error = '';
$success = '';

// Ambil data dokter
$stmt = $conn->prepare("SELECT * FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dokter_id = $dokter['id'];

// Hapus jadwal
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE id = ? AND dokter_id = ?");
    $stmt->bind_param("ii", $id, $dokter_id);
    if ($stmt->execute()) {
        $success = 'Jadwal berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus jadwal!';
    }
    $stmt->close();
}

$page_title = 'Jadwal Praktik';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-alt me-2"></i>Jadwal Praktik</h2>
            <div class="user-info">
                <a href="<?php echo APP_URL; ?>/dokter/tambah_jadwal.php" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-plus me-2"></i>Tambah Jadwal
                </a>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div class="card-custom">
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Max Slot</th>
                                <th>Terisi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT jd.*, 
                                (SELECT COUNT(*) FROM appointment WHERE jadwal_dokter_id = jd.id) as terisi
                                FROM jadwal_dokter jd
                                WHERE jd.dokter_id = ?
                                ORDER BY jd.tanggal DESC, jd.jam_mulai DESC
                            ");
                            $stmt->bind_param("i", $dokter_id);
                            $stmt->execute();
                            $jadwal = $stmt->get_result();
                            $no = 1;
                            
                            if ($jadwal->num_rows > 0):
                                while ($row = $jadwal->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo formatTanggal($row['tanggal']); ?></td>
                                <td><?php echo formatWaktu($row['jam_mulai']); ?></td>
                                <td><?php echo formatWaktu($row['jam_selesai']); ?></td>
                                <td><?php echo $row['max_slot']; ?></td>
                                <td><?php echo $row['terisi']; ?></td>
                                <td>
                                    <span class="badge-custom badge-<?php echo $row['status'] == 'aktif' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger btn-delete" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada jadwal</td>
                            </tr>
                            <?php 
                            endif; 
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>