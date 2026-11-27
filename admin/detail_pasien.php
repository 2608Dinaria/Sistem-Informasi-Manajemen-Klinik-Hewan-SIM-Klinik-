<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pasien
$stmt = $conn->prepare("
    SELECT p.*, pem.nama as nama_pemilik, pem.alamat, pem.telepon 
    FROM pasien p
    JOIN pemilik pem ON p.id_pemilik = pem.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pasien = $result->fetch_assoc();
$stmt->close();

if (!$pasien) {
    header('Location: ' . APP_URL . '/admin/pasien.php');
    exit;
}

$page_title = 'Detail Pasien';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-paw me-2"></i>Detail Pasien</h2>
            <div>
                <a href="<?php echo APP_URL; ?>/admin/edit_pasien.php?id=<?php echo $id; ?>" class="btn btn-primary btn-custom">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <a href="<?php echo APP_URL; ?>/admin/pasien.php" class="btn btn-secondary btn-custom">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0">Data Pasien</h5>
                    </div>
                    <div class="card-body-custom">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nama Hewan</th>
                                <td><?php echo $pasien['nama_hewan']; ?></td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td><?php echo $pasien['jenis']; ?></td>
                            </tr>
                            <tr>
                                <th>Ras</th>
                                <td><?php echo $pasien['ras'] ?? '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Umur</th>
                                <td><?php echo $pasien['umur'] ? $pasien['umur'] . ' tahun' : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td><?php echo $pasien['gender']; ?></td>
                            </tr>
                            <tr>
                                <th>Berat Badan</th>
                                <td><?php echo $pasien['berat_badan'] ? $pasien['berat_badan'] . ' kg' : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0">Data Pemilik</h5>
                    </div>
                    <div class="card-body-custom">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nama</th>
                                <td><?php echo $pasien['nama_pemilik']; ?></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><?php echo $pasien['alamat']; ?></td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td><?php echo $pasien['telepon']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rekam Medis -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="mb-0">Riwayat Rekam Medis</h5>
            </div>
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Dokter</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT rm.*, d.nama as nama_dokter 
                                FROM rekam_medis rm
                                JOIN dokter d ON rm.dokter_id = d.id
                                WHERE rm.pasien_id = ?
                                ORDER BY rm.tanggal DESC
                            ");
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $rekam = $stmt->get_result();
                            
                            if ($rekam->num_rows > 0):
                                while ($r = $rekam->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo formatTanggal($r['tanggal']); ?></td>
                                <td><?php echo $r['nama_dokter']; ?></td>
                                <td><?php echo substr($r['diagnosis'], 0, 50) . '...'; ?></td>
                                <td><?php echo substr($r['treatment'], 0, 50) . '...'; ?></td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada rekam medis</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



