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
    $tanggal = sanitize($_POST['tanggal']);
    $jam_mulai = sanitize($_POST['jam_mulai']);
    $jam_selesai = sanitize($_POST['jam_selesai']);
    $max_slot = (int)$_POST['max_slot'];
    
    if (empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($max_slot)) {
        $error = 'Semua field wajib diisi!';
    } elseif ($jam_mulai >= $jam_selesai) {
        $error = 'Jam mulai harus lebih kecil dari jam selesai!';
    } elseif ($max_slot < 1) {
        $error = 'Max slot minimal 1!';
    } else {
        // Cek apakah sudah ada jadwal di waktu yang sama
        $stmt = $conn->prepare("SELECT id FROM jadwal_dokter WHERE dokter_id = ? AND tanggal = ? AND jam_mulai = ?");
        $stmt->bind_param("iss", $dokter_id, $tanggal, $jam_mulai);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Jadwal pada waktu tersebut sudah ada!';
        } else {
            $status = 'aktif';
            $stmt = $conn->prepare("INSERT INTO jadwal_dokter (dokter_id, tanggal, jam_mulai, jam_selesai, max_slot, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssis", $dokter_id, $tanggal, $jam_mulai, $jam_selesai, $max_slot, $status);
            
            if ($stmt->execute()) {
                $success = 'Jadwal berhasil ditambahkan!';
                header('refresh:2;url=' . APP_URL . '/dokter/jadwal_saya.php');
            } else {
                $error = 'Gagal menambahkan jadwal!';
            }
        }
        $stmt->close();
    }
}

$page_title = 'Tambah Jadwal';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_dokter.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-calendar-plus me-2"></i>Tambah Jadwal</h2>
            <a href="<?php echo APP_URL; ?>/dokter/jadwal_saya.php" class="btn btn-secondary btn-custom">
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
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="jam_mulai" name="jam_mulai" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="jam_selesai" name="jam_selesai" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="max_slot" class="form-label">Max Slot <span class="text-danger">*</span></label>
                    <input type="number" class="form-control form-control-custom" id="max_slot" name="max_slot" required min="1" value="10">
                    <small class="text-muted">Jumlah maksimal appointment yang dapat diterima</small>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
</div>