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

// Update status appointment
if (isset($_GET['update_status'])) {
    $appointment_id = (int)$_GET['update_status'];
    $status = sanitize($_GET['status']);
    
    $stmt = $conn->prepare("UPDATE appointment SET status = ? WHERE id = ? AND dokter_id = ?");
    $stmt->bind_param("sii", $status, $appointment_id, $dokter_id);
    if ($stmt->execute()) {
        $success = 'Status appointment berhasil diupdate!';
    } else {
        $error = 'Gagal mengupdate status!';
    }
    $stmt->close();
}

$page_title = 'Appointment Saya';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-check me-2"></i>Appointment Saya</h2>
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
                                <th>Jam</th>
                                <th>Pasien</th>
                                <th>Pemilik</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT a.*, p.nama_hewan, pem.nama as nama_pemilik 
                                FROM appointment a
                                JOIN pasien p ON a.pasien_id = p.id
                                JOIN pemilik pem ON p.id_pemilik = pem.id
                                WHERE a.dokter_id = ?
                                ORDER BY a.tanggal DESC, a.jam DESC
                            ");
                            $stmt->bind_param("i", $dokter_id);
                            $stmt->execute();
                            $appointments = $stmt->get_result();
                            $no = 1;
                            
                            if ($appointments->num_rows > 0):
                                while ($row = $appointments->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo formatTanggal($row['tanggal']); ?></td>
                                <td><?php echo formatWaktu($row['jam']); ?></td>
                                <td><?php echo $row['nama_hewan']; ?></td>
                                <td><?php echo $row['nama_pemilik']; ?></td>
                                <td><?php echo $row['keluhan'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge-custom badge-<?php 
                                        echo $row['status'] == 'selesai' ? 'success' : 
                                            ($row['status'] == 'pending' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                    <a href="?update_status=<?php echo $row['id']; ?>&status=dikonfirmasi" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi appointment?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($row['status'] != 'selesai'): ?>
                                    <a href="?update_status=<?php echo $row['id']; ?>&status=selesai" class="btn btn-sm btn-primary" onclick="return confirm('Tandai selesai?')">
                                        <i class="fas fa-check-double"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?php echo APP_URL; ?>/dokter/tambah_rekam_medis.php?appointment_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-medical"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada appointment</td>
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
