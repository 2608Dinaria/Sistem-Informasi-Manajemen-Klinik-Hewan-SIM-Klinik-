<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header('Location: ' . APP_URL . '/admin/jadwal_vaksinasi.php');
    exit;
}

$id = (int)$_GET['id'];

// Ambil data jadwal existing
$stmt = $conn->prepare("SELECT * FROM jadwal_vaksinasi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$jadwal = $stmt->get_result()->fetch_assoc();

if (!$jadwal) {
    echo "<script>alert('Jadwal tidak ditemukan!'); window.location.href='" . APP_URL . "/admin/jadwal_vaksinasi.php';</script>";
    exit;
}

// Ambil daftar dokter
$dokter_list = $conn->query("SELECT id, nama, spesialis FROM dokter ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = sanitize($_POST['tanggal']);
    $waktu = sanitize($_POST['waktu']);
    $jenis_vaksin = sanitize($_POST['jenis_vaksin']);
    $kuota = (int)$_POST['kuota'];
    $dokter_id = !empty($_POST['dokter_id']) ? (int)$_POST['dokter_id'] : NULL;
    $catatan = sanitize($_POST['catatan']);
    
    if (empty($tanggal) || empty($waktu) || empty($jenis_vaksin) || empty($kuota)) {
        $error = 'Field wajib diisi (Tanggal, Waktu, Jenis Vaksin, Kuota)!';
    } elseif ($kuota < $jadwal['kuota_terpakai']) {
        $error = 'Kuota tidak boleh lebih kecil dari jumlah yang sudah terpakai (' . $jadwal['kuota_terpakai'] . ')!';
    } else {
        $stmt = $conn->prepare("UPDATE jadwal_vaksinasi SET tanggal = ?, waktu = ?, jenis_vaksin = ?, kuota = ?, dokter_id = ?, catatan = ? WHERE id = ?");
        $stmt->bind_param("sssiisi", $tanggal, $waktu, $jenis_vaksin, $kuota, $dokter_id, $catatan, $id);
        
        if ($stmt->execute()) {
            $success = 'Jadwal vaksinasi berhasil diperbarui!';
            // Refresh data
            $stmt = $conn->prepare("SELECT * FROM jadwal_vaksinasi WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $jadwal = $stmt->get_result()->fetch_assoc();
            
            header('refresh:2;url=' . APP_URL . '/admin/jadwal_vaksinasi.php');
        } else {
            $error = 'Gagal memperbarui jadwal!';
        }
        $stmt->close();
    }
}

$page_title = 'Edit Jadwal Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-edit me-2"></i>Edit Jadwal Vaksinasi</h2>
            <a href="<?php echo APP_URL; ?>/admin/jadwal_vaksinasi.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="" data-validate="true">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" required value="<?php echo $jadwal['tanggal']; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="waktu" class="form-label">Waktu <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="waktu" name="waktu" required value="<?php echo $jadwal['waktu']; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="jenis_vaksin" class="form-label">Jenis Vaksin <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" id="jenis_vaksin" name="jenis_vaksin" required value="<?php echo htmlspecialchars($jadwal['jenis_vaksin']); ?>">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kuota" class="form-label">Kuota <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-custom" id="kuota" name="kuota" required min="<?php echo $jadwal['kuota_terpakai']; ?>" value="<?php echo $jadwal['kuota']; ?>">
                        <small class="text-muted">Minimal: <?php echo $jadwal['kuota_terpakai']; ?> (Sesuai jumlah pendaftar saat ini)</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="dokter_id" class="form-label">Dokter Penanggung Jawab (Optional)</label>
                        <select class="form-select form-select-custom" id="dokter_id" name="dokter_id">
                            <option value="">-- Pilih Dokter --</option>
                            <?php while ($d = $dokter_list->fetch_assoc()): ?>
                                <option value="<?php echo $d['id']; ?>" <?php echo ($jadwal['dokter_id'] == $d['id']) ? 'selected' : ''; ?>>
                                    <?php echo $d['nama']; ?> - <?php echo $d['spesialis']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Tambahan</label>
                    <textarea class="form-control form-control-custom" id="catatan" name="catatan" rows="3"><?php echo htmlspecialchars($jadwal['catatan']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
