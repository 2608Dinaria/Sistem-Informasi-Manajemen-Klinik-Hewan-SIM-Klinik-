<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

// Ambil data dokter
$stmt = $conn->prepare("SELECT * FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dokter_id = $dokter['id'];

$page_title = 'Dashboard Dokter';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-home me-2"></i>Dashboard Dokter</h2>
            <div class="user-info">
                <span><i class="fas fa-user-md me-2"></i><?php echo $_SESSION['nama']; ?></span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointment WHERE dokter_id = ? AND status = 'pending'");
                    $stmt->bind_param("i", $dokter_id);
                    $stmt->execute();
                    $appointment_pending = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $appointment_pending; ?></h3>
                    <p><i class="fas fa-clock me-2"></i>Appointment Pending</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointment WHERE dokter_id = ? AND DATE(tanggal) = CURDATE()");
                    $stmt->bind_param("i", $dokter_id);
                    $stmt->execute();
                    $appointment_today = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $appointment_today; ?></h3>
                    <p><i class="fas fa-calendar-check me-2"></i>Appointment Hari Ini</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM rekam_medis WHERE dokter_id = ? AND DATE(tanggal) = CURDATE()");
                    $stmt->bind_param("i", $dokter_id);
                    $stmt->execute();
                    $rekam_today = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $rekam_today; ?></h3>
                    <p><i class="fas fa-file-medical me-2"></i>Rekam Medis Hari Ini</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM vaksinasi WHERE dokter_id = ? AND DATE(tanggal_vaksin) = CURDATE()");
                    $stmt->bind_param("i", $dokter_id);
                    $stmt->execute();
                    $vaksin_today = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $vaksin_today; ?></h3>
                    <p><i class="fas fa-syringe me-2"></i>Vaksinasi Hari Ini</p>
                </div>
            </div>
        </div>
        
        <!-- Appointment Hari Ini -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Appointment Hari Ini</h5>
            </div>
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Pasien</th>
                                <th>Pemilik</th>
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
                                WHERE a.dokter_id = ? AND DATE(a.tanggal) = CURDATE()
                                ORDER BY a.jam ASC
                            ");
                            $stmt->bind_param("i", $dokter_id);
                            $stmt->execute();
                            $appointments = $stmt->get_result();
                            
                            if ($appointments->num_rows > 0):
                                while ($row = $appointments->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo formatWaktu($row['jam']); ?></td>
                                <td><?php echo $row['nama_hewan']; ?></td>
                                <td><?php echo $row['nama_pemilik']; ?></td>
                                <td>
                                    <span class="badge-custom badge-<?php 
                                        echo $row['status'] == 'selesai' ? 'success' : 
                                            ($row['status'] == 'pending' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/dokter/appointment_saya.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada appointment hari ini</td>
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

