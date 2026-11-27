<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $action = $_POST['action'];
        
        if ($action == 'confirm') {
            $stmt = $conn->prepare("UPDATE vaksinasi SET status = 'Dikonfirmasi' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "<script>alert('Pendaftaran berhasil dikonfirmasi!'); window.location.href='" . APP_URL . "/admin/konfirmasi_vaksinasi.php';</script>";
            }
        } elseif ($action == 'reject') {
            $alasan = sanitize($_POST['alasan']);
            if (empty($alasan)) {
                echo "<script>alert('Alasan penolakan wajib diisi!'); window.location.href='" . APP_URL . "/admin/konfirmasi_vaksinasi.php';</script>";
            } else {
                // Kembalikan kuota
                // First get jadwal_id
                $stmt = $conn->prepare("SELECT jadwal_id FROM vaksinasi WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $jadwal_id = $res['jadwal_id'];
                
                // Update status
                $stmt = $conn->prepare("UPDATE vaksinasi SET status = 'Ditolak', alasan_penundaan = ? WHERE id = ?"); // Using alasan_penundaan column for rejection reason too as per user request "Ditolak (dengan alasan)"
                // Wait, user said "Ditolak (dengan alasan)" and "Ditunda, wajib isi alasan".
                // The table structure has `alasan_penundaan`. I can use that for rejection reason too, or add a new column.
                // Given the structure in step 0: "alasan_penundaan (nullable)". I'll use that.
                $stmt->bind_param("si", $alasan, $id);
                
                if ($stmt->execute()) {
                    // Decrease kuota_terpakai
                    $conn->query("UPDATE jadwal_vaksinasi SET kuota_terpakai = kuota_terpakai - 1 WHERE id = $jadwal_id");
                    echo "<script>alert('Pendaftaran berhasil ditolak!'); window.location.href='" . APP_URL . "/admin/konfirmasi_vaksinasi.php';</script>";
                }
            }
        }
    }
}

// Ambil data pendaftaran yang Menunggu Konfirmasi
$query = "SELECT v.*, h.nama_hewan AS nama_hewan, p.nama AS nama_pemilik, 
                 jv.tanggal, jv.waktu, jv.jenis_vaksin AS jenis_vaksin_jadwal
          FROM vaksinasi v
          JOIN pasien h ON v.hewan_id = h.id
          JOIN pemilik p ON h.id_pemilik = p.id
          JOIN jadwal_vaksinasi jv ON v.jadwal_id = jv.id
          WHERE v.status = 'Menunggu Konfirmasi'
          ORDER BY v.created_at ASC";
$result = $conn->query($query);

$page_title = 'Konfirmasi Pendaftaran Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-check-double me-2"></i>Konfirmasi Pendaftaran</h2>
        </div>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Daftar</th>
                        <th>Pemilik</th>
                        <th>Hewan</th>
                        <th>Jadwal Vaksin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo formatTanggal(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pemilik']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_hewan']); ?></td>
                                <td>
                                    <?php echo formatTanggal($row['tanggal']); ?><br>
                                    <small><?php echo formatWaktu($row['waktu']); ?> - <?php echo htmlspecialchars($row['jenis_vaksin_jadwal']); ?></small>
                                </td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="confirm">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pendaftaran ini?')">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $row['id']; ?>">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                    
                                    <!-- Modal Tolak -->
                                    <div class="modal fade" id="rejectModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Pendaftaran</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <div class="mb-3">
                                                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="alasan" required rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada pendaftaran yang menunggu konfirmasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
