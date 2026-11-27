<?php
require_once '../config.php';
require_once '../koneksi.php';

checkRole(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) {
    header('Location: ' . APP_URL . '/admin/jadwal_dokter.php');
    exit;
}

$error = '';
$success = '';

// Ambil data jadwal
$stmt = $conn->prepare("SELECT * FROM jadwal_dokter WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$jadwal = $stmt->get_result()->fetch_assoc();

if (!$jadwal) {
    header('Location: ' . APP_URL . '/admin/jadwal_dokter.php');
    exit;
}

// Ambil daftar dokter
$dokter_list = $conn->query("SELECT id, nama, spesialis FROM dokter ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = sanitize($_POST['tanggal']);
    $jam_mulai = sanitize($_POST['jam_mulai']);
    $jam_selesai = sanitize($_POST['jam_selesai']);
    $max_slot = (int)$_POST['max_slot'];
    
    if (empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($max_slot)) {
        $error = 'Semua field wajib diisi!';
    } elseif ($jam_mulai >= $jam_selesai) {
        $error = 'Jam mulai harus lebih awal dari jam selesai!';
    } elseif ($max_slot < $jadwal['slot_terisi']) {
        $error = 'Maksimum slot tidak boleh lebih kecil dari slot yang sudah terisi (' . $jadwal['slot_terisi'] . ')!';
    } else {
        // Cek bentrok jadwal (exclude current schedule)
        $stmt = $conn->prepare("
            SELECT id FROM jadwal_dokter 
            WHERE dokter_id = ? 
            AND tanggal = ? 
            AND jam_selesai > ? 
            AND jam_mulai < ?
            AND id != ?
        ");
        
        $stmt->bind_param("isssi", $jadwal['dokter_id'], $tanggal, $jam_mulai, $jam_selesai, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Dokter ini sudah memiliki jadwal yang bentrok di waktu tersebut!';
        } else {
            $stmt = $conn->prepare("UPDATE jadwal_dokter SET tanggal = ?, jam_mulai = ?, jam_selesai = ?, max_slot = ? WHERE id = ?");
            $stmt->bind_param("sssii", $tanggal, $jam_mulai, $jam_selesai, $max_slot, $id);
            
            if ($stmt->execute()) {
                $success = 'Jadwal berhasil diperbarui!';
                // Refresh data
                $stmt = $conn->prepare("SELECT * FROM jadwal_dokter WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $jadwal = $stmt->get_result()->fetch_assoc();
                
                header('refresh:2;url=' . APP_URL . '/admin/jadwal_dokter.php');
            } else {
                $error = 'Gagal memperbarui jadwal!';
            }
        }
        $stmt->close();
    }
}

$page_title = 'Edit Jadwal Dokter';
include '../layouts/header_dashboard.php';
?>

<div class="wrapper">
    <?php include '../layouts/sidebar_admin.php'; ?>
    
    <div class="main-content">
        <div class="topbar">
            <h2><i class="fas fa-edit me-2"></i>Edit Jadwal Dokter</h2>
            <a href="<?php echo APP_URL; ?>/admin/jadwal_dokter.php" class="btn btn-secondary btn-custom">
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
                    <label class="form-label">Dokter</label>
                    <input type="text" class="form-control form-control-custom" value="<?php 
                        $d_stmt = $conn->prepare("SELECT nama FROM dokter WHERE id = ?");
                        $d_stmt->bind_param("i", $jadwal['dokter_id']);
                        $d_stmt->execute();
                        $d_res = $d_stmt->get_result()->fetch_assoc();
                        echo $d_res['nama'];
                    ?>" readonly disabled>
                    <small class="text-muted">Dokter tidak dapat diubah saat edit.</small>
                </div>
                
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" required value="<?php echo $jadwal['tanggal']; ?>">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="jam_mulai" name="jam_mulai" required value="<?php echo $jadwal['jam_mulai']; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control form-control-custom" id="jam_selesai" name="jam_selesai" required value="<?php echo $jadwal['jam_selesai']; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="max_slot" class="form-label">Maksimum Slot (Kuota) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control form-control-custom" id="max_slot" name="max_slot" required min="1" value="<?php echo $jadwal['max_slot']; ?>">
                    <small class="text-muted">Slot terisi saat ini: <?php echo $jadwal['slot_terisi']; ?></small>
                </div>
                
                <button type="submit" class="btn btn-primary-custom-solid btn-custom">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
