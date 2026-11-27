<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$page_title = 'Laporan Rekam Medis';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-file-medical me-2"></i>Laporan Rekam Medis</h2>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-custom">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <a href="<?php echo APP_URL; ?>/admin/laporan_pasien.php" class="btn btn-secondary btn-custom">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("
                        SELECT rm.*, p.nama_hewan, d.nama AS nama_dokter
                        FROM rekam_medis rm
                        JOIN pasien p ON rm.pasien_id = p.id
                        JOIN dokter d ON rm.dokter_id = d.id
                        ORDER BY rm.tanggal DESC
                    ");

                    $no = 1;

                    if ($stmt->num_rows > 0):
                        while ($row = $stmt->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= formatTanggal($row['tanggal']); ?></td>
                        <td><?= $row['nama_hewan']; ?></td>
                        <td><?= $row['nama_dokter']; ?></td>

                        <td>
                            <div class="catatan-box">
                                <?= !empty($row['diagnosis']) ? $row['diagnosis'] : '-'; ?>
                            </div>
                        </td>

                        <td>
                            <div class="catatan-box">
                                <?= !empty($row['treatment']) ? $row['treatment'] : '-'; ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
