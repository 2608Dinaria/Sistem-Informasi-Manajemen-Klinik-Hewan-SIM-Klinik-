<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

// Pastikan session ada
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil data pemilik berdasarkan user_id
$stmt = $conn->prepare("SELECT * FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pemilik) {
    echo "<script>alert('Data pemilik tidak ditemukan!');</script>";
    exit;
}

$pemilik_id = $pemilik['id'];


if (!$pemilik) {
    echo "<script>alert('Akun pemilik tidak ditemukan.');</script>";
    exit;
}

$pemilik_id = $pemilik['id']; 

$page_title = 'Dashboard Pemilik';
include '../layouts/header_dashboard.php';
?>


<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-home me-2"></i>Dashboard Pemilik</h2>
            <div class="user-info">
                <span><i class="fas fa-user me-2"></i><?php echo $_SESSION['nama']; ?></span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pasien WHERE id_pemilik = ?");
                    $stmt->bind_param("i", $pemilik_id);
                    $stmt->execute();
                    $hewan_count = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $hewan_count; ?></h3>
                    <p><i class="fas fa-paw me-2"></i>Total Hewan</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) as total FROM appointment a
                        JOIN pasien p ON a.pasien_id = p.id
                        WHERE p.id_pemilik = ? AND a.status = 'pending'
                    ");
                    $stmt->bind_param("i", $pemilik_id);
                    $stmt->execute();
                    $appointment_pending = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $appointment_pending; ?></h3>
                    <p><i class="fas fa-clock me-2"></i>Appointment Pending</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <?php
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) as total FROM vaksinasi v
                        JOIN pasien p ON v.hewan_id = p.id
                        WHERE p.id_pemilik = ?
                    ");
                    $stmt->bind_param("i", $pemilik_id);
                    $stmt->execute();
                    $vaksin_count = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    ?>
                    <h3><?php echo $vaksin_count; ?></h3>
                    <p><i class="fas fa-syringe me-2"></i>Total Vaksinasi</p>
                </div>
            </div>
        </div>
        
        <!-- Appointment Terbaru -->
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
                                <th>Hewan</th>
                                <th>Dokter</th>
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
                                LIMIT 5
                            ");
                            $stmt->bind_param("i", $pemilik_id);
                            $stmt->execute();
                            $appointments = $stmt->get_result();
                            
                            if ($appointments->num_rows > 0):
                                while ($row = $appointments->fetch_assoc()):
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
                                <td colspan="5" class="text-center">Belum ada appointment</td>
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


