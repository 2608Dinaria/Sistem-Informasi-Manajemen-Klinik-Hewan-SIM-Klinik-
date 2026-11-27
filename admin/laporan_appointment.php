<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$page_title = 'Laporan Appointment';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-check me-2"></i>Laporan Appointment</h2>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-custom">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <a href="<?php echo APP_URL; ?>/admin/laporan_pasien.php" class="btn btn-secondary btn-custom">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
        
        <div class="card-custom">
            <div class="card-body-custom">
                <div class="table-container">
                    <table class="table table-custom" id="tableAppointment">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Status</th>
                                <th>Keluhan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("
                                SELECT a.*, p.nama_hewan, d.nama as nama_dokter 
                                FROM appointment a
                                JOIN pasien p ON a.pasien_id = p.id
                                JOIN dokter d ON a.dokter_id = d.id
                                ORDER BY a.tanggal DESC, a.jam DESC
                            ");
                            $no = 1;
                            if ($stmt->num_rows > 0):
                                while ($row = $stmt->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
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
                                <td><?php echo $row['keluhan'] ?? '-'; ?></td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



