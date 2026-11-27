<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$page_title = 'Dashboard Admin';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-home me-2"></i>Dashboard Admin</h2>
            <div class="user-info">
                <span><i class="fas fa-user me-2"></i><?php echo $_SESSION['nama']; ?></span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
                    $admin_count = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $admin_count; ?></h3>
                    <p><i class="fas fa-users-cog me-2"></i>Total Admin</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM dokter");
                    $dokter_count = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $dokter_count; ?></h3>
                    <p><i class="fas fa-user-md me-2"></i>Total Dokter</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM pemilik");
                    $pemilik_count = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $pemilik_count; ?></h3>
                    <p><i class="fas fa-users me-2"></i>Total Pemilik</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM pasien");
                    $pasien_count = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $pasien_count; ?></h3>
                    <p><i class="fas fa-paw me-2"></i>Total Pasien</p>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM appointment WHERE status = 'pending'");
                    $appointment_pending = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $appointment_pending; ?></h3>
                    <p><i class="fas fa-clock me-2"></i>Appointment Pending</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM rekam_medis WHERE DATE(tanggal) = CURDATE()");
                    $rekam_today = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $rekam_today; ?></h3>
                    <p><i class="fas fa-file-medical me-2"></i>Rekam Medis Hari Ini</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM vaksinasi WHERE DATE(tanggal_vaksin) = CURDATE()");
                    $vaksin_today = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $vaksin_today; ?></h3>
                    <p><i class="fas fa-syringe me-2"></i>Vaksinasi Hari Ini</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) as total FROM appointment WHERE DATE(tanggal) = CURDATE()");
                    $appointment_today = $stmt->fetch_assoc()['total'];
                    ?>
                    <h3><?php echo $appointment_today; ?></h3>
                    <p><i class="fas fa-calendar-check me-2"></i>Appointment Hari Ini</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Appointments -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Appointment Terbaru</h5>
            </div>
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("
                                SELECT a.*, p.nama_hewan, d.nama as nama_dokter 
                                FROM appointment a
                                JOIN pasien p ON a.pasien_id = p.id
                                JOIN dokter d ON a.dokter_id = d.id
                                ORDER BY a.created_at DESC
                                LIMIT 10
                            ");
                            if ($stmt->num_rows > 0):
                                while ($row = $stmt->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo formatTanggal($row['tanggal']); ?></td>
                                <td><?php echo formatWaktu($row['jam']); ?></td>
                                <td><?php echo $row['nama_hewan']; ?></td>
                                <td><?php echo $row['nama_dokter']; ?></td>
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
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

