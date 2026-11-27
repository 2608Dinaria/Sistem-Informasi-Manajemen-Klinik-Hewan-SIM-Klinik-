<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

// Ambil data pemilik
$stmt = $conn->prepare("SELECT * FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pemilik_id = $pemilik['id'];

$page_title = 'Appointment';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-check me-2"></i>Appointment</h2>
            <div class="user-info">
                <a href="<?php echo APP_URL; ?>/pemilik/buat_appointment.php" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-plus me-2"></i>Buat Appointment
                </a>
            </div>
        </div>
        
        <div class="card-custom">
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Hewan</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT a.*, p.nama_hewan, d.nama as nama_dokter 
                                FROM appointment a
                                JOIN pasien p ON a.pasien_id = p.id
                                JOIN dokter d ON a.dokter_id = d.id
                                WHERE p.id_pemilik = ?
                                ORDER BY a.tanggal DESC, a.jam DESC
                            ");
                            $stmt->bind_param("i", $pemilik_id);
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
                                <td><?php echo $row['nama_dokter']; ?></td>
                                <td><?php echo $row['keluhan'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge-custom badge-<?php 
                                        echo $row['status'] == 'selesai' ? 'success' : 
                                            ($row['status'] == 'pending' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada appointment</td>
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