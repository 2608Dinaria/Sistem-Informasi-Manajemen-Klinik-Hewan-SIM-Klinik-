<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

// Filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$status = isset($_GET['status']) ? $_GET['status'] : '';

$where_clause = "WHERE v.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
if ($status) {
    $where_clause .= " AND v.status = '$status'";
}

$query = "SELECT v.*, h.nama_hewan AS nama_hewan, p.nama as nama_pemilik, jv.jenis_vaksin as jenis_vaksin_jadwal, d.nama as nama_dokter
          FROM vaksinasi v
          JOIN pasien h ON v.hewan_id = h.id
          JOIN pemilik p ON h.id_pemilik = p.id
          JOIN jadwal_vaksinasi jv ON v.jadwal_id = jv.id
          LEFT JOIN dokter d ON v.dokter_id = d.id
          $where_clause
          ORDER BY v.created_at DESC";
$result = $conn->query($query);

$page_title = 'Laporan Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-file-alt me-2"></i>Laporan Vaksinasi</h2>
            <button onclick="window.print()" class="btn btn-secondary btn-custom">
                <i class="fas fa-print me-2"></i>Cetak
            </button>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="Menunggu Konfirmasi" <?php echo $status == 'Menunggu Konfirmasi' ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                            <option value="Dikonfirmasi" <?php echo $status == 'Dikonfirmasi' ? 'selected' : ''; ?>>Dikonfirmasi</option>
                            <option value="Sudah Divaksin" <?php echo $status == 'Sudah Divaksin' ? 'selected' : ''; ?>>Sudah Divaksin</option>
                            <option value="Ditunda" <?php echo $status == 'Ditunda' ? 'selected' : ''; ?>>Ditunda</option>
                            <option value="Ditolak" <?php echo $status == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pemilik</th>
                        <th>Hewan</th>
                        <th>Jenis Vaksin</th>
                        <th>Dokter</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo formatTanggal($row['tanggal_vaksin'] ? $row['tanggal_vaksin'] : date('Y-m-d', strtotime($row['created_at']))); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_vaksin'] ? $row['jenis_vaksin'] : $row['jenis_vaksin_jadwal']); ?></td>
                                <td><?php echo $row['nama_dokter'] ? htmlspecialchars($row['nama_dokter']) : '-'; ?></td>
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
                                <td><?php echo htmlspecialchars($row['catatan_dokter'] ? $row['catatan_dokter'] : ($row['alasan_penundaan'] ? $row['alasan_penundaan'] : '-')); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data vaksinasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
