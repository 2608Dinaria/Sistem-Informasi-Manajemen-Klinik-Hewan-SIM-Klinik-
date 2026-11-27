<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

// Ambil data pemilik
$stmt = $conn->prepare("SELECT id FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$pemilik_id = $pemilik['id'];

// Ambil riwayat vaksinasi (TAMBAHKAN catatan_dokter)
$query = "SELECT v.*, h.nama_hewan, jv.tanggal, jv.waktu, 
                 jv.jenis_vaksin AS jenis_vaksin_jadwal, 
                 d.nama AS nama_dokter,
                 v.catatan_dokter
          FROM vaksinasi v
          JOIN pasien h ON v.hewan_id = h.id
          JOIN jadwal_vaksinasi jv ON v.jadwal_id = jv.id
          LEFT JOIN dokter d ON v.dokter_id = d.id
          WHERE h.id_pemilik = ?
          ORDER BY v.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pemilik_id);
$stmt->execute();
$result = $stmt->get_result();

$page_title = 'Riwayat Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-history me-2"></i>Riwayat Vaksinasi</h2>
            <a href="daftar_jadwal_vaksin.php" class="btn btn-primary-custom-solid btn-custom">
                <i class="fas fa-plus me-2"></i>Daftar Vaksin Baru
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Daftar</th>
                        <th>Hewan</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo formatTanggal(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                <td>
                                    <?php echo formatTanggal($row['tanggal']); ?><br>
                                    <small><?php echo htmlspecialchars($row['jenis_vaksin_jadwal']); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    if ($row['status'] == 'Sudah Divaksin') $badgeClass = 'bg-success';
                                    elseif ($row['status'] == 'Dikonfirmasi') $badgeClass = 'bg-primary';
                                    elseif ($row['status'] == 'Ditunda') $badgeClass = 'bg-warning text-dark';
                                    elseif ($row['status'] == 'Ditolak') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $row['status']; ?></span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Sudah Divaksin'): ?>
                                        <small class="text-muted">
                                            Vaksin: <?php echo htmlspecialchars($row['jenis_vaksin'] ? $row['jenis_vaksin'] : $row['jenis_vaksin_jadwal']); ?><br>
                                            Dokter: <?php echo $row['nama_dokter'] ? htmlspecialchars($row['nama_dokter']) : '-'; ?><br>

                                            <!-- ❗ Tambahan Catatan Dokter -->
                                            <?php if (!empty($row['catatan_dokter'])): ?>
                                                <span class="text-primary fw-bold">Catatan Dokter:</span><br>
                                                <span><?php echo nl2br(htmlspecialchars($row['catatan_dokter'])); ?></span>
                                            <?php endif; ?>
                                        </small>

                                    <?php elseif ($row['status'] == 'Ditolak' || $row['status'] == 'Ditunda'): ?>
                                        <small class="text-danger">
                                            Alasan: <?php echo htmlspecialchars($row['alasan_penundaan']); ?>
                                        </small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada riwayat vaksinasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>