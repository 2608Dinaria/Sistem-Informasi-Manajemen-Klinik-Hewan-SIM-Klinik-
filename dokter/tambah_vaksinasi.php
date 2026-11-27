<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['dokter']);

$error = '';
$success = '';

// Ambil data dokter
$stmt = $conn->prepare("SELECT * FROM dokter WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$dokter = $stmt->get_result()->fetch_assoc();
$stmt->close();

$dokter_id = $dokter['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pasien_id = (int)$_POST['pasien_id'];
    $jenis_vaksin = sanitize($_POST['jenis_vaksin']);
    $tanggal_pemberian = sanitize($_POST['tanggal_pemberian']);
    $dosis = sanitize($_POST['dosis']);
    $jadwal_booster = !empty($_POST['jadwal_booster']) ? sanitize($_POST['jadwal_booster']) : null;
    $catatan = sanitize($_POST['catatan']);
    
    if (empty($pasien_id) || empty($jenis_vaksin) || empty($tanggal_pemberian)) {
        $error = 'Pasien, jenis vaksin, dan tanggal pemberian wajib diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO vaksinasi (pasien_id, dokter_id, jenis_vaksin, tanggal_pemberian, dosis, jadwal_booster, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $pasien_id, $dokter_id, $jenis_vaksin, $tanggal_pemberian, $dosis, $jadwal_booster, $catatan);
        
        if ($stmt->execute()) {
            $success = 'Data vaksinasi berhasil ditambahkan!';
            header('refresh:2;url=' . APP_URL . '/dokter/vaksinasi.php');
        } else {
            $error = 'Gagal menambahkan data vaksinasi!';
        }
        $stmt->close();
    }
}

// Ambil daftar pasien
$pasien_list = $conn->query("SELECT * FROM pasien ORDER BY nama_hewan");

$page_title = 'Tambah Vaksinasi';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-syringe me-2"></i>Tambah Vaksinasi</h2>
            <a href="<?php echo APP_URL; ?>/dokter/vaksinasi.php" class="btn btn-secondary btn-custom">
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
                <div class="mb-3">
                    <label for="pasien_id" class="form-label">Pasien <span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" id="pasien_id" name="pasien_id" required>
                        <option value="">Pilih Pasien</option>
                        <?php while ($p = $pasien_list->fetch_assoc()): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo $p['nama_hewan']; ?> (<?php echo $p['jenis']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="jenis_vaksin" class="form-label">Jenis Vaksin <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom" id="jenis_vaksin" name="jenis_vaksin" required placeholder="Contoh: Rabies, DHLPP">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pemberian" class="form-label">Tanggal Pemberian <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-custom" id="tanggal_pemberian" name="tanggal_pemberian" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="dosis" class="form-label">Dosis</label>
                        <input type="text" class="form-control form-control-custom" id="dosis" name="dosis" placeholder="Contoh: 1 ml">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="jadwal_booster" class="form-label">Jadwal Booster</label>
                    <input type="date" class="form-control form-control-custom" id="jadwal_booster" name="jadwal_booster">
                    <small class="text-muted">Tanggal untuk vaksinasi booster berikutnya</small>
                </div>
                
                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control form-control-custom" id="catatan" name="catatan" rows="2"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>