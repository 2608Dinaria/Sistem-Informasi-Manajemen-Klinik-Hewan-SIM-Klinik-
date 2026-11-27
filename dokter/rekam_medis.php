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

$page_title = 'Rekam Medis';
include '../layouts/header_dashboard.php';

// Fungsi untuk menampilkan isi Summernote secara rapi di tabel
function cleanFull($text) {
    // Hilangkan semua script untuk keamanan
    $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $text);

    // Hapus sebagian besar styling inline agar lebih rapi, tapi biarkan <p>, <br>, <b>, <ul>, <li>, dll.
    $allowed = '<p><br><b><strong><i><em><u><ul><ol><li>';

    $clean = strip_tags($text, $allowed);

    return nl2br($clean);
}
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">

        <div class="topbar">
            <h2><i class="fas fa-file-medical me-2"></i>Rekam Medis</h2>
            <div class="user-info">
                <a href="<?php echo APP_URL; ?>/dokter/tambah_rekam_medis.php" 
                   class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-plus me-2"></i>Tambah Rekam Medis
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
                                <th>Pasien</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT rm.*, p.nama_hewan 
                                FROM rekam_medis rm
                                JOIN pasien p ON rm.pasien_id = p.id
                                WHERE rm.dokter_id = ?
                                ORDER BY rm.tanggal DESC
                            ");
                            $stmt->bind_param("i", $dokter_id);
                            $stmt->execute();
                            $rekam = $stmt->get_result();
                            $no = 1;
                            
                            if ($rekam->num_rows > 0):
                                while ($row = $rekam->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>

                                <td><?= formatTanggal($row['tanggal']); ?></td>

                                <td><?= $row['nama_hewan']; ?></td>

                                <!-- Tampilkan Diagnosis FULL & RAPI -->
                                <td><?= cleanFull($row['diagnosis']); ?></td>

                                <!-- Tampilkan Treatment FULL & RAPI -->
                                <td><?= cleanFull($row['treatment']); ?></td>

                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada rekam medis
                                </td>
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
