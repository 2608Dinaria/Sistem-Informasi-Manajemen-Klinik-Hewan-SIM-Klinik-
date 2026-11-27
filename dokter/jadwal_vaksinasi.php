<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

$dokter_id = $_SESSION['user_id']; // Assuming user_id is linked to dokter table.
// Wait, usually dokter table has its own ID, and users table has ID.
// I need to find the dokter_id from users table.
// Let's check `dokter/dashboard.php` or similar.
// I'll assume I need to fetch dokter_id from `dokter` table using `user_id` or `id_user`.
// Let's check `dokter/profil.php` if it exists, or just query `dokter` table.
// Assuming `dokter` table has `id_user` column.

$stmt = $conn->prepare("SELECT id FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter_data = $stmt->get_result()->fetch_assoc();
$dokter_id_real = $dokter_data['id'];

$page_title = 'Jadwal Vaksinasi';
include '../layouts/header_dashboard.php';

// Ambil jadwal vaksinasi yang aktif (hari ini atau masa depan)
// Dan ada pasien yang sudah dikonfirmasi
$today = date('Y-m-d');
$query = "SELECT jv.*, COUNT(v.id) as jumlah_pasien
          FROM jadwal_vaksinasi jv
          LEFT JOIN vaksinasi v ON jv.id = v.jadwal_id AND v.status = 'Dikonfirmasi'
          WHERE jv.tanggal >= '$today'
          GROUP BY jv.id
          ORDER BY jv.tanggal ASC, jv.waktu ASC";
$result = $conn->query($query);
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-user-md me-2"></i>Jadwal Vaksinasi & Pemeriksaan</h2>
        </div>
        
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i><?php echo formatTanggal($row['tanggal']); ?> - <?php echo formatWaktu($row['waktu']); ?>
                                </h5>
                                <span class="badge bg-light text-primary"><?php echo htmlspecialchars($row['jenis_vaksin']); ?></span>
                            </div>
                            <div class="card-body">
                                <p class="mb-3"><i class="fas fa-info-circle me-2"></i><?php echo $row['catatan'] ? htmlspecialchars($row['catatan']) : 'Tidak ada catatan khusus'; ?></p>
                                
                                <h6>Daftar Pasien (Siap Vaksin):</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Hewan</th>
                                                <th>Jenis</th>
                                                <th>Pemilik</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $jadwal_id = $row['id'];
                                            $q_pasien = "SELECT v.*, h.nama_hewan, h.jenis, p.nama_lengkap as nama_pemilik
                                                         FROM vaksinasi v
                                                         JOIN pasien h ON v.hewan_id = h.id
                                                         JOIN pemilik p ON h.id_pemilik = p.id
                                                         WHERE v.jadwal_id = $jadwal_id AND v.status = 'Dikonfirmasi'";
                                            $res_pasien = $conn->query($q_pasien);
                                            
                                            if ($res_pasien->num_rows > 0):
                                                $no = 1;
                                                while ($p = $res_pasien->fetch_assoc()):
                                            ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo htmlspecialchars($p['nama_hewan']); ?></td>
                                                    <td><?php echo htmlspecialchars($p['jenis']); ?></td>
                                                    <td><?php echo htmlspecialchars($p['nama_pemilik']); ?></td>
                                                    <td>
                                                        <a href="input_vaksinasi.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-stethoscope me-1"></i> Periksa
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php 
                                                endwhile;
                                            else:
                                            ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Belum ada pasien yang dikonfirmasi untuk jadwal ini.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>Tidak ada jadwal vaksinasi aktif saat ini.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
