<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['pemilik']);

if (!isset($_GET['jadwal_id'])) {
    header('Location: daftar_jadwal_vaksin.php');
    exit;
}

$jadwal_id = (int)$_GET['jadwal_id'];

// Ambil data pemilik
$stmt = $conn->prepare("SELECT id FROM pemilik WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pemilik = $stmt->get_result()->fetch_assoc();
$pemilik_id = $pemilik['id'];

// Ambil detail jadwal
$stmt = $conn->prepare("SELECT jv.*, d.nama as nama_dokter FROM jadwal_vaksinasi jv LEFT JOIN dokter d ON jv.dokter_id = d.id WHERE jv.id = ?");
$stmt->bind_param("i", $jadwal_id);
$stmt->execute();
$jadwal = $stmt->get_result()->fetch_assoc();

if (!$jadwal) {
    echo "<script>alert('Jadwal tidak ditemukan!'); window.location.href='daftar_jadwal_vaksin.php';</script>";
    exit;
}

// Cek kuota
if (($jadwal['kuota'] - $jadwal['kuota_terpakai']) <= 0) {
    echo "<script>alert('Kuota sudah penuh!'); window.location.href='daftar_jadwal_vaksin.php';</script>";
    exit;
}

// Ambil daftar hewan pemilik
$stmt = $conn->prepare("SELECT id, nama_hewan, jenis FROM pasien WHERE id_pemilik = ?");
$stmt->bind_param("i", $pemilik_id);
$stmt->execute();
$hewan_list = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hewan_id = (int)$_POST['hewan_id'];
    
    if (empty($hewan_id)) {
        echo "<script>alert('Pilih hewan terlebih dahulu!');</script>";
    } else {
        // Cek apakah sudah terdaftar di jadwal ini
        $stmt = $conn->prepare("SELECT id FROM vaksinasi WHERE hewan_id = ? AND jadwal_id = ?");
        $stmt->bind_param("ii", $hewan_id, $jadwal_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo "<script>alert('Hewan ini sudah terdaftar di jadwal ini!');</script>";
        } else {
            // Daftar
            $stmt = $conn->prepare("INSERT INTO vaksinasi (hewan_id, jadwal_id, status) VALUES (?, ?, 'Menunggu Konfirmasi')");
            $stmt->bind_param("ii", $hewan_id, $jadwal_id);
            
            if ($stmt->execute()) {
                // Update kuota terpakai
                $conn->query("UPDATE jadwal_vaksinasi SET kuota_terpakai = kuota_terpakai + 1 WHERE id = $jadwal_id");
                
                echo "<script>alert('Pendaftaran berhasil! Silakan tunggu konfirmasi admin.'); window.location.href='riwayat_vaksinasi.php';</script>";
            } else {
                echo "<script>alert('Gagal mendaftar!');</script>";
            }
        }
    }
}

$page_title = 'Form Pendaftaran Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_pemilik.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-file-signature me-2"></i>Form Pendaftaran Vaksinasi</h2>
            <a href="daftar_jadwal_vaksin.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Detail Jadwal</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Tanggal</th>
                                <td>: <?php echo formatTanggal($jadwal['tanggal']); ?></td>
                            </tr>
                            <tr>
                                <th>Waktu</th>
                                <td>: <?php echo formatWaktu($jadwal['waktu']); ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Vaksin</th>
                                <td>: <?php echo htmlspecialchars($jadwal['jenis_vaksin']); ?></td>
                            </tr>
                            <tr>
                                <th>Dokter</th>
                                <td>: <?php echo $jadwal['nama_dokter'] ? htmlspecialchars($jadwal['nama_dokter']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <td>
                                    <div class="catatan-summernote">
                                        <?php echo $jadwal['catatan']; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                        <hr>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="hewan_id" class="form-label fw-bold">Pilih Hewan yang akan divaksin <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" name="hewan_id" required>
                                    <option value="">-- Pilih Hewan --</option>
                                    <?php if ($hewan_list->num_rows > 0): ?>
                                        <?php while ($h = $hewan_list->fetch_assoc()): ?>
                                            <option value="<?php echo $h['id']; ?>">
                                                <?php echo htmlspecialchars($h['nama_hewan']); ?> (<?php echo htmlspecialchars($h['jenis']); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Anda belum memiliki data hewan</option>
                                    <?php endif; ?>
                                </select>
                                <?php if ($hewan_list->num_rows == 0): ?>
                                    <div class="form-text text-danger mt-2">
                                        Anda belum mendaftarkan hewan. Silakan <a href="tambah_hewan.php">tambah hewan</a> terlebih dahulu.
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary-custom-solid btn-lg" <?php echo $hewan_list->num_rows == 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
